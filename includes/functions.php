<?php
// includes/functions.php

/**
 * Get total number of appointments
 */
function getTotalAppointments() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM appointments");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in getTotalAppointments: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get count of active lawyers
 */
function getActiveLawyersCount() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM lawyers WHERE is_active = 1");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in getActiveLawyersCount: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get total number of clients
 */
function getTotalClients() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM clients");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in getTotalClients: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get count of pending appointments
 */
function getPendingAppointmentsCount() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in getPendingAppointmentsCount: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get recent appointments
 */
function getRecentAppointments($limit = 5) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT a.*, c.first_name, c.last_name, l.user_id, u.first_name as lawyer_first_name, u.last_name as lawyer_last_name 
            FROM appointments a
            JOIN clients c ON a.client_id = c.id
            JOIN lawyers l ON a.lawyer_id = l.id
            JOIN users u ON l.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getRecentAppointments: " . $e->getMessage());
        return [];
    }
}
?>