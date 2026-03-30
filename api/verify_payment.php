<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../config/db.php';

$key_secret = "twVLCP2dDZdNevbTaJHecShG"; // Correct secret

$data = json_decode(file_get_contents("php://input"), true);

$razorpay_order_id   = $data['razorpay_order_id'] ?? null;
$razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
$razorpay_signature  = $data['razorpay_signature'] ?? null;
$booking_date = $data['booking_date'] ?? '';
$slot_id = $data['slot_id'] ?? '';

if(!$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature){
    echo json_encode(["success"=>false,"message"=>"Missing payment info"]); exit;
}

// Verify signature
$generated_signature = hash_hmac('sha256', $razorpay_order_id."|".$razorpay_payment_id, $key_secret);
if($generated_signature !== $razorpay_signature){
    echo json_encode(["success"=>false,"message"=>"Invalid signature"]); exit;
}

$booking_ref = 'JS-'.strtoupper(substr(uniqid(),-6));

try {
    $conn->beginTransaction();

    // Prevent double booking
    $check_sql = "SELECT id FROM bookings WHERE slot_id=? AND booking_date=? AND status='confirmed' FOR UPDATE";
    $stmt = $conn->prepare($check_sql);
    $stmt->execute([$slot_id, $booking_date]);
    if($stmt->rowCount()>0) throw new Exception("Slot already booked.");

    // Insert booking
    $sql = "INSERT INTO bookings (booking_ref, full_name,email,phone,dob,tob,pob,booking_date,slot_id,status) VALUES (?,?,?,?,?,?,?,?,?, 'confirmed')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$booking_ref, $data['full_name'],$data['email'],$data['phone'],$data['dob']??'',$data['tob']??'',$data['pob']??'',$booking_date,$slot_id]);
    $new_booking_id = $conn->lastInsertId();

    // Insert payment
    $sql_pay = "INSERT INTO payments (booking_id, razorpay_order_id, razorpay_payment_id, amount, payment_status) VALUES (?,?,?,?, 'success')";
    $stmt = $conn->prepare($sql_pay);
    $stmt->execute([$new_booking_id, $razorpay_order_id, $razorpay_payment_id, $data['amount']??500]);

    $conn->commit();
    echo json_encode(["success"=>true,"message"=>"Payment verified!","booking_ref"=>$booking_ref]);
} catch(Exception $e){
    if($conn->inTransaction()) $conn->rollBack();
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
?>