<?php
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Hash for 'admin123': " . $hashed_password;
?>