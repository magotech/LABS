<?php
// includes/admin_sidebar.php

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<!-- Fixed Sidebar -->
<div class="fixed h-screen w-64 flex-shrink-0 border-r border-gray-200 bg-white z-10">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 flex-shrink-0 px-4 bg-blue-600">
        <i class="fas fa-shield-alt text-2xl text-white mr-2"></i>
        <span class="text-xl font-semibold text-white">Admin Panel</span>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">
        <a href="dashboard.php" 
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
            <i class="fas fa-tachometer-alt mr-3 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
            Dashboard
        </a>
        
        <a href="appointments/manage.php" 
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
            <i class="fas fa-calendar-check mr-3 <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
            Appointments
        </a>
        
        <a href="lawyers/manage.php" 
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
            <i class="fas fa-user-tie mr-3 <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
            Lawyers
        </a>
        
        <a href="clients/manage.php" 
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'clients/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
            <i class="fas fa-users mr-3 <?= strpos($_SERVER['PHP_SELF'], 'clients/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
            Clients
        </a>
        
        <a href="settings.php" 
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
            <i class="fas fa-cog mr-3 <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
            Settings
        </a>
    </nav>
    
    <!-- Logout Section - Fixed at bottom -->
    <div class="p-4 border-t border-gray-200 absolute bottom-0 w-full bg-white">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></p>
                <a href="../logout.php" class="text-xs font-medium text-gray-500 hover:text-gray-700 flex items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i> Sign out
                </a>
            </div>
        </div>
    </div>
</div>