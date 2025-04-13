<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

adminAuthCheck();

// Get lawyer ID from URL
$lawyerId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$lawyerId) {
    $_SESSION['error'] = "Invalid lawyer ID";
    header("Location: manage.php");
    exit;
}

// Fetch lawyer data
$stmt = $pdo->prepare("SELECT * FROM lawyers WHERE id = ?");
$stmt->execute([$lawyerId]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    $_SESSION['error'] = "Lawyer not found";
    header("Location: manage.php");
    exit;
}

// Initialize variables
$errors = [];
$currentPhoto = $lawyer['photo'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $lawyer['first_name'] = trim($_POST['first_name'] ?? '');
    $lawyer['last_name'] = trim($_POST['last_name'] ?? '');
    $lawyer['email'] = trim($_POST['email'] ?? '');
    $lawyer['phone'] = trim($_POST['phone'] ?? '');
    $lawyer['specialization'] = trim($_POST['specialization'] ?? '');
    $lawyer['license_number'] = trim($_POST['license_number'] ?? '');
    $lawyer['bio'] = trim($_POST['bio'] ?? '');
    $lawyer['experience_years'] = (int)($_POST['experience_years'] ?? 0);
    $lawyer['consultation_fee'] = (float)($_POST['consultation_fee'] ?? 0.00);
    $lawyer['status'] = $_POST['status'] ?? 'active';

    // Validate required fields
    if (empty($lawyer['first_name'])) $errors['first_name'] = 'First name is required';
    if (empty($lawyer['last_name'])) $errors['last_name'] = 'Last name is required';
    if (empty($lawyer['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($lawyer['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    if (empty($lawyer['phone'])) $errors['phone'] = 'Phone number is required';
    if (empty($lawyer['specialization'])) $errors['specialization'] = 'Specialization is required';
    if (empty($lawyer['license_number'])) $errors['license_number'] = 'License number is required';

    // Check if email or license number already exists (excluding current lawyer)
    $stmt = $pdo->prepare("SELECT id FROM lawyers WHERE (email = ? OR license_number = ?) AND id != ?");
    $stmt->execute([$lawyer['email'], $lawyer['license_number'], $lawyerId]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Email or license number already exists';
    }

    // Handle file upload
    $photo = $currentPhoto;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $_FILES['photo']['tmp_name']);
        finfo_close($fileInfo);

        if (in_array($detectedType, $allowedTypes)) {
            // Delete old photo if exists
            if ($currentPhoto && file_exists("../../uploads/profile_pictures/$currentPhoto")) {
                unlink("../../uploads/profile_pictures/$currentPhoto");
            }

            $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo = uniqid('lawyer_') . '.' . $extension;
            $uploadPath = '../../uploads/profile_pictures/' . $photo;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                $errors['photo'] = 'Failed to upload photo';
                $photo = $currentPhoto; // Revert to current photo if upload fails
            }
        } else {
            $errors['photo'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        }
    }

    // If no errors, update database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE lawyers SET 
                first_name = ?, last_name = ?, email = ?, phone = ?, specialization = ?, 
                license_number = ?, bio = ?, experience_years = ?, consultation_fee = ?, 
                photo = ?, status = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([
                $lawyer['first_name'],
                $lawyer['last_name'],
                $lawyer['email'],
                $lawyer['phone'],
                $lawyer['specialization'],
                $lawyer['license_number'],
                $lawyer['bio'],
                $lawyer['experience_years'],
                $lawyer['consultation_fee'],
                $photo,
                $lawyer['status'],
                $lawyerId
            ]);

            $_SESSION['success'] = "Lawyer updated successfully";
            header("Location: manage.php");
            exit;
        } catch (PDOException $e) {
            $errors['database'] = "Database error: " . $e->getMessage();
        }
    }
}

$pageTitle = "Edit Lawyer";
include '../../includes/header.php';
?>

<div class="bg-white rounded-lg shadow overflow-hidden max-w-3xl mx-auto">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="font-semibold text-lg text-gray-800">
            <i class="fas fa-user-edit mr-2 text-blue-500"></i> Edit Lawyer: <?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?>
        </h2>
    </div>
    
    <!-- Display success/error messages -->
    <?php include '../../includes/notifications.php'; ?>
    
    <form method="POST" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- First Name -->
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($lawyer['first_name']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['first_name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['first_name'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Last Name -->
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($lawyer['last_name']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['last_name'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['last_name'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($lawyer['email']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['email'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($lawyer['phone']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['phone'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['phone'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Specialization -->
            <div>
                <label for="specialization" class="block text-sm font-medium text-gray-700">Specialization *</label>
                <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($lawyer['specialization']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['specialization'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['specialization'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- License Number -->
            <div>
                <label for="license_number" class="block text-sm font-medium text-gray-700">License Number *</label>
                <input type="text" id="license_number" name="license_number" value="<?= htmlspecialchars($lawyer['license_number']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['license_number'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['license_number'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Experience Years -->
            <div>
                <label for="experience_years" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                <input type="number" id="experience_years" name="experience_years" min="0" value="<?= htmlspecialchars($lawyer['experience_years']) ?>"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Consultation Fee -->
            <div>
                <label for="consultation_fee" class="block text-sm font-medium text-gray-700">Consultation Fee</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input type="number" id="consultation_fee" name="consultation_fee" min="0" step="0.01" value="<?= htmlspecialchars($lawyer['consultation_fee']) ?>"
                           class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2 px-3">
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="active" <?= $lawyer['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $lawyer['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="on_leave" <?= $lawyer['status'] === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                </select>
            </div>
            
            <!-- Photo -->
            <div class="md:col-span-2">
                <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                <div class="mt-1 flex items-center">
                    <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                        <?php if ($lawyer['photo']): ?>
                            <img src="../../uploads/profile_pictures/<?= htmlspecialchars($lawyer['photo']) ?>" alt="Current Photo" class="h-full w-full object-cover">
                        <?php else: ?>
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        <?php endif; ?>
                    </span>
                    <input type="file" id="photo" name="photo" accept="image/*" class="ml-5 block">
                    <?php if ($lawyer['photo']): ?>
                        <div class="ml-4">
                            <input type="checkbox" id="remove_photo" name="remove_photo" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remove_photo" class="ml-2 text-sm text-gray-700">Remove current photo</label>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (isset($errors['photo'])): ?>
                    <p class="mt-1 text-sm text-red-600"><?= $errors['photo'] ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Bio -->
            <div class="md:col-span-2">
                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($lawyer['bio']) ?></textarea>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <a href="manage.php" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Lawyer
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>