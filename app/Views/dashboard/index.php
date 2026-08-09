<?= $this->extend('layout/main') ?> <!-- Assuming a base layout exists -->

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">System Dashboard</h4>
        <span class="text-muted"><?= date('F d, Y') ?></span>
    </div>

    <div class="row g-3">
        <!-- Metric Cards -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold mb-1">TOTAL EMPLOYEES</div>
                <h3 class="fw-bold mb-0"><?= $total_employees ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-primary border-4">
                <div class="text-muted small fw-bold mb-1">OFFICE UNITS</div>
                <h3 class="fw-bold mb-0"><?= $total_offices ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold mb-1">PENDING PAYROLL</div>
                <h3 class="fw-bold mb-0 text-warning">12</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small fw-bold mb-1">REPORTS GENERATED</div>
                <h3 class="fw-bold mb-0 text-success">45</h3>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold mb-3">Quick Navigation</h6>
                <div class="row g-2">
                    <div class="col-6 col-md-3 text-center">
                        <a href="/employee" class="btn btn-light w-100 py-3 border">
                            <i class="fas fa-users d-block mb-2"></i> Employees
                        </a>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <a href="/payroll" class="btn btn-light w-100 py-3 border">
                            <i class="fas fa-calculator d-block mb-2"></i> Run Payroll
                        </a>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <a href="/payslip" class="btn btn-light w-100 py-3 border">
                            <i class="fas fa-file-invoice-dollar d-block mb-2"></i> Payslips
                        </a>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <a href="/report" class="btn btn-light w-100 py-3 border">
                            <i class="fas fa-chart-bar d-block mb-2"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h6 class="fw-bold mb-3">User Profile</h6>
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <?= substr(session()->get('username'), 0, 1) ?>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold"><?= session()->get('username') ?></p>
                        <span class="badge bg-soft-primary text-primary"><?= ucfirst(session()->get('role')) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>