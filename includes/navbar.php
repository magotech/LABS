<header class="bg-white shadow-sm">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Mobile menu button -->
        <button class="md:hidden text-gray-500 focus:outline-none" onclick="toggleSidebar()">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <!-- Search bar -->
        <div class="flex-1 max-w-md ml-4 md:ml-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search...">
            </div>
        </div>
        
        <!-- Right side icons -->
        <div class="flex items-center space-x-4">
            <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                <span class="sr-only">Notifications</span>
                <i class="far fa-bell text-xl"></i>
            </button>
            
            <div class="relative ml-3">
                <div>
                    <button type="button" class="flex text-sm rounded-full focus:outline-none" id="user-menu-button">
                        <span class="sr-only">Open user menu</span>
                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)); ?>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile sidebar -->
<div id="mobile-sidebar" class="hidden fixed inset-0 z-40 md:hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" onclick="toggleSidebar()"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" onclick="toggleSidebar()">
                <span class="sr-only">Close sidebar</span>
                <i class="fas fa-times text-white text-xl"></i>
            </button>
        </div>
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex-shrink-0 flex items-center px-4">
                <i class="fas fa-balance-scale text-2xl text-blue-600 mr-2"></i>
                <span class="text-xl font-semibold text-gray-800">Lawyer Booking</span>
            </div>
            <nav class="mt-5 px-2 space-y-1">
                <a href="dashboard.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-tachometer-alt mr-4 <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    Dashboard
                </a>
                
                <a href="appointments/manage.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-calendar-check mr-4 <?= strpos($_SERVER['PHP_SELF'], 'appointments/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    Appointments
                </a>
                
                <a href="lawyers/manage.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-user-tie mr-4 <?= strpos($_SERVER['PHP_SELF'], 'lawyers/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    Lawyers
                </a>
                
                <a href="users/manage.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md <?= strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                    <i class="fas fa-users mr-4 <?= strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500' ?>"></i>
                    Users
                </a>
            </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
            <div class="flex items-center">
                <div class="ml-3">
                    <p class="text-base font-medium text-gray-700"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></p>
                    <a href="../logout.php" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sign out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('mobile-sidebar').classList.toggle('hidden');
    }
</script>