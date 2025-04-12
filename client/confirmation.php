<?php 
include '../includes/config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['booking'])) {
    header("Location: index.php");
    exit;
}

// Validate input
$required = ['first_name', 'last_name', 'email', 'phone', 'appointment_time'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        header("Location: step4.php");
        exit;
    }
}

// Store client details in database
$stmt = $pdo->prepare("
    INSERT INTO clients (first_name, last_name, email, phone)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['email'],
    $_POST['phone']
]);
$client_id = $pdo->lastInsertId();

// Create appointment
$stmt = $pdo->prepare("
    INSERT INTO appointments (
        lawyer_id, 
        client_id, 
        specialization_id, 
        appointment_date, 
        start_time, 
        end_time, 
        timezone, 
        case_details,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')
");

// Calculate end time (1 hour after start time)
$start_time = $_POST['appointment_time'];
$end_time = date('H:i:s', strtotime($start_time) + 3600);

$stmt->execute([
    $_SESSION['booking']['lawyer_id'],
    $client_id,
    $_SESSION['booking']['specialization_id'],
    $_SESSION['booking']['appointment_date'],
    $start_time,
    $end_time,
    $_SESSION['booking']['timezone'],
    $_POST['case_details'] ?? null
]);
$appointment_id = $pdo->lastInsertId();

// Get appointment details for confirmation
$stmt = $pdo->prepare("
    SELECT 
        a.*, 
        c.first_name as client_first_name, 
        c.last_name as client_last_name,
        c.email as client_email,
        c.phone as client_phone,
        l.id as lawyer_id,
        u.first_name as lawyer_first_name,
        u.last_name as lawyer_last_name,
        u.email as lawyer_email,
        u.phone as lawyer_phone,
        s.name as specialization_name
    FROM appointments a
    JOIN clients c ON a.client_id = c.id
    JOIN lawyers l ON a.lawyer_id = l.id
    JOIN users u ON l.user_id = u.id
    JOIN specializations s ON a.specialization_id = s.id
    WHERE a.id = ?
");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

// Clear booking session
unset($_SESSION['booking']);

$pageTitle = "Appointment Confirmed";
include '../includes/header.php'; 
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <h2 class="h5 mb-0"><i class="fas fa-check-circle me-2"></i>Appointment Confirmed</h2>
                        <span class="confirmation-badge">#<?= $appointment['id'] ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-2x text-success mt-1 me-3"></i>
                            </div>
                            <div>
                                <h3 class="h4 mb-2">Thank you, <?= htmlspecialchars($appointment['client_first_name']) ?>!</h3>
                                <p class="mb-0">Your appointment has been successfully booked. A confirmation has been sent to <strong><?= htmlspecialchars($appointment['client_email']) ?></strong>.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <img src="../assets/images/lawyers/lawyer<?= rand(1,3) ?>.jpg" alt="<?= htmlspecialchars($appointment['lawyer_first_name']) ?>" class="lawyer-img me-3">
                                <div>
                                    <h3 class="h5 mb-1"><?= htmlspecialchars($appointment['lawyer_first_name'] . ' ' . $appointment['lawyer_last_name']) ?></h3>
                                    <p class="mb-1 text-accent"><?= htmlspecialchars($appointment['specialization_name']) ?></p>
                                    <div class="d-flex small text-muted">
                                        <span class="me-3"><i class="fas fa-star text-warning me-1"></i> 4.9</span>
                                        <span><i class="fas fa-briefcase me-1"></i> 10+ years</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h4 class="h6 mb-2 text-muted">APPOINTMENT DATE</h4>
                                        <p class="mb-0 h5"><?= date('l, F j, Y', strtotime($appointment['appointment_date'])) ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h4 class="h6 mb-2 text-muted">TIME</h4>
                                        <p class="mb-0 h5"><?= date('g:i A', strtotime($appointment['start_time'])) ?> - <?= date('g:i A', strtotime($appointment['end_time'])) ?></p>
                                        <small class="text-muted"><?= htmlspecialchars($appointment['timezone']) ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h4 class="h6 mb-2 text-muted">YOUR INFORMATION</h4>
                                        <p class="mb-1"><?= htmlspecialchars($appointment['client_first_name'] . ' ' . $appointment['client_last_name']) ?></p>
                                        <p class="mb-1 small"><?= htmlspecialchars($appointment['client_email']) ?></p>
                                        <p class="mb-0 small"><?= htmlspecialchars($appointment['client_phone']) ?></p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h4 class="h6 mb-2 text-muted">LAWYER CONTACT</h4>
                                        <p class="mb-1 small"><?= htmlspecialchars($appointment['lawyer_email']) ?></p>
                                        <p class="mb-0 small"><?= htmlspecialchars($appointment['lawyer_phone']) ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($appointment['case_details'])): ?>
                            <div class="mt-4 pt-3 border-top">
                                <h4 class="h6 mb-2 text-muted">CASE DETAILS</h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($appointment['case_details'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <div>
                            <button onclick="window.print()" class="btn btn-outline-primary me-2">
                                <i class="fas fa-print me-2"></i>Print
                            </button>
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Add to Calendar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h4 class="h5 mb-3">What to Expect Next</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary me-3">
                                    <i class="fas fa-envelope fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Confirmation Email</h5>
                                    <p class="small text-muted mb-0">You'll receive a confirmation email with all the details.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary me-3">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Reminder</h5>
                                    <p class="small text-muted mb-0">We'll send you a reminder 24 hours before your appointment.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary me-3">
                                    <i class="fas fa-video fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="h6 mb-1">Meeting Link</h5>
                                    <p class="small text-muted mb-0">If virtual, the meeting link will be sent to your email.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>