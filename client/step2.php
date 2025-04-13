<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isset($_POST['specialization_id'])) {
    header("Location: step1.php");
    exit();
}

$specialization_id = intval($_POST['specialization_id']);

// Store in session for later steps
$_SESSION['booking']['specialization_id'] = $specialization_id;

// Fetch specialization details
$specialization = $conn->query("SELECT * FROM specializations WHERE id = $specialization_id")->fetch_assoc();

// Fetch lawyers with this specialization
$sql = "SELECT l.*, u.first_name, u.last_name, u.email, u.phone 
        FROM lawyers l
        JOIN users u ON l.user_id = u.id
        WHERE l.specialization_id = $specialization_id";
$lawyers = $conn->query($sql);

require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Progress bar -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Select a Lawyer</h2>
                    <span class="text-sm font-medium text-indigo-600">Step 2 of 4</span>
                </div>
                <div class="mt-4 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 50%"></div>
                </div>
            </div>
            
            <div class="px-6 py-5">
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-indigo-700">
                                You're looking for a <strong><?= htmlspecialchars($specialization['name']) ?></strong> lawyer.
                            </p>
                        </div>
                    </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Lawyers</h3>
                
                <?php if ($lawyers->num_rows > 0): ?>
                    <form action="step3.php" method="post">
                        <input type="hidden" name="specialization_id" value="<?= $specialization_id ?>">
                        
                        <div class="space-y-4 mb-6">
                            <?php while($lawyer = $lawyers->fetch_assoc()): ?>
                            <label class="flex items-start p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer transition duration-150 ease-in-out">
                                <input type="radio" name="lawyer_id" value="<?= $lawyer['id'] ?>" class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" required>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900"><?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></span>
                                            <span class="block text-sm text-indigo-600">$<?= number_format($lawyer['hourly_rate'], 2) ?>/hr</span>
                                        </div>
                                        <div class="flex items-center text-yellow-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-1 text-xs text-gray-500">4.8</span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500"><?= htmlspecialchars($lawyer['bio']) ?></p>
                                </div>
                            </label>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="flex justify-between">
                            <a href="step1.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                Next: Select Date & Time
                                <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    No lawyers available for this specialization at the moment.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="step1.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Back to Specializations
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>