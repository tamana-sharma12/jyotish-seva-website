<?php
require_once 'meta_capi_helper.php';

// Fake Data for Testing
$res = sendMetaPurchase('test@example.com', '919876543210', 501, 'BOOKING_12345');

echo "Response from Meta: <pre>";
print_r($res);
echo "</pre>";
?>