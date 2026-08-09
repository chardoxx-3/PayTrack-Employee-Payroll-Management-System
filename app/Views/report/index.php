<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h4 class="fw-bold mb-4">Reports Center</h4>

    <div class="row g-4">
        <!-- Report Generator Form -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Generate Summary Report</h6>
                    <form action="/report/generate" method="get">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">REPORT TYPE</label>
                            <select name="report_type" class="form-select">
                                <option value="office">Office-wise Summary</option>
                                <option value="period">Monthly Payroll Record</option>
                                <option value="deductions">Deduction Analysis</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">PAYROLL PERIOD</label>
                            <input type="month" name="period" class="form-control" value="<?= date('Y-m') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">OFFICE UNIT</label>
                            <select name="office_id" class="form-select">
                                <option value="all">All Offices</option>
                                <!-- Office loops -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-sync me-2"></i> Generate Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats for Context -->
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card border-0 shadow-sm p-4 text-center">
                        <i class="fas fa-download fa-2x text-primary mb-2"></i>
                        <p class="text-muted small mb-0">Total Processed (YTD)</p>
                        <h4 class="fw-bold">₱ 1,450,000.00</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm p-4 text-center">
                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                        <p class="text-muted small mb-0">Reports Archived</p>
                        <h4 class="fw-bold">128 Files</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>