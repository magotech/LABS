<?php
require_once __DIR__ . '/../includes/config.php';

// Verify required data exists
if (!isset($_POST['first_name']) || !isset($_SESSION['booking'])) {
    header("Location: step1.php");
    exit();
}

// Sanitize all inputs
$client_data = [
    'first_name' => sanitizeInput($_POST['first_name']),
    'last_name'  => sanitizeInput($_POST['last_name']),
    'email'      => sanitizeInput($_POST['email']),
    'phone'      => sanitizeInput($_POST['phone'])
];

$appointment_data = [
    'lawyer_id' => (int)$_SESSION['booking']['lawyer_id'],
    'specialization_id' => (int)$_SESSION['booking']['specialization_id'],
    'appointment_date' => sanitizeInput($_SESSION['booking']['appointment_date']),
    'start_time' => sanitizeInput($_SESSION['booking']['start_time']),
    'end_time' => sanitizeInput($_SESSION['booking']['end_time']),
    'case_details' => isset($_POST['case_details']) ? sanitizeInput($_POST['case_details']) : null
];

// Save client to database
$stmt = $conn->prepare("INSERT INTO clients (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", 
    $client_data['first_name'],
    $client_data['last_name'],
    $client_data['email'],
    $client_data['phone']);
$stmt->execute();
$client_id = $stmt->insert_id;
$stmt->close();

// Save appointment to database
$stmt = $conn->prepare("INSERT INTO appointments (lawyer_id, client_id, specialization_id, appointment_date, start_time, end_time, case_details) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiissss",
    $appointment_data['lawyer_id'],
    $client_id,
    $appointment_data['specialization_id'],
    $appointment_data['appointment_date'],
    $appointment_data['start_time'],
    $appointment_data['end_time'],
    $appointment_data['case_details']);
$stmt->execute();
$appointment_id = $stmt->insert_id;
$stmt->close();

// Get lawyer details
$stmt = $conn->prepare("SELECT u.first_name, u.last_name, u.email as lawyer_email FROM lawyers l JOIN users u ON l.user_id = u.id WHERE l.id = ?");
$stmt->bind_param("i", $appointment_data['lawyer_id']);
$stmt->execute();
$lawyer = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Format date and time
$formatted_date = date('l, F j, Y', strtotime($appointment_data['appointment_date']));
$formatted_time = date('g:i A', strtotime($appointment_data['start_time'])) . ' - ' . date('g:i A', strtotime($appointment_data['end_time']));

// Send notifications
$email_content = generateEmailContent(
    $client_data['first_name'] . ' ' . $client_data['last_name'],
    $lawyer['first_name'] . ' ' . $lawyer['last_name'],
    $formatted_date,
    $formatted_time,
    $appointment_data['case_details']
);

sendEmail(
    $client_data['email'],
    "Your Appointment Confirmation",
    $email_content
);

sendEmail(
    $lawyer['lawyer_email'],
    "New Appointment Booking",
    "You have a new appointment with {$client_data['first_name']} {$client_data['last_name']} on $formatted_date at $formatted_time"
);

sendSMS(
    $client_data['phone'],
    "Hello {$client_data['first_name']}, your appointment with {$lawyer['first_name']} {$lawyer['last_name']} is confirmed for $formatted_date at " . date('g:i A', strtotime($appointment_data['start_time'])) . "."
);

// Clear session data
unset($_SESSION['booking']);

// Display confirmation page
require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="bg-green-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-white">Appointment Confirmed!</h2>
                    <span class="text-sm font-medium text-green-100">Completed</span>
                </div>
            </div>
            
            <div class="px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h3>
                <p class="text-lg text-gray-600 mb-6">Your appointment has been successfully booked.</p>
                
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded-lg text-left">
                    <h4 class="text-lg font-medium text-green-800 mb-2">Confirmation Sent</h4>
                    <p class="text-sm text-green-700 mb-1">✓ Email confirmation sent to <strong><?= htmlspecialchars($client_data['email']) ?></strong></p>
                    <p class="text-sm text-green-700">✓ SMS reminder will be sent to <strong><?= htmlspecialchars($client_data['phone']) ?></strong></p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8 text-left">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Appointment Details</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Lawyer:</span> <?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Date:</span> <?= $formatted_date ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Time:</span> <?= $formatted_time ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Reference #:</span> APPT-<?= str_pad($appointment_id, 6, '0', STR_PAD_LEFT) ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($appointment_data['case_details'])): ?>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-900 mb-1">Your Notes:</p>
                        <p class="text-sm text-gray-600"><?= htmlspecialchars($appointment_data['case_details']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="space-y-3">
                    <a href="<?= BASE_URL ?>client/" class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Back to Home
                    </a>
                    <a href="#" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Add to Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>