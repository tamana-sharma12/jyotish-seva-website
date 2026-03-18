<?php
require_once 'middleware.php';
checkAdminSession();
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$review_id = $data['id'] ?? '';

if (!$review_id) {
    echo json_encode(["success" => false, "message" => "Review ID required"]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$review_id]);
    echo json_encode(["success" => true, "message" => "Review approved!"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}