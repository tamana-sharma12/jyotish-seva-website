<?php
// 1. Security Check (Middleware)
require_once 'middleware.php';
checkAdminSession(); // Bina login ke koi delete nahi kar payega
require_once '../config/db.php';

header('Content-Type: application/json');

// 2. Input Data lo (JSON format mein)
// Frontend se aayega: {"id": 5}
$data = json_decode(file_get_contents("php://input"));
$review_id = $data->id ?? null;

// 3. Validation
if (!$review_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Review ID is required to delete."]);
    exit;
}

try {
    // 4. Delete Query
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$review_id]);

    // Check karo ki sach mein kuch delete hua ya nahi
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Review deleted successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Review not found or already deleted."
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
}
?>