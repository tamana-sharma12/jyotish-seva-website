<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once __DIR__ . '/../config/db.php';

$key_id = "rzp_test_SVNwp9h4rEXVhF";
$key_secret = "twVLCP2dDZdNevbTaJHecShG"; // Correct secret key

$data = json_decode(file_get_contents("php://input"), true);

$amount = $data['amount'] ?? 500;
$slot_id = $data['slot_id'] ?? null;
$booking_date = $data['booking_date'] ?? null;

if(!$amount || !$slot_id || !$booking_date){
    echo json_encode(["success"=>false,"message"=>"Missing fields"]); exit;
}

// Create Razorpay order
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_USERPWD, $key_id.":".$key_secret);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => $amount*100,
    'currency' => 'INR',
    'payment_capture' => 1
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if($err){ echo json_encode(["success"=>false,"message"=>$err]); exit; }

$order = json_decode($response,true);
if(!isset($order['id'])){ echo json_encode(["success"=>false,"message"=>"Order ID missing"]); exit; }

echo json_encode(["success"=>true,"data"=>["order_id"=>$order['id'], "amount"=>$amount], "message"=>"Order created successfully"]);
?>