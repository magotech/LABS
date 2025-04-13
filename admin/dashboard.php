<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Verify admin authentication
adminAuthCheck();

// Fetch dashboard data
$totalAppointments = getTotalAppointments();
$activeLawyers = getActiveLawyersCount();
$totalClients = getTotalClients();
$pendingAppointments = getPendingAppointmentsCount();
$recentAppointments = getRecentAppointments(5);

$pageTitle = "Admin Dashboard";
include '../includes/header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Fixed Sidebar -->
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64 h-full border-r border-gray-200 bg-white fixed">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 flex-shrink-0 px-4 bg-blue-600">
                <i class="fas fa-shield-alt text-2xl text-white mr-2"></i>
                <span class="text-xl font-semibold text-white">Admin Panel</span>
            </div>
            
            <!-- Navigation -->
            <div class="flex-1 flex flex-col overflow-y-auto">
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <a href="dashboard.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i class="fas fa-tachometer-alt mr-3 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        Dashboard
                    </a>
                    
                    <a href="appointments/manage.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i class="fas fa-calendar-check mr-3 <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        Appointments
                    </a>
                    
                    <a href="lawyers/manage.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i class="fas fa-user-tie mr-3 <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        Lawyers
                    </a>
                    
                    <a href="users/manage.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                        <i class="fas fa-users mr-3 <?= strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                        Users
                    </a>
                </nav>
            </div>
            
            <!-- Logout Section - Fixed at bottom -->
            <div class="p-4 border-t border-gray-200 mt-auto">
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></p>
                        <a href="../logout.php" class="text-xs font-medium text-gray-500 hover:text-gray-700 flex items-center">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sign out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden ml-0 md:ml-64">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-tachometer-alt mr-2 text-blue-500"></i> Dashboard Overview
                </h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">
                        <?= date('l, F j, Y') ?>
                    </span>
                </div>
            </div>
        </header>
        
        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Appointments -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Appointments</p>
                        <p class="text-2xl font-semibold text-gray-800"><?= $totalAppointments ?></p>
                    </div>
                </div>
                
                <!-- Active Lawyers -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-tie text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Lawyers</p>
                        <p class="text-2xl font-semibold text-gray-800"><?= $activeLawyers ?></p>
                    </div>
                </div>
                
                <!-- Registered Clients -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Registered Clients</p>
                        <p class="text-2xl font-semibold text-gray-800"><?= $totalClients ?></p>
                    </div>
                </div>
                
                <!-- Pending Appointments -->
                <div class="bg-white rounded-lg shadow p-6 flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Appointments</p>
                        <p class="text-2xl font-semibold text-gray-800"><?= $pendingAppointments ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Appointments Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="font-semibold text-lg text-gray-800">
                        <i class="far fa-calendar-alt mr-2 text-blue-500"></i> Recent Appointments
                    </h2>
                    <a href="appointments/manage.php" class="text-sm text-blue-600 hover:text-blue-500 flex items-center">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lawyer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
    <?php foreach($recentAppointments as $appointment): ?>
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $appointment['id'] ?></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <?= htmlspecialchars($appointment['first_name']) . ' ' . htmlspecialchars($appointment['last_name']) ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <?= htmlspecialchars($appointment['lawyer_first_name']) . ' ' . htmlspecialchars($appointment['lawyer_last_name']) ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <?= date('M j, Y g:i A', strtotime($appointment['appointment_date'])) ?>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                <?= $appointment['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : '' ?>
                <?= $appointment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                <?= $appointment['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' ?>">
                <?= ucfirst($appointment['status']) ?>
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <a href="appointments/view.php?id=<?= $appointment['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                <i class="far fa-eye"></i>
            </a>
            <a href="appointments/manage.php?action=edit&id=<?= $appointment['id'] ?>" class="text-yellow-600 hover:text-yellow-900">
                <i class="far fa-edit"></i>
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-6">
            <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
                <div class="text-sm text-gray-600 mb-2 md:mb-0">
                    &copy; <?= date('Y') ?> Lawyer Booking System - Admin Portal
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-blue-600">
                        <i class="fas fa-cog"></i> Admin Settings
                    </a>
                    <span class="text-sm text-gray-500">
                        v1.0.0
                    </span>
                </div>
            </div>
        </footer>
    </div>
</div>

<?php include '../includes/footer.php'; ?>