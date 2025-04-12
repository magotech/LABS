<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <!-- Progress Steps -->
    <div class="d-flex justify-content-between mb-5">
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-completed rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check"></i>
        </div>
        <div class="step step-active rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            4
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 py-4">
            <h2 class="text-center mb-1">Your Information</h2>
            <p class="text-center text-muted">Please provide your details to confirm the appointment</p>
        </div>
        
        <div class="card-body">
            <div class="card mb-4 border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../assets/images/lawyers/lawyer<?= rand(1,3) ?>.jpg" class="rounded-circle me-3" width="80" height="80">
                        <div>
                            <h5 class="mb-1"><?= htmlspecialchars($lawyer['first_name'] . ' ' . $lawyer['last_name']) ?></h5>
                            <p class="text-primary small mb-2"><?= htmlspecialchars($specialization['name']) ?></p>
                            <div class="d-flex small text-muted">
                                <span class="me-3"><i class="far fa-calendar-alt me-1"></i> <?= date('M j, Y', strtotime($_SESSION['booking']['appointment_date'])) ?></span>
                                <span><i class="far fa-clock me-1"></i> <span id="selected-time-display">Select time</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="confirmation.php" method="post">
                <div class="mb-4">
                    <h5 class="mb-3">Select Time Slot</h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($time_slots as $slot): ?>
                        <div>
                            <input type="radio" class="btn-check time-slot-radio" name="appointment_time" id="time<?= str_replace(':', '', $slot) ?>" value="<?= $slot ?>" required>
                            <label class="btn btn-outline-primary" for="time<?= str_replace(':', '', $slot) ?>">
                                <?= date('g:i A', strtotime($slot)) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name *</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Case Details (Optional)</label>
                    <textarea class="form-control" name="case_details" rows="4" placeholder="Briefly describe your legal issue..."></textarea>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <a href="step3.php" class="btn btn-outline-secondary px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        Confirm Appointment <i class="fas fa-calendar-check ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.time-slot-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const timeString = this.value;
        const formattedTime = formatTime(timeString);
        document.getElementById('selected-time-display').textContent = formattedTime;
        
        document.querySelectorAll('.time-slot-radio').forEach(r => {
            r.nextElementSibling.classList.remove('btn-primary', 'active');
            r.nextElementSibling.classList.add('btn-outline-primary');
        });
        this.nextElementSibling.classList.remove('btn-outline-primary');
        this.nextElementSibling.classList.add('btn-primary', 'active');
    });
});

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}
</script>

<?php include '../includes/footer.php'; ?>