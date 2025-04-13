<?php
require_once '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <img class="mx-auto h-16 w-auto" src="../assets/images/logo.png" alt="Law Firm Logo">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Book a Lawyer Appointment
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Get expert legal advice by booking a consultation with our qualified lawyers.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
            <div class="text-center">
                <a href="step1.php" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Start Booking Process
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            
            <div class="mt-6 grid grid-cols-3 gap-3">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600">
                        1
                    </div>
                    <p class="mt-2 text-xs text-gray-600">Select Lawyer Type</p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 text-gray-600">
                        2
                    </div>
                    <p class="mt-2 text-xs text-gray-600">Choose Lawyer</p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 text-gray-600">
                        3
                    </div>
                    <p class="mt-2 text-xs text-gray-600">Book Appointment</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>