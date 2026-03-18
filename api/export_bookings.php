<?php
require_once 'middleware.php';
checkAdminSession();
require_once '../config/db.php';

// Header set karein taaki browser ise download kare
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bookings_export_' . date('Y-m-d') . '.csv');

// Output stream kholna
$output = fopen('php://output', 'w');

// 1. CSV ki Pehli Line (Headers)
fputcsv($output, ['ID', 'Reference', 'Name', 'Phone', 'Date', 'Slot', 'Status', 'Booking Time']);

// 2. Database se data nikaalna
$stmt = $conn->query("SELECT id, booking_ref, full_name, phone, booking_date, slot_id, status, created_at FROM bookings");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row); // Har row ko CSV line mein badalna
}

fclose($output);
exit;