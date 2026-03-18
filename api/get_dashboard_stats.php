<?php
require_once 'middleware.php';
checkAdminSession();
require_once '../config/db.php';

try {
    // 1. Total Bookings
    $total_bookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

    // 2. Today's Bookings
    $today = date('Y-m-d');
    $stmt_today = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE booking_date = ?");
    $stmt_today->execute([$today]);
    $today_bookings = $stmt_today->fetchColumn();

    // 3. Pending Count (Jinka payment_status 'pending' hai)
    // Note: Aapke table mein column 'status' hai ya 'payment_status', use check kar lena.
    $pending_count = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();

    // 4. Total Revenue (Maan lijiye 1 booking 500/- ki hai)
    // Agar aapke pas 'amount' ka column hai toh SUM(amount) karein
    $revenue = $total_bookings * 500; 

    echo json_encode([
        "success" => true,
        "stats" => [
            "total_bookings" => (int)$total_bookings,
            "today_bookings" => (int)$today_bookings,
            "total_revenue" => $revenue,
            "pending_count" => (int)$pending_count
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}