<?php
require_once 'middleware.php';
checkAdminSession();
require_once '../config/db.php';

try {
    // Bookings by Source (Google, Facebook, Direct)
    $source_sql = "SELECT utm_source, COUNT(*) as count FROM bookings GROUP BY utm_source";
    $sources = $conn->query($source_sql)->fetchAll(PDO::FETCH_ASSOC);

    // Daily Bookings (Last 7 days)
    $daily_sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                  FROM bookings 
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(created_at)";
    $daily = $conn->query($daily_sql)->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "analytics" => [
            "source_breakdown" => $sources,
            "daily_trend" => $daily
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}