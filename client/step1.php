<?php
// Initialize with absolute paths
require_once __DIR__ . '/../includes/config.php';

// Verify database connection exists
if (!isset($conn)) {
    die("Database connection not established");
}

// Fetch specializations from database
$sql = "SELECT * FROM specializations";
$result = $conn->query($sql);

// Handle query errors
if (!$result) {
    error_log("Database Error: " . $conn->error);
    die("We're experiencing technical difficulties. Please try again later.");
}

// Include header after establishing DB connection
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Progress Header -->
        <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-indigo-800">Select Type of Lawyer</h2>
                <span class="text-sm font-medium text-indigo-600 bg-indigo-100 px-3 py-1 rounded-full">Step 1 of 4</span>
            </div>
            <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
                <div class="bg-indigo-600 h-2 rounded-full" style="width: 25%"></div>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">What type of legal assistance do you need?</h3>
            
            <form action="step2.php" method="post" class="space-y-6">
                <div class="space-y-4">
                    <?php while($specialization = $result->fetch_assoc()): ?>
                    <label class="flex items-start p-4 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition duration-150 ease-in-out cursor-pointer">
                        <input type="radio" name="specialization_id" value="<?php echo htmlspecialchars($specialization['id']); ?>" class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" required>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-start">
                                <span class="block text-sm font-medium text-gray-900"><?php echo htmlspecialchars($specialization['name']); ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($specialization['description']); ?></p>
                        </div>
                    </label>
                    <?php endwhile; ?>
                </div>
                
                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <a href="<?php echo BASE_URL; ?>client/" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Home
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Next: Select Lawyer
                        <svg xmlns="http://www.w3.org/2000/svg" class="-mr-1 ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>