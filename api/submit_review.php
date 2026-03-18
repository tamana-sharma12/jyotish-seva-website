<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
require_once '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data['reviewer_name'] ?? '';
$rating  = $data['rating'] ?? 5;
$comment = $data['comment'] ?? '';

if (empty($name) || empty($comment)) {
    echo json_encode(["success" => false, "message" => "Name and comment are required"]);
    exit;
}

try {
    $sql = "INSERT INTO reviews (reviewer_name, rating, comment, is_approved) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $rating, $comment]);

    echo json_encode(["success" => true, "message" => "Review submitted! It will be visible after approval."]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>