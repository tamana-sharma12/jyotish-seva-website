<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

// --- CUSTOM .ENV LOADER ---
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

loadEnv(__DIR__ . '/../.env');

function sendBookingEmail($toEmail, $userName, $bookingDetails) {
    $mail = new PHPMailer(true);
    try {
        // --- STEP 1: DEBUGGING ON (Postman mein error dikhega) ---
        $mail->SMTPDebug = 2; 
        
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->Username   = $_ENV['SMTP_USER'];
        $mail->Password   = $_ENV['SMTP_PASS'];
        $mail->Port       = 2525; // Mailtrap ke liye 2525 best hai
        $mail->SMTPAuth   = true;

        // --- STEP 2: MAILTRAP SETTINGS (Inhe aise hi rehne dein) ---
        $mail->SMTPSecure = false; 
        $mail->SMTPAutoTLS = false;

        $mail->setFrom('confirm@jyotish.com', 'Jyotish Seva');
        $mail->addAddress($toEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = "Booking Confirmed - Ref: " . $bookingDetails['booking_ref'];
        $mail->Body    = "<h2>Pranam {$userName}!</h2><p>Aapki booking ({$bookingDetails['booking_ref']}) confirm ho gayi hai.</p>";
        $mail->AltBody = "Booking Confirmed: " . $bookingDetails['booking_ref'];

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Agar error aaye toh wo log mein chala jaye
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>