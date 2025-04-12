<?php 
require_once __DIR__ . '/../includes/config.php';

// Check if specialization was selected
if (!isset($_POST['specialization'])) {
    header("Location: index.php");
    exit;
}

$specialization_id = (int)$_POST['specialization'];
$_SESSION['booking']['specialization_id'] = $specialization_id;

// Get specialization details
try {
    $stmt = $pdo->prepare("SELECT * FROM specializations WHERE id = ?");
    $stmt->execute([$specialization_id]);
    $specialization = $stmt->fetch();

    if (!$specialization) {
        $_SESSION['error'] = "Invalid specialization selected";
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

// Get lawyers for this specialization
try {
    $lawyers_stmt = $pdo->prepare("
        SELECT l.*, u.first_name, u.last_name, u.email, u.phone 
        FROM lawyers l
        JOIN users u ON l.user_id = u.id
        WHERE l.specialization_id = ?
    ");
    $lawyers_stmt->execute([$specialization_id]);
    
    $lawyers = $lawyers_stmt->fetchAll();
    
    if (empty($lawyers)) {
        $_SESSION['error'] = "No lawyers available for this specialization. Please check back later.";
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

$pageTitle = "Select Lawyer";
include '../includes/header.php'; 

// Display any errors
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container py-5">
    <!-- Progress Steps -->
    <div class="d-flex justify-content-between mb-5">
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-active rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            2
        </div>
        <div class="step bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            3
        </div>
        <div class="step bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            4
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-4">
            <h2 class="text-center mb-1">Select Your Lawyer</h2>
            <p class="text-center text-muted">Choose from our qualified <?= htmlspecialchars($specialization['name']) ?> specialists</p>
        </div>
        
        <div class="card-body">
            <form action="step3.php" method="post" id="lawyerForm">
                <div class="row g-4">
                    <?php foreach ($lawyers as $lawyer): ?>
                    <div class="col-md-6">
                        <input type="radio" class="btn-check lawyer-radio" name="lawyer" id="lawyer<?= $lawyer['id'] ?>" value="<?= $lawyer['id'] ?>" required>
                        <label class="btn btn-outline-primary w-100 py-3 text-start lawyer-card" for="lawyer<?= $lawyer['id'] ?>">
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/lawyers/lawyer<?= rand(1,3) ?>.jpg" class="rounded-circle me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></h5>
                                    <div class="d-flex small text-muted mb-1">
                                        <span class="me-3"><i class="fas fa-star text-warning me-1"></i> 4.9</span>
                                        <span><i class="fas fa-briefcase me-1"></i> 10+ years</span>
                                    </div>
                                    <p class="text-muted small mb-1">$<?= number_format($lawyer['hourly_rate'], 2) ?>/hr</p>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars(substr($lawyer['bio'], 0, 80)) ?>...</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <a href="index.php" class="btn btn-outline-secondary px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" id="nextButton" disabled>
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('lawyerForm');
    const nextButton = document.getElementById('nextButton');
    const radioButtons = document.querySelectorAll('.lawyer-radio');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            nextButton.disabled = !Array.from(radioButtons).some(rb => rb.checked);
            
            document.querySelectorAll('.lawyer-card').forEach(card => {
                card.classList.remove('btn-primary', 'active');
                card.classList.add('btn-outline-primary');
            });
            
            if (this.checked) {
                this.nextElementSibling.classList.remove('btn-outline-primary');
                this.nextElementSibling.classList.add('btn-primary', 'active');
            }
        });
    });
    
    form.addEventListener('submit', function(e) {
        if (!Array.from(radioButtons).some(rb => rb.checked)) {
            e.preventDefault();
            alert('Please select a lawyer before proceeding');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>