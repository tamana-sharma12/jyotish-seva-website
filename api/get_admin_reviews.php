<?php
// 1. Security Check (Middleware)
require_once 'middleware.php';
checkAdminSession(); 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once '../config/db.php';

try {
    // 2. Query (is_approved flag ke saath)
    $sql = "SELECT id, reviewer_name, rating, comment, is_approved, created_at 
            FROM reviews 
            ORDER BY created_at DESC";
            
    $stmt = $conn->query($sql);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true, 
        "count" => count($reviews),
        "data" => $reviews
    ]);
} catch (PDOException $e) {
    // Error message mein actual error dikhana debugging ke liye accha hota hai
    echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
}
?>