<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Payslip Search & Batch Print</h5>
                </div>
                <div class="card-body">
                    <form action="/payslip/batchPrint" method="get" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="office_id" class="form-label fw-bold">Office</label>
                            <select name="office_id" id="office_id" class="form-select" required>
                                <option value="">Select Office...</option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?= $office['id'] ?>"><?= esc($office['office_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select an office.</div>
                        </div>

                        <div class="mb-3">
                            <label for="period" class="form-label fw-bold">Payroll Period</label>
                            <input type="month" name="period" id="period" class="form-control" 
                                   value="<?= date('Y-m') ?>" required>
                            <div class="invalid-feedback">Please select a valid period.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-print me-2"></i>Generate Batch Payslips
                            </button>
                            <a href="/payslip/preview/1" class="btn btn-outline-secondary">
                                <i class="fas fa-eye me-2"></i>Preview Individual Payslip (Example)
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-muted small">
                <p class="mb-1"><i class="fas fa-info-circle me-1"></i> Batch printing generates payslips for all employees in the selected office for the given period.</p>
                <p class="mb-0"><i class="fas fa-print me-1"></i> Use the print dialog to print or save as PDF.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Enable Bootstrap form validation
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
<?= $this->endSection() ?>