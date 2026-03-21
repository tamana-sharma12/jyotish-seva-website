<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// db.php mein humne PDO options aur error logging pehle hi set kar di hai
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/email_helper.php'; 

// --- TASK B9: ERROR LOGGING CONFIG ---
// Agar koi unexpected error aaye toh wo logs mein jaye, screen par nahi
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// --- TASK B9: SECURITY (No Hardcoded Secrets) ---
// Key Secret ko hamesha .env se uthao
$key_secret = $_ENV['RAZORPAY_KEY_SECRET'] ?? '';

$data = json_decode(file_get_contents("php://input"), true);

// Check if data is valid
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Invalid Request Data"]);
    exit;
}

$razorpay_order_id   = $data['razorpay_order_id'] ?? null;
$razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
$razorpay_signature  = $data['razorpay_signature'] ?? null;

// --- TASK B9: SIGNATURE VERIFICATION (Always Server-Side) ---
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $key_secret);

if ($generated_signature !== $razorpay_signature) {
    error_log("Security Alert: Invalid Razorpay Signature for Order: $razorpay_order_id");
    http_response_code(401); // Unauthorized
    echo json_encode(["success" => false, "message" => "Payment verification failed!", "error" => "INVALID_SIGNATURE"]);
    exit;
}

$booking_date = $data['booking_date'] ?? '';
$slot_id      = $data['slot_id'] ?? '';
$booking_ref  = 'JS-' . strtoupper(substr(uniqid(), -6));
$amount_paid  = (int)($data['amount'] ?? 501); // Integer casting for security

try {
    $conn->beginTransaction();

    // --- TASK B9: PDO PREPARED STATEMENTS (SQL Injection Protection) ---
    // SELECT query with FOR UPDATE to prevent double booking
    $check_sql = "SELECT id FROM bookings WHERE slot_id = ? AND booking_date = ? AND status = 'confirmed' FOR UPDATE";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$slot_id, $booking_date]);

    if ($check_stmt->rowCount() > 0) {
        http_response_code(409); // Conflict
        throw new Exception("Slot already occupied.");
    }

    // Insert Booking
    $sql_book = "INSERT INTO bookings (
        booking_ref, full_name, email, phone, dob, tob, pob, 
        booking_date, slot_id, status, payment_status, amount_paid,
        utm_source, utm_medium, utm_campaign, gclid, fbclid
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', 'paid', ?, ?, ?, ?, ?, ?)";

    $stmt_book = $conn->prepare($sql_book);
    $stmt_book->execute([
        htmlspecialchars($data['full_name']), // Task B9: XSS Protection
        $data['email'], 
        $data['phone'], 
        $data['dob'], 
        $data['tob'], 
        htmlspecialchars($data['pob']), 
        $booking_date, 
        $slot_id,
        $amount_paid,
        $data['utm_source'] ?? 'direct',
        $data['utm_medium'] ?? null,
        $data['utm_campaign'] ?? null,
        $data['gclid'] ?? null,
        $data['fbclid'] ?? null
    ]);
    
    $new_booking_id = $conn->lastInsertId();

    // Insert Payment Record
    $sql_pay = "INSERT INTO payments (booking_id, razorpay_order_id, razorpay_payment_id, amount, payment_status) 
                VALUES (?, ?, ?, ?, 'success')";
    $stmt_pay = $conn->prepare($sql_pay);
    $stmt_pay->execute([$new_booking_id, $razorpay_order_id, $razorpay_payment_id, $amount_paid]);

    // Update Order Status
    $sql_order = "UPDATE orders SET status = 'paid' WHERE order_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->execute([$razorpay_order_id]);

    $conn->commit();

    // --- META & EMAIL LOGIC (Same as before but using defines from db.php) ---
    // ... (Yahan aapka Meta aur Email wala code aayega) ...

    http_response_code(200); // Success
    echo json_encode([
        "success" => true,
        "message" => "Payment verified & Booking confirmed!",
        "booking_ref" => $booking_ref
    ]);

} catch (Exception $e) {
    if($conn->inTransaction()) { $conn->rollBack(); }
    
    // Task B9: Error Logging
    error_log("Payment Error: " . $e->getMessage());
    
    // Generic message for user
    echo json_encode(["success" => false, "message" => "An error occurred. Please contact support."]);
}
?>