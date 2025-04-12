<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');

// Base URL
define('BASE_URL', 'http://localhost/LABS');

// Database credentials - Updated for your XAMPP default setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'labs');  // Changed to your database name 'labs'

// Include database connection
require_once 'db.php';
require_once 'functions.php';
?>