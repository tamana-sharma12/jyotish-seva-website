<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/db.php';

try {
    // Sirf wahi reviews dikhao jo approved hain (Requirement 3.0)
    $sql = "SELECT rating, reviewer_name, comment, created_at 
            FROM reviews 
            WHERE is_approved = 1 
            ORDER BY created_at DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $reviews
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Database Error: " . $e->getMessage() 
    ]);
}
?>