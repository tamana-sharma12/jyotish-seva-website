<?php
// 1. Security & Header Setup
require_once 'middleware.php';
checkAdminSession(); 
require_once '../config/db.php';

// 2. Capture Date Range Filters (As per Task Requirement)
$from = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to   = $_GET['to'] ?? date('Y-m-d');

// 3. Set Headers for CSV Download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bookings_report_' . $from . '_to_' . $to . '.csv');

// 4. Open PHP Output Stream
$output = fopen('php://output', 'w');

// 5. CSV Headers (Professional Columns)
fputcsv($output, ['ID', 'Ref', 'Client Name', 'Phone', 'Date', 'Status', 'Payment', 'Amount', 'Notes', 'Created At']);

try {
    // 6. Fetch Data from DB (Using the new column names)
    $stmt = $conn->prepare("SELECT id, booking_ref, full_name, phone, booking_date, status, payment_status, amount_paid, notes, created_at 
                            FROM bookings 
                            WHERE booking_date BETWEEN ? AND ? 
                            ORDER BY booking_date DESC");
    $stmt->execute([$from, $to]);

    // 7. Write Rows to CSV
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Export Error: " . $e->getMessage());
}
?>