<?php
/**
 * functions.php - Core functions for the lawyer booking system
 */

// Database connection (already established in config.php)
global $pdo;

/**
 * Get all specializations from database
 */
function getSpecializations() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM specializations ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching specializations: " . $e->getMessage());
        return [];
    }
}

/**
 * Get lawyers for a specific specialization
 */
function getLawyersBySpecialization($specialization_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT l.*, u.first_name, u.last_name, u.email, u.phone 
            FROM lawyers l
            JOIN users u ON l.user_id = u.id
            WHERE l.specialization_id = ?
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute([$specialization_id]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching lawyers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get lawyer details by ID
 */
function getLawyerById($lawyer_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT l.*, u.first_name, u.last_name, s.name as specialization_name 
            FROM lawyers l
            JOIN users u ON l.user_id = u.id
            JOIN specializations s ON l.specialization_id = s.id
            WHERE l.id = ?
        ");
        $stmt->execute([$lawyer_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching lawyer: " . $e->getMessage());
        return false;
    }
}

/**
 * Gets lawyer availability for FullCalendar
 */
function getLawyerAvailability($lawyer_id) {
    global $pdo;
    
    try {
        // 1. Get lawyer's working hours
        $stmt = $pdo->prepare("SELECT available_days, start_time, end_time FROM lawyers WHERE id = ?");
        $stmt->execute([$lawyer_id]);
        $lawyer = $stmt->fetch();
        
        if (!$lawyer) {
            return [];
        }
        
        // 2. Get booked appointments (next 14 days)
        $stmt = $pdo->prepare("
            SELECT 
                appointment_date, 
                start_time, 
                end_time 
            FROM appointments 
            WHERE lawyer_id = ? 
            AND status NOT IN ('cancelled', 'rejected')
            AND appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 14 DAY)
        ");
        $stmt->execute([$lawyer_id]);
        $appointments = $stmt->fetchAll();
        
        // 3. Get manually marked unavailable slots
        $stmt = $pdo->prepare("
            SELECT 
                day_of_week, 
                start_time, 
                end_time 
            FROM time_slots 
            WHERE lawyer_id = ? 
            AND is_available = FALSE
        ");
        $stmt->execute([$lawyer_id]);
        $unavailable = $stmt->fetchAll();
        
        // 4. Prepare events array for FullCalendar
        $events = [];
        
        // Add unavailable slots (gray)
        foreach ($unavailable as $slot) {
            $events[] = [
                'groupId' => 'unavailable',
                'daysOfWeek' => [$slot['day_of_week']],
                'startTime' => $slot['start_time'],
                'endTime' => $slot['end_time'],
                'display' => 'background',
                'color' => '#e5e7eb',
                'overlap' => false
            ];
        }
        
        // Add booked appointments (light red)
        foreach ($appointments as $appt) {
            $events[] = [
                'start' => $appt['appointment_date'] . 'T' . $appt['start_time'],
                'end' => $appt['appointment_date'] . 'T' . $appt['end_time'],
                'display' => 'background',
                'color' => '#fee2e2',
                'overlap' => false,
                'extendedProps' => [
                    'status' => 'booked'
                ]
            ];
        }
        
        return $events;
        
    } catch (PDOException $e) {
        error_log("Error getting lawyer availability: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a new appointment
 */
function createAppointment($data) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO appointments (
                lawyer_id, 
                client_id, 
                specialization_id, 
                appointment_date, 
                start_time, 
                end_time, 
                timezone, 
                case_details,
                status,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', NOW())
        ");
        
        $stmt->execute([
            $data['lawyer_id'],
            $data['client_id'],
            $data['specialization_id'],
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time'],
            $data['timezone'],
            $data['case_details'] ?? null
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating appointment: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a new client
 */
function createClient($data) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO clients (
                first_name, 
                last_name, 
                email, 
                phone,
                created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone']
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error creating client: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle AJAX requests
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_lawyer_availability':
            if (isset($_POST['lawyer_id'])) {
                echo json_encode(getLawyerAvailability($_POST['lawyer_id']));
            } else {
                echo json_encode(['error' => 'Lawyer ID missing']);
            }
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
    
    exit;
}
?>