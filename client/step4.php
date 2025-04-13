<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isset($_POST['appointment_date']) || !isset($_SESSION['booking']['lawyer_id'])) {
    header("Location: step1.php");
    exit();
}

// Store appointment details in session
$_SESSION['booking']['appointment_date'] = $_POST['appointment_date'];
$_SESSION['booking']['start_time'] = $_POST['start_time'];
$_SESSION['booking']['end_time'] = $_POST['end_time'];

// Fetch lawyer details
$lawyer_id = $_SESSION['booking']['lawyer_id'];
$sql = "SELECT l.*, u.first_name, u.last_name, s.name as specialization_name 
        FROM lawyers l
        JOIN users u ON l.user_id = u.id
        JOIN specializations s ON l.specialization_id = s.id
        WHERE l.id = $lawyer_id";
$lawyer = $conn->query($sql)->fetch_assoc();

require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Progress bar -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Your Details</h2>
                    <span class="text-sm font-medium text-indigo-600">Step 4 of 4</span>
                </div>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="px-6 py-5">
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6 rounded-lg">
                    <h3 class="text-lg font-medium text-indigo-800 mb-2">Appointment Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Lawyer:</span> <?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Specialization:</span> <?= htmlspecialchars($lawyer['specialization_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600"><span class="font-medium">Date:</span> <?= date('l, F j, Y', strtotime($_POST['appointment_date'])) ?></p>
                            <p class="text-sm text-gray-600"><span class="font-medium">Time:</span> <?= date('g:i A', strtotime($_POST['start_time'])) ?> - <?= date('g:i A', strtotime($_POST['end_time'])) ?></p>
                        </div>
                    </div>
                </div>
                
                <form action="confirmation.php" method="post">
                    <input type="hidden" name="lawyer_id" value="<?= $lawyer_id ?>">
                    <input type="hidden" name="appointment_date" value="<?= $_POST['appointment_date'] ?>">
                    <input type="hidden" name="start_time" value="<?= $_POST['start_time'] ?>">
                    <input type="hidden" name="end_time" value="<?= $_POST['end_time'] ?>">
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Your Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" id="first_name" name="first_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">We'll send your appointment confirmation here</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">We'll send an SMS reminder before your appointment</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="case_details" class="block text-sm font-medium text-gray-700 mb-1">Brief Description of Your Case (Optional)</label>
                        <textarea id="case_details" name="case_details" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="step3.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Confirm Appointment
                            <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>