<?php
// Jab Meta account ban jaye, tab yahan asli ID aur Token dal dena
define('META_ACCESS_TOKEN', 'YOUR_ACTUAL_TOKEN_HERE'); 
define('META_PIXEL_ID', 'YOUR_ACTUAL_PIXEL_ID_HERE');

function sendPurchaseToMeta($email, $phone, $amount, $booking_ref) {
    // Agar Token nahi hai, toh function ko yahin rok do (Error nahi aayega)
    if (META_ACCESS_TOKEN === 'YOUR_ACTUAL_TOKEN_HERE') {
        return "Meta keys not set, skipping API call.";
    }

    $url = "https://graph.facebook.com/v17.0/" . META_PIXEL_ID . "/events?access_token=" . META_ACCESS_TOKEN;

    $data = [
        "data" => [
            [
                "event_name" => "Purchase",
                "event_time" => time(),
                "action_source" => "website",
                "event_id" => $booking_ref,
                "user_data" => [
                    "em" => [hash('sha256', strtolower(trim($email)))],
                    "ph" => [hash('sha256', strtolower(trim($phone)))],
                    "client_ip_address" => $_SERVER['REMOTE_ADDR'],
                    "client_user_agent" => $_SERVER['HTTP_USER_AGENT']
                ],
                "custom_data" => [
                    "value" => $amount,
                    "currency" => "INR"
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}