<?php
/**
 * Meta Conversions API (CAPI) - Updated Native PHP Implementation
 * Purpose: Server-side tracking to bypass ad-blockers and iOS restrictions.
 */

function sendMetaPurchase($email, $phone, $amount, $orderId) {
    // 1. Meta Configuration (Yahan apne asli credentials daalein)
    $access_token = 'YOUR_META_ACCESS_TOKEN'; 
    $pixel_id = 'YOUR_PIXEL_ID';
    $test_event_code = ''; // Agar Meta Events Manager mein test karna hai toh yahan 'TESTXXXXX' daalein

    // 2. Data Cleaning & Hashing (Meta Standard)
    // Email: Lowercase, trim, aur SHA256 hash
    $hashed_email = hash('sha256', strtolower(trim($email)));
    
    // Phone: Sirf numbers rakhein (no +, no spaces), aur SHA256 hash
    $clean_phone = preg_replace('/\D/', '', $phone); 
    $hashed_phone = hash('sha256', $clean_phone);

    // 3. User & Event Payload Structure
    $event_data = [
        'data' => [
            [
                'event_name' => 'Purchase',
                'event_time' => time(),
                'event_id' => $orderId, // IMPORTANT: Pixel ke same event_id se match hona chahiye
                'action_source' => 'website',
                'event_source_url' => 'http://localhost/astrologer/', // Apni live URL yahan daalein
                'user_data' => [
                    'em' => [$hashed_email],
                    'ph' => [$hashed_phone],
                    'client_ip_address' => ($_SERVER['REMOTE_ADDR'] == '::1') ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'],
                    'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0'
                ],
                'custom_data' => [
                    'currency' => 'INR',
                    'value' => (float)$amount
                ]
            ]
        ]
    ];

    // Agar testing kar rahe hain toh test code add karein
    if (!empty($test_event_code)) {
        $event_data['test_event_code'] = $test_event_code;
    }

    // 4. Meta API URL
    $url = "https://graph.facebook.com/v17.0/{$pixel_id}/events?access_token={$access_token}";

    // 5. cURL Request Execution
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($event_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // Localhost par SSL error bypass karne ke liye
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // 6. Response Handling
    if ($error) {
        error_log("Meta CAPI Error: " . $error);
        return ["success" => false, "error" => $error];
    }
    
    return json_decode($response, true);
}