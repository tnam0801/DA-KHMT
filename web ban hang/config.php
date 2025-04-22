<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'your_username'); // Replace with your database username
define('DB_PASSWORD', 'your_password'); // Replace with your database password
define('DB_NAME', 'salewn_db'); // Replace with your database name

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>