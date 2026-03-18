<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

// 1. Sabse pehle variables extract karein
$amount_in_paise = $data['amount'] ?? null;
$slot_id = $data['slot_id'] ?? null;
$booking_date = $data['booking_date'] ?? null;

// 2. Required fields check
if(!$amount_in_paise || !$slot_id || !$booking_date) {
    echo json_encode([
        "success" => false, 
        "message" => "Amount, Slot ID, and Booking Date are required",
        "error" => "MISSING_FIELDS"
    ]);
    exit;
}

// 3. Date Validation (Future date check)
$today = date('Y-m-d');
if ($booking_date < $today) {
    echo json_encode([
        "success" => false,
        "message" => "Past dates are not allowed for booking",
        "error" => "INVALID_DATE"
    ]);
    exit;
}

try {
    // --- STEP 1: Slot availability check ---
    $check_sql = "SELECT id FROM bookings WHERE slot_id = ? AND booking_date = ? AND status = 'confirmed'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$slot_id, $booking_date]);

    if($check_stmt->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "This slot is already booked. Please choose another.",
            "error" => "SLOT_OCCUPIED"
        ]);
        exit;
    }

    // --- STEP 2: Mock Order ID Generate karna ---
    $mockOrderId = "order_mock_" . bin2hex(random_bytes(6));
    $amount_in_rupees = $amount_in_paise / 100; // Rs. 501.00

    // --- STEP 3: Database mein INSERT karna ---
    $sql = "INSERT INTO orders (order_id, amount, status) VALUES (:order_id, :amount, :status)";
    $stmt = $conn->prepare($sql);
    
    $status = "pending";
    $stmt->bindParam(':order_id', $mockOrderId);
    $stmt->bindParam(':amount', $amount_in_rupees);
    $stmt->bindParam(':status', $status);
    
    if($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "data" => [
                "order_id" => $mockOrderId,
                "amount" => $amount_in_rupees
            ],
            "message" => "Order created successfully"
        ]);
    }

} catch (\PDOException $e) {
    echo json_encode([
        "success" => false, 
        "message" => "Database Error", 
        "error" => $e->getMessage()
    ]);
}
?>