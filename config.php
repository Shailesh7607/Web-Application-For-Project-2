<?php
// Prevent direct access to this file
if (basename($_SERVER['SCRIPT_FILENAME']) === 'config.php') {
    exit('This file cannot be accessed directly.');
}

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection settings
$host = 'localhost';
$dbname = 'canberra_momo_house';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to sanitize input
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to check if the logged-in user is an admin
function isAdmin() {
    return isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'admin';
}
?>