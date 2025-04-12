<?php
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(2, 1);       // PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    $pdo->setAttribute(19, 2);      // PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>