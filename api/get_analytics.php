<?php
// 1. Security & Header Setup
require_once 'middleware.php';
checkAdminSession(); 
require_once '../config/db.php';

header('Content-Type: application/json');

// 2. Capture Date Range Filters (Task Requirement: ?from=&to=)
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to'] ?? date('Y-m-d');

try {
    // 3. Source Breakdown (e.g., Website, Referrals, Social Media)
    $source_stmt = $conn->prepare("SELECT source, COUNT(*) as count FROM bookings WHERE booking_date BETWEEN ? AND ? GROUP BY source");
    $source_stmt->execute([$from, $to]);
    $source_breakdown = $source_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Campaign Breakdown (e.g., FB Ads, Google Ads)
    $campaign_stmt = $conn->prepare("SELECT utm_campaign as campaign, COUNT(*) as count FROM bookings WHERE booking_date BETWEEN ? AND ? AND utm_campaign IS NOT NULL GROUP BY utm_campaign");
    $campaign_stmt->execute([$from, $to]);
    $campaign_breakdown = $campaign_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Daily Bookings (For Charts)
    $daily_stmt = $conn->prepare("SELECT booking_date as date, COUNT(*) as count FROM bookings WHERE booking_date BETWEEN ? AND ? GROUP BY booking_date ORDER BY booking_date ASC");
    $daily_stmt->execute([$from, $to]);
    $daily_bookings = $daily_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Return Analytics Data
    echo json_encode([
        "success" => true,
        "range" => ["from" => $from, "to" => $to],
        "data" => [
            "source_breakdown"   => $source_breakdown,
            "campaign_breakdown" => $campaign_breakdown,
            "daily_bookings"     => $daily_bookings
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
}
?>