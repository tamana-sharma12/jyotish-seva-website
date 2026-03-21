<?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// db.php mein PDO options aur error logging pehle hi set hai
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$user_input = $data['username'] ?? '';
$pass_input = $data['password'] ?? '';

if (empty($user_input) || empty($pass_input)) {
    http_response_code(400); // Task B9: Bad Request
    echo json_encode(["success" => false, "message" => "Username and Password are required"]);
    exit;
}

try {
    // 1. Prepared Statement (SQL Injection Protection)
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
    $stmt->execute([$user_input]);
    $admin = $stmt->fetch();

    // 2. Secure Password Verification (Task B9 Requirement)
    // Humne plain text check (===) hata diya hai
    if ($admin && password_verify($pass_input, $admin['password_hash'])) {
        
        // Session Regeneration (Session Hijacking se bachne ke liye)
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        // Task B9: XSS Protection for stored data
        $_SESSION['admin_user'] = htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8');
        $_SESSION['is_logged_in'] = true;

        // Last login update
        $updateStmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$admin['id']]);

        http_response_code(200); // Success
        echo json_encode([
            "success" => true,
            "message" => "Login successful!",
            "admin" => [
                "id" => $admin['id'],
                "username" => $_SESSION['admin_user']
            ]
        ]);
    } else {
        // Security Tip: Username galat ho ya password, message hamesha generic rakho
        http_response_code(401); // Task B9: Unauthorized
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    // Task B9: Error Logging (Logs mein likho, user ko mat dikhao)
    error_log("Login Error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "An internal error occurred"]);
}
?>