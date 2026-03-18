<?php
// Database configuration
$host = "localhost";
$db_name = "jyotish_db"; 
$username = "root";          
$password = "";        
    

try {
    // PDO Connection banana
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    
    // Error handling mode set karna
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Default fetch mode as Associative Array (taaki data asani se mile)
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Test karne ke liye (Sab sahi hone par ise comment out // kar dein)
    // echo "Database Connected Successfully!"; 
    
} catch(PDOException $e) {
    // Agar connection fail ho toh error dikhaye
    die("Connection failed: " . $e->getMessage());
}
?>