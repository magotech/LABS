<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 h-screen border-r border-gray-200 bg-white fixed">
        <!-- Lawyer Logo -->
        <div class="flex items-center justify-center h-16 flex-shrink-0 px-4 bg-green-600">
            <i class="fas fa-gavel text-2xl text-white mr-2"></i>
            <span class="text-xl font-semibold text-white">Lawyer Portal</span>
        </div>
        
        <!-- Lawyer Navigation -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-1">
                <a href="dashboard.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-tachometer-alt mr-3 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-green-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    Dashboard
                </a>
                
                <a href="appointments/manage.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-calendar-check mr-3 <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'text-green-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    My Appointments
                </a>
                
                <a href="schedule/manage.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'schedule/') !== false ? 'bg-green-50 text-green-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-clock mr-3 <?= strpos($_SERVER['PHP_SELF'], 'schedule/') !== false ? 'text-green-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    My Schedule
                </a>
            </nav>
        </div>
        
        <!-- Lawyer Logout Section -->
        <div class="p-4 border-t border-gray-200 mt-auto">
            <div class="flex items-center">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['lawyer_name'] ?? 'Lawyer') ?></p>
                    <a href="../../logout.php" class="text-xs font-medium text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sign out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>