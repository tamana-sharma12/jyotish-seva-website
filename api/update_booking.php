<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/db.php';

// Check karein ki user logged in hai (Admin Only)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$booking_id = $data['booking_id'] ?? null;
$new_status = $data['status'] ?? null; // 'confirmed', 'cancelled', 'completed'

if (!$booking_id || !$new_status) {
    echo json_encode(["success" => false, "message" => "Booking ID and Status are required"]);
    exit;
}

try {
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$new_status, $booking_id])) {
        echo json_encode([
            "success" => true, 
            "message" => "Booking status updated to $new_status"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database Error"]);
}