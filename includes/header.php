<?php
// Ensure config is loaded first
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Appointment System</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/images/favicon.ico">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .fc .fc-button-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .fc .fc-button-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50">
    <!-- Header Content -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center">
                    <img class="h-8 w-auto" src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Law Firm Logo">
                    <span class="ml-2 text-xl font-semibold text-gray-900">LegalConnect</span>
                </a>
                <nav class="hidden md:flex space-x-8">
                    <a href="<?php echo BASE_URL; ?>client/" class="text-gray-500 hover:text-indigo-600">Book Appointment</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600">Lawyers</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600">About</a>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="flex-grow">