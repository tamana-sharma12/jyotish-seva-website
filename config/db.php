<?php
// .env file ka raasta (Path)
$envPath = __DIR__ . '/../.env';

// Agar .env file exists karti hai toh use load karo
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Comments skip karo
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// .env se data uthao (Agar nahi mile toh default use karo)
$host     = $_ENV['DB_HOST'] ?? 'localhost';
$db_name  = $_ENV['DB_NAME'] ?? 'jyotish_db';
$username = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASS'] ?? '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Check karne ke liye:
    // echo "Connected using .env settings!"; 
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>