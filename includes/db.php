<?php
// Database connection configuration
$servername = "localhost";
$username = "root";     // Default XAMPP username
$password = "";         // Default XAMPP password (empty)
$dbname = "labs";       // Your database name

try {
    // Create connection using MySQLi
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for proper encoding
    $conn->set_charset("utf8mb4");
    
    // Uncomment to verify connection on load
    // echo "Database connected successfully";
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log("Database Error: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}
?>