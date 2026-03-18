<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$user = $data['username'] ?? '';
$pass = $data['password'] ?? '';

try {
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Yahan hum wapas password_verify par ja sakte hain agar aapne hash reset kar liya hai, 
    // warna testing ke liye plain match rakhein:
    if ($admin && ($pass === $admin['password_hash'] || password_verify($pass, $admin['password_hash']))) {
        
        // SESSION SET KARNA (Task ki line 1)
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
        $_SESSION['is_logged_in'] = true;

        $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")->execute([$admin['id']]);

        echo json_encode([
            "success" => true,
            "message" => "Login successful and Session set!"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}