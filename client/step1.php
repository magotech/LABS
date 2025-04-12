<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <!-- Progress Steps -->
    <div class="d-flex justify-content-between mb-5">
        <div class="step step-active rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            1
        </div>
        <div class="step bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
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
            <h2 class="text-center mb-1">What type of legal assistance do you need?</h2>
            <p class="text-center text-muted">Select the category that best matches your case</p>
        </div>
        
        <div class="card-body">
            <form action="step2.php" method="post">
                <div class="row g-4">
                    <?php while ($row = $stmt->fetch()): ?>
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="specialization" id="specialization<?= $row['id'] ?>" value="<?= $row['id'] ?>" required>
                        <label class="btn btn-outline-primary w-100 py-4 text-start" for="specialization<?= $row['id'] ?>">
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-100 p-3 rounded me-3">
                                    <i class="fas fa-<?= 
                                        $row['name'] == 'Family Law' ? 'home' : 
                                        ($row['name'] == 'Criminal Law' ? 'gavel' : 
                                        ($row['name'] == 'Corporate Law' ? 'building' : 'passport'))
                                    ?> text-blue-600"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($row['description']) ?></small>
                                </div>
                            </div>
                        </label>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>