<?php
// 1. Security & Header Setup
require_once 'middleware.php';
checkAdminSession(); // Validate admin access
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    // 2. Fetch Total Bookings Count
    $total_bookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

    // 3. Fetch Today's Bookings Count
    $today = date('Y-m-d');
    $stmt_today = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE DATE(created_at) = ?");
    $stmt_today->execute([$today]);
    $today_bookings = $stmt_today->fetchColumn();

    // 4. Fetch Pending Bookings Count (Task Requirement)
    $pending_count = $conn->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();

    // 5. Calculate Total Revenue
    // Update: 'amount' ko badal kar 'amount_paid' kar diya hai jo humne abhi DB mein add kiya
    $revenue_stmt = $conn->query("SELECT SUM(amount_paid) FROM bookings WHERE status != 'cancelled'");
    $total_revenue = $revenue_stmt->fetchColumn() ?? 0;

    // 6. Return Data in JSON Format
    echo json_encode([
        "success" => true,
        "stats" => [
            "total_bookings" => (int)$total_bookings,
            "today_bookings" => (int)$today_bookings,
            "total_revenue"  => (float)$total_revenue, // Ab sahi column se data aayega
            "pending_count"  => (int)$pending_count
        ]
    ]);

} catch (PDOException $e) {
    // Error Handling
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Database Error: " . $e->getMessage()
    ]);
}
?>