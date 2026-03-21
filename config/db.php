<?php
// .env file ka raasta (Path)
$envPath = __DIR__ . '/../.env';

// .env Loader logic (Same as yours)
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue; 
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

$host     = $_ENV['DB_HOST'] ?? 'localhost';
$db_name  = $_ENV['DB_NAME'] ?? 'jyotish_db';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

// --- TASK B9: SECURE PDO OPTIONS ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  // SQL Injection PROTECTION (OFF emulation)
];

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, $options);
} catch(PDOException $e) {
    // --- TASK B9: ERROR LOGGING (User ko raw error mat dikhao) ---
    error_log("DB Connection Error: " . $e->getMessage()); 
    http_response_code(500); // Sahi Status Code
    die(json_encode(["success" => false, "message" => "Internal Server Error"]));
}

// Meta details (Same as yours)
if(!defined('META_ACCESS_TOKEN')) define('META_ACCESS_TOKEN', $_ENV['META_ACCESS_TOKEN'] ?? '');
if(!defined('META_PIXEL_ID')) define('META_PIXEL_ID', $_ENV['META_PIXEL_ID'] ?? '');
?>