<?php
// Database connection ko connect karein
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form se aane wala data
    $user_id = $_POST['user_id']; // Abhi ke liye hum dummy ID bhejenge
    $astrologer_name = $_POST['astrologer_name'];
    $booking_date = $_POST['booking_date'];

    try {
        // Appointments table mein data insert karne ki query
        $sql = "INSERT INTO appointments (user_id, astrologer_name, booking_date, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $astrologer_name, $booking_date]);

        echo json_encode(["status" => "success", "message" => "Appointment booked!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Request"]);
}
?>