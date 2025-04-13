<?php
// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define application environment
define('APP_ENV', 'development'); // Change to 'production' when live

// Base path configuration
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/LABS/');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'labs');

// Beem SMS API configuration
define('BEEM_API_KEY', '1198b695035b285d');
define('BEEM_SECRET_KEY', 'NGQ4YzA0MTBmNzY1NDMwYzFjMDJiMDBkMzgwMjZiOTMxOTA2MmU1MWM0MzYzZDViMmM2N2M4OTYzMDI2NTExMw==');

// Include database connection
require_once BASE_PATH . '/db.php';

// Include functions
require_once BASE_PATH . '/functions.php';

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,        // 1 day
        'cookie_secure'   => false,        // Set to true in production with HTTPS
        'cookie_httponly' => true,
        'use_strict_mode' => true,
        'name'            => 'LawyerApptSessID'
    ]);
}

// Timezone configuration
date_default_timezone_set('UTC');
?>