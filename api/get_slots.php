<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../config/db.php';

$date = $_GET['date'] ?? null;

if (!$date) {
    echo json_encode(["success" => false, "message" => "Date is required"]);
    exit;
}

try {
    // 1. Check karein ki mangi gayi date aaj se purani toh nahi
    $today = date('Y-m-d');
    $is_past_date = ($date < $today);

    // 2. Sabhi active slots ki list lena
    $sql_slots = "SELECT id, slot_label FROM time_slots WHERE is_active = 1 ORDER BY sort_order ASC";
    $stmt_slots = $conn->query($sql_slots);
    $all_slots = $stmt_slots->fetchAll(PDO::FETCH_ASSOC);

    // 3. Us date par jo slots pehle se 'confirmed' hain unhe nikalna
    $sql_booked = "SELECT slot_id FROM bookings WHERE booking_date = ? AND status = 'confirmed'";
    $stmt_booked = $conn->prepare($sql_booked);
    $stmt_booked->execute([$date]);
    $booked_slots = $stmt_booked->fetchAll(PDO::FETCH_COLUMN);

    $final_slots = [];
    foreach ($all_slots as $slot) {
        $final_slots[] = [
            "id" => (int)$slot['id'],
            "label" => $slot['slot_label'],
            // Logic: Agar purani date hai toh hamesha false, warna check availability
            "available" => $is_past_date ? false : !in_array($slot['id'], $booked_slots)
        ];
    }

    echo json_encode([
        "success" => true, 
        "date" => $date, 
        "is_past_date" => $is_past_date,
        "data" => $final_slots
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Server Error", "error" => $e->getMessage()]);
}