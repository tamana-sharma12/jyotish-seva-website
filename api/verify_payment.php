<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/db.php';

// Razorpay Key Secret (Ise .env ya config se aana chahiye)
$key_secret = "YOUR_RAZORPAY_KEY_SECRET"; 

$data = json_decode(file_get_contents("php://input"), true);

// 1. Data receiving logic
$razorpay_order_id   = $data['razorpay_order_id'] ?? null;
$razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
$razorpay_signature  = $data['razorpay_signature'] ?? null;

$booking_date = $data['booking_date'] ?? '';
$slot_id      = $data['slot_id'] ?? '';

// 2. Signature Verification (TASK B5: Requirement 1.2)
// Logic: HMAC SHA256(order_id + "|" + payment_id, secret)
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $key_secret);

if ($generated_signature !== $razorpay_signature) {
    echo json_encode(["success" => false, "message" => "Payment verification failed!", "error" => "INVALID_SIGNATURE"]);
    exit;
}

// 3. Unique Ref Generate (Requirement 5.1)
$booking_ref = 'JS-' . strtoupper(substr(uniqid(), -6));

try {
    $conn->beginTransaction();

    // STEP 1: Race Condition Prevention (TASK B4)
    // Phirse check karein ki payment verify hote-hote kisi aur ne toh book nahi kar liya
    $check_sql = "SELECT id FROM bookings WHERE slot_id = ? AND booking_date = ? AND status = 'confirmed' FOR UPDATE";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$slot_id, $booking_date]);

    if ($check_stmt->rowCount() > 0) {
        throw new Exception("Slot already occupied during payment processing.");
    }

    // STEP 2: Bookings Table Entry
    $sql_book = "INSERT INTO bookings (booking_ref, full_name, email, phone, dob, tob, pob, booking_date, slot_id, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')";
    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->execute([
        $booking_ref, $data['full_name'], $data['email'], $data['phone'], 
        $data['dob'], $data['tob'], $data['pob'], $booking_date, $slot_id
    ]);
    
    $new_booking_id = $conn->lastInsertId();

    // STEP 3: Payments Table Entry
    $sql_pay = "INSERT INTO payments (booking_id, razorpay_order_id, razorpay_payment_id, amount, payment_status) 
                VALUES (?, ?, ?, ?, 'success')";
    $stmt_pay = $conn->prepare($sql_pay);
    $stmt_pay->execute([$new_booking_id, $razorpay_order_id, $razorpay_payment_id, $data['amount'] ?? 501]);

    // STEP 4: Orders Table Update
    $sql_order = "UPDATE orders SET status = 'paid' WHERE order_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute([$razorpay_order_id]);

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Payment verified and Booking confirmed!",
        "booking_ref" => $booking_ref
    ]);

} catch (Exception $e) {
    if($conn->inTransaction()) { $conn->rollBack(); }
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}