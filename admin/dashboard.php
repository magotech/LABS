<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

adminAuthCheck();

// Handle search
$searchTerm = $_GET['search'] ?? '';
$whereClause = '';
$params = [];

if (!empty($searchTerm)) {
    $whereClause = "WHERE (c.first_name LIKE ? OR c.last_name LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params = array_fill(0, 4, $searchParam);
}

// Get dashboard stats
$stats = [
    'totalAppointments' => getTotalAppointments(),
    'activeLawyers' => getActiveLawyersCount(),
    'totalClients' => getTotalClients(),
    'pendingAppointments' => getPendingAppointmentsCount(),
    'recentAppointments' => getRecentAppointments(10, $whereClause, $params)
];

$pageTitle = "Admin Dashboard";
include '../includes/header.php';
?>

<!-- Main Layout Container -->
<div class="flex flex-col min-h-screen bg-gray-100">
    <!-- Top Navigation -->
    <header class="bg-white shadow-sm fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center ml-64">
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

    <!-- Main Content Area (offset for sidebar) -->
    <div class="flex flex-1 pt-16 ml-64">
        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <?php foreach ([
                    ['title' => 'Total Appointments', 'value' => $stats['totalAppointments'], 'icon' => 'calendar-check', 'color' => 'blue'],
                    ['title' => 'Active Lawyers', 'value' => $stats['activeLawyers'], 'icon' => 'user-tie', 'color' => 'green'],
                    ['title' => 'Registered Clients', 'value' => $stats['totalClients'], 'icon' => 'users', 'color' => 'indigo'],
                    ['title' => 'Pending Appointments', 'value' => $stats['pendingAppointments'], 'icon' => 'clock', 'color' => 'yellow']
                ] as $stat): ?>
                <div class="bg-white rounded-lg shadow p-6 flex items-center">
                    <div class="p-3 rounded-full bg-<?= $stat['color'] ?>-100 text-<?= $stat['color'] ?>-600 mr-4">
                        <i class="fas fa-<?= $stat['icon'] ?> text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500"><?= $stat['title'] ?></p>
                        <p class="text-2xl font-semibold text-gray-800"><?= $stat['value'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Recent Appointments Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="font-semibold text-lg text-gray-800">
                        <i class="far fa-calendar-alt mr-2 text-blue-500"></i> Recent Appointments
                    </h2>
                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                        <form method="GET" class="w-full sm:w-64">
                            <div class="relative">
                                <input type="text" name="search" placeholder="Search clients/lawyers..." 
                                       value="<?= htmlspecialchars($searchTerm) ?>"
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </form>
                        <a href="appointments/manage.php" class="text-sm text-blue-600 hover:text-blue-500 flex items-center justify-end sm:justify-start">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <?php include '../includes/notifications.php'; ?>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lawyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($stats['recentAppointments'] as $appointment): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $appointment['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars(($appointment['client_first_name'] ?? 'Unknown') . ' ' . ($appointment['client_last_name'] ?? 'Client')) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars(($appointment['lawyer_first_name'] ?? 'Unknown') . ' ' . ($appointment['lawyer_last_name'] ?? 'Lawyer')) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDateTime($appointment['appointment_date'] ?? '', $appointment['start_time'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= getStatusBadge($appointment['status'] ?? null) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="appointments/view.php?id=<?= $appointment['id'] ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="far fa-eye mr-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Sticky Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6 fixed bottom-0 w-full ml-64">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm text-gray-600 mb-2 md:mb-0">
                &copy; <?= date('Y') ?> Lawyer Booking System - Admin Portal
            </div>
            <div class="flex space-x-4">
                <span class="text-sm text-gray-500">
                    v1.0.0
                </span>
            </div>
        </div>
    </footer>
</div>

<?php include '../includes/footer.php'; ?>