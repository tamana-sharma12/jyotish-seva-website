<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Path ko absolute banane ke liye __DIR__ use karein
require_once __DIR__ . '/../config/db.php';

try {
    // Logic: Woh dates dhoondo jahan confirmed bookings slots ke barabar hain
    $query = "SELECT booking_date FROM bookings 
              WHERE status = 'confirmed' 
              GROUP BY booking_date 
              HAVING COUNT(DISTINCT slot_id) >= (SELECT COUNT(*) FROM time_slots WHERE is_active = 1)";
    
    // Check karein ki $conn sahi se connect hua hai
    if (!isset($conn)) {
        throw new \Exception("Database connection missing.");
    }

    $stmt = $conn->query($query);
    // \PDO use kiya hai red lines hatane ke liye
    $booked_dates = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    echo json_encode([
        "success" => true, 
        "booked_dates" => $booked_dates 
    ]);

} catch (\PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database Error: " . $e->getMessage()]);
} catch (\Exception $e) {
    echo json_encode(["success" => false, "error" => "Server Error: " . $e->getMessage()]);
}
?>