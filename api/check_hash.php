<?php
// Yeh check karne ke liye ki kya hash sahi hai
$password = 'testpass';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2...';

if (password_verify($password, $hash)) {
    echo "Hash is VALID!";
} else {
    echo "Hash is INVALID!";
}
?>