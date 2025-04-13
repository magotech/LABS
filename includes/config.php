<?php
// includes/config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'labs'); // Your database name
define('DB_USER', 'root');           // Your database username
define('DB_PASS', '');               // Your database password

// App settings
define('APP_NAME', 'Lawyer Booking System');
define('BASE_URL', 'http://localhost/LABS/'); // Adjust to your project path

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>