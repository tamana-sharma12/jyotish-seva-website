<?php
// Headers: Dusre domain (frontend) se request allow karne ke liye
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Database connection file include karein
require_once '../config/db.php';

try {
    // Task B3: Naye columns ke saath query
    $query = "SELECT name, tagline, bio, photo_url, experience_years, specialties, phone, email 
              FROM astrologer_info 
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $result = $stmt->fetch();

    if($result) {
        // Professional Response Format
        echo json_encode([
            "success" => true,
            "data" => $result,
            "message" => "Astrologer profile fetched successfully",
            "error" => ""
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "data" => null,
            "message" => "No profile data found in database",
            "error" => "DATA_NOT_FOUND"
        ]);
    }

} catch(PDOException $e) {
    // Error ko server logs mein save karein
    error_log("Database Error: " . $e->getMessage());
    
    // User ko clean error message dein
    echo json_encode([
        "success" => false,
        "data" => null,
        "message" => "Server error occurred while fetching data",
        "error" => "DB_CONNECTION_ERROR"
    ]);
}
?>