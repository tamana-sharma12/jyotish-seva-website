<?php
require_once 'middleware.php';
checkAdminSession();
require_once '../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "Booking ID required"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        echo json_encode(["success" => true, "data" => $booking]);
    } else {
        echo json_encode(["success" => false, "message" => "Booking not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}