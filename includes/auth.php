<?php
// includes/auth.php
function isAdminLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function adminAuthCheck() {
    if (!isAdminLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}
?>