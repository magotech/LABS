<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

adminAuthCheck();

// Handle delete request
if (isset($_GET['delete'])) {
    $lawyerId = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    
    if ($lawyerId) {
        try {
            // Delete associated user account if exists
            $stmt = $pdo->prepare("SELECT user_id FROM lawyers WHERE id = ?");
            $stmt->execute([$lawyerId]);
            $lawyer = $stmt->fetch();
            
            if ($lawyer && $lawyer['user_id']) {
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$lawyer['user_id']]);
            }
            
            // Delete lawyer record
            $pdo->prepare("DELETE FROM lawyers WHERE id = ?")->execute([$lawyerId]);
            
            $_SESSION['success'] = "Lawyer deleted successfully";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error deleting lawyer: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid lawyer ID";
    }
    header("Location: manage.php");
    exit;
}

// Search functionality
$searchTerm = $_GET['search'] ?? '';
$whereClause = '';
$params = [];

if (!empty($searchTerm)) {
    $whereClause = "WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR specialization LIKE ?)";
    $searchParam = "%$searchTerm%";
    $params = array_fill(0, 4, $searchParam);
}

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total lawyers count
$totalQuery = "SELECT COUNT(*) FROM lawyers $whereClause";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute($params);
$totalLawyers = $totalStmt->fetchColumn();

// Get lawyers data
$query = "SELECT * FROM lawyers $whereClause ORDER BY last_name, first_name LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$lawyers = $stmt->fetchAll();

$pageTitle = "Manage Lawyers";
include '../../includes/header.php';
?>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="font-semibold text-lg text-gray-800">
            <i class="fas fa-user-tie mr-2 text-blue-500"></i> Manage Lawyers
        </h2>
        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
            <form method="GET" class="w-full sm:w-64">
                <div class="relative">
                    <input type="text" name="search" placeholder="Search lawyers..." 
                           value="<?= htmlspecialchars($searchTerm) ?>"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </form>
            <a href="add.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Add New Lawyer
            </a>
        </div>
    </div>
    
    <!-- Display success/error messages -->
    <?php include '../../includes/notifications.php'; ?>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($lawyers as $lawyer): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <?php if ($lawyer['photo']): ?>
                                <img class="h-10 w-10 rounded-full object-cover" src="../../uploads/profile_pictures/<?= htmlspecialchars($lawyer['photo']) ?>" alt="<?= htmlspecialchars($lawyer['first_name']) ?>">
                                <?php else: ?>
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></div>
                                <div class="text-sm text-gray-500">License: <?= htmlspecialchars($lawyer['license_number']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= htmlspecialchars($lawyer['specialization']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div><?= htmlspecialchars($lawyer['email']) ?></div>
                        <div><?= htmlspecialchars($lawyer['phone']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $lawyer['status'] === 'active' ? 'bg-green-100 text-green-800' : '' ?>
                            <?= $lawyer['status'] === 'inactive' ? 'bg-red-100 text-red-800' : '' ?>
                            <?= $lawyer['status'] === 'on_leave' ? 'bg-yellow-100 text-yellow-800' : '' ?>">
                            <?= ucfirst(str_replace('_', ' ', $lawyer['status'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="edit.php?id=<?= $lawyer['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?= $lawyer['id'] ?>" onclick="return confirm('Are you sure you want to delete this lawyer?')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalLawyers > $perPage): ?>
    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>&search=<?= urlencode($searchTerm) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Previous
            </a>
            <a href="?page=<?= $page < ceil($totalLawyers / $perPage) ? $page + 1 : ceil($totalLawyers / $perPage) ?>&search=<?= urlencode($searchTerm) ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Next
            </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium"><?= $offset + 1 ?></span> to <span class="font-medium"><?= min($offset + $perPage, $totalLawyers) ?></span> of <span class="font-medium"><?= $totalLawyers ?></span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>&search=<?= urlencode($searchTerm) ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php for ($i = 1; $i <= ceil($totalLawyers / $perPage); $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>" class="<?= $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <a href="?page=<?= $page < ceil($totalLawyers / $perPage) ? $page + 1 : ceil($totalLawyers / $perPage) ?>&search=<?= urlencode($searchTerm) ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>