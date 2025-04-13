<?php
// includes/functions.php

/**
 * Get database connection
 */
function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        require_once 'config.php';
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

/**
 * Get total appointments count
 */
function getTotalAppointments() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM appointments");
    return $stmt->fetchColumn();
}

/**
 * Get active lawyers count
 */
function getActiveLawyersCount() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM lawyers");
    return $stmt->fetchColumn();
}

/**
 * Get pending appointments count
 */
function getPendingAppointmentsCount() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
    return $stmt->fetchColumn();
}

/**
 * Get total clients count
 */
function getTotalClients() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM clients");
    return $stmt->fetchColumn();
}

/**
 * Get recent appointments with lawyer and client details
 */
function getRecentAppointments($limit = 10, $whereClause = '', $params = []) {
    $pdo = getDBConnection();
    
    $query = "SELECT 
                a.id, 
                a.appointment_date, 
                a.start_time, 
                a.end_time, 
                a.status,
                c.first_name as client_first_name, 
                c.last_name as client_last_name,
                u.first_name as lawyer_first_name, 
                u.last_name as lawyer_last_name,
                s.name as specialization
              FROM appointments a
              JOIN clients c ON a.client_id = c.id
              JOIN lawyers l ON a.lawyer_id = l.id
              JOIN users u ON l.user_id = u.id
              JOIN specializations s ON a.specialization_id = s.id
              $whereClause
              ORDER BY a.appointment_date DESC, a.start_time DESC
              LIMIT $limit";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get available time slots for a lawyer
 */
function getAvailableTimeSlots($lawyerId, $date) {
    $pdo = getDBConnection();
    $dayOfWeek = date('N', strtotime($date)); // 1 (Monday) through 7 (Sunday)
    
    $query = "SELECT * FROM time_slots 
              WHERE lawyer_id = ? 
              AND day_of_week = ?
              AND is_available = 1
              ORDER BY start_time";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$lawyerId, $dayOfWeek]);
    return $stmt->fetchAll();
}

/**
 * Get lawyer details by ID
 */
function getLawyerById($lawyerId) {
    $pdo = getDBConnection();
    $query = "SELECT l.*, u.first_name, u.last_name, u.email, u.phone, s.name as specialization
              FROM lawyers l
              JOIN users u ON l.user_id = u.id
              JOIN specializations s ON l.specialization_id = s.id
              WHERE l.id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$lawyerId]);
    return $stmt->fetch();
}

/**
 * Get all specializations
 */
function getSpecializations() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM specializations ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Format date and time for display
 */
function formatDateTime($dateString, $timeString = null) {
    if (empty($dateString)) {
        return 'N/A';
    }
    
    $timestamp = strtotime($dateString . ($timeString ? ' ' . $timeString : ''));
    return $timestamp ? date('M j, Y g:i A', $timestamp) : 'N/A';
}

/**
 * Get status badge HTML with proper null handling
 */
function getStatusBadge(?string $status = null): string {
    // Handle null or empty status
    if ($status === null || $status === '') {
        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">N/A</span>';
    }

    $statusLower = strtolower($status);
    $statusDisplay = ucfirst($statusLower);

    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'completed' => 'bg-blue-100 text-blue-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'default' => 'bg-gray-100 text-gray-800'
    ];
    
    $class = $statusClasses[$statusLower] ?? $statusClasses['default'];
    
    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $class . '">' 
           . htmlspecialchars($statusDisplay) . '</span>';
}

/**
 * Check if user is admin
 */
function isAdmin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Check if user is lawyer
 */
function isLawyer(): bool {
    return ($_SESSION['role'] ?? '') === 'lawyer';
}

/**
 * Get user full name by ID
 */
function getUserFullName($userId): string {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    return $result['full_name'] ?? 'Unknown User';
}

/**
 * Redirect with flash message
 */
function redirect(string $url, ?string $type = null, ?string $message = null): void {
    if ($type && $message) {
        $_SESSION[$type] = $message;
    }
    header("Location: $url");
    exit;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data): string {
    return htmlspecialchars(strip_tags(trim($data ?? '')));
}


function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: no-reply@lawfirm.com' . "\r\n";
    
    // In a real application, use a proper email library like PHPMailer
    return mail($to, $subject, $message, $headers);
}

function sendSMS($phone, $message) {
    // Beem API integration
    $api_key = 'YOUR_BEEM_API_KEY';
    $secret_key = 'YOUR_BEEM_SECRET_KEY';
    
    $url = 'https://apisms.beem.africa/v1/send';
    
    $data = [
        'source_addr' => 'LAWFIRM',
        'encoding' => 0,
        'message' => $message,
        'recipients' => [
            ['recipient_id' => 1, 'dest_addr' => $phone]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$secret_key");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    // In production, you would actually execute this
    // $response = curl_exec($ch);
    // curl_close($ch);
    
    // For demo purposes, we'll just log to a file
    file_put_contents('../sms_log.txt', date('Y-m-d H:i:s') . " - To: $phone\nMessage: $message\n\n", FILE_APPEND);
    
    return true;
}


?>





<?php
/**
 * Sanitize user input
 */
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $conn->real_escape_string($data);
    }
}

/**
 * Send email notification
 */
if (!function_exists('sendEmail')) {
    function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@lawfirm.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        try {
            $sent = mail($to, $subject, $message, $headers);
            if (!$sent) {
                error_log("Email sending failed to: $to");
                return false;
            }
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Send SMS via Beem API
 */
if (!function_exists('sendSMS')) {
    function sendSMS($phone, $message) {
        // Skip SMS in development mode
        if (APP_ENV === 'development') {
            file_put_contents(
                BASE_PATH . '/../sms_log.txt',
                date('[Y-m-d H:i:s]') . " SMS to $phone: $message\n",
                FILE_APPEND
            );
            return true;
        }

        // Production SMS sending
        $url = 'https://apisms.beem.africa/v1/send';
        $data = [
            'source_addr' => 'LAWFIRM',
            'encoding' => 0,
            'message' => $message,
            'recipients' => [
                ['recipient_id' => 1, 'dest_addr' => $phone]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => BEEM_API_KEY . ':' . BEEM_SECRET_KEY,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10
        ]);

        try {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode !== 200) {
                error_log("Beem API Error: HTTP $httpCode - " . $response);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("SMS Error: " . $e->getMessage());
            return false;
        } finally {
            curl_close($ch);
        }
    }
}

/**
 * Generate appointment confirmation email content
 */
if (!function_exists('generateEmailContent')) {
    function generateEmailContent($client_name, $lawyer_name, $date, $time, $case_details = null) {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Appointment Confirmed</h1>
        </div>
        <div class="content">
            <p>Dear $client_name,</p>
            <p>Your appointment with $lawyer_name has been successfully booked.</p>
            
            <h3>Appointment Details</h3>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Time:</strong> $time</p>
HTML;

        if ($case_details) {
            $html .= "<p><strong>Your Notes:</strong> $case_details</p>";
        }

        $html .= <<<HTML
            <p>Please arrive 10 minutes before your scheduled time.</p>
            <p>If you need to reschedule or cancel, please contact us at least 24 hours in advance.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>The Legal Team</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
?>