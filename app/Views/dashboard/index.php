<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .dashboard-card {
        background: #fff;
        border: 1px solid #e7dcc0;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .dashboard-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .metric-card {
        border-left: 4px solid var(--bs-primary);
    }
    .metric-card .metric-value {
        font-weight: 600;
    }
    .metric-teal { border-left-color: #0d5c4e; }
    .metric-green { border-left-color: #1a7d5b; }
    .metric-warning { border-left-color: #d97706; }
    .metric-info { border-left-color: #0d6efd; }
    .bg-teal { background-color: #0d5c4e !important; }
    .bg-teal-light { background-color: #e6f4f1 !important; }
    .text-teal { color: #0d5c4e !important; }
    .chart-card {
        background: #fff;
        border: 1px solid #e7dcc0;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 600;
    }
    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #574a30;
    }
    .table-analytics th {
        background-color: #0d2d27 !important;
        color: #e6f4f1 !important;
        font-size: 0.8rem;
    }
    .table-analytics td {
        border: 1px solid #dee2e6 !important;
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">System Dashboard</h4>
            <p class="text-muted small mb-0">Analytics & overview — Period: <strong><?= date('F Y') ?></strong></p>
        </div>
        <span class="text-muted small">Updated: <?= date('M d, Y g:i A') ?></span>
    </div>

    <!-- Metric Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 metric-card metric-teal">
                <div class="stat-label">Total Employees</div>
                <div class="stat-value text-teal"><?= $total_employees ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 metric-card metric-info">
                <div class="stat-label">Office Units</div>
                <div class="stat-value text-primary"><?= $total_offices ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 metric-card metric-green">
                <div class="stat-label">Payroll Processed</div>
                <div class="stat-value text-success"><?= $processed_payroll ?>/<?= $total_employees ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 metric-card metric-warning">
                <div class="stat-label">Pending Payroll</div>
                <div class="stat-value text-warning"><?= $pending_payroll ?></div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="stat-label">Total Gross Pay</div>
                <div class="stat-value text-teal">₱<?= number_format($total_gross_pay, 2) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="stat-label">Total Deductions</div>
                <div class="stat-value text-danger">₱<?= number_format($total_deductions, 2) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="stat-label">Total Net Pay</div>
                <div class="stat-value text-success">₱<?= number_format($total_net_pay, 2) ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="stat-label">Total Cash Paid</div>
                <div class="stat-value text-primary">₱<?= number_format($total_cash_paid, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-3">
        <!-- Payroll by Office - Bar Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Payroll by Office (Current Period)</h6>
                <div style="position: relative; height: 320px;">
                    <canvas id="payrollByOfficeChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Deductions Breakdown - Doughnut Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Deductions Breakdown</h6>
                <div style="position: relative; height: 320px;">
                    <canvas id="deductionsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Office Distribution + Employment Status -->
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Employees by Office</h6>
                <div style="position: relative; height: 280px;">
                    <canvas id="employeesByOfficeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Employment Status</h6>
                <div style="position: relative; height: 280px;">
                    <canvas id="employmentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row g-3">
        <!-- Recent Payroll Records -->
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Top 10 Payroll Records (by Gross Pay)</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-analytics mb-0">
                        <thead>
                            <tr>
                                <th>NO.</th>
                                <th>EMPLOYEE</th>
                                <th>OFFICE</th>
                                <th class="text-end">GROSS PAY</th>
                                <th class="text-end">DEDUCTIONS</th>
                                <th class="text-end">NET PAY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_payroll as $i => $r): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($r['full_name']) ?></div>
                                    <small class="text-muted"><?= $r['emp_code'] ?></small>
                                </td>
                                <td><span class="text-muted small"><?= esc($r['office_name'] ?? '—') ?></span></td>
                                <td class="text-end fw-bold text-teal">₱<?= number_format($r['gross_pay'], 2) ?></td>
                                <td class="text-end text-danger">₱<?= number_format($r['total_deductions'], 2) ?></td>
                                <td class="text-end fw-bold text-success">₱<?= number_format($r['net_pay'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recent_payroll)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">No payroll records for current period.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Top Deduction Holders -->
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm p-4 chart-card h-100">
                <h6 class="fw-bold mb-3">Top 5 Deduction Holders</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-analytics mb-0">
                        <thead>
                            <tr>
                                <th>NO.</th>
                                <th>EMPLOYEE</th>
                                <th>OFFICE</th>
                                <th class="text-end">TOTAL DEDUCTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_deductions as $i => $t): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($t['full_name']) ?></div>
                                    <small class="text-muted"><?= $t['emp_code'] ?></small>
                                </td>
                                <td><span class="text-muted small"><?= esc($t['office_name'] ?? '—') ?></span></td>
                                <td class="text-end fw-bold text-danger">₱<?= number_format($t['total_deduct'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($top_deductions)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">No deduction data available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payroll by Office - Bar Chart
    const payrollCtx = document.getElementById('payrollByOfficeChart').getContext('2d');
    const payrollByOfficeData = <?= json_encode($payroll_by_office) ?>;
    new Chart(payrollCtx, {
        type: 'bar',
        data: {
            labels: payrollByOfficeData.map(r => r.office_name),
            datasets: [
                {
                    label: 'Gross Pay',
                    data: payrollByOfficeData.map(r => parseFloat(r.gross)),
                    backgroundColor: 'rgba(13, 92, 78, 0.7)',
                    borderColor: 'rgba(13, 92, 78, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Deduct.',
                    data: payrollByOfficeData.map(r => parseFloat(r.deductions)),
                    backgroundColor: 'rgba(217, 119, 6, 0.6)',
                    borderColor: 'rgba(217, 119, 6, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Net Pay',
                    data: payrollByOfficeData.map(r => parseFloat(r.net)),
                    backgroundColor: 'rgba(26, 125, 91, 0.7)',
                    borderColor: 'rgba(26, 125, 91, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': ₱' + Number(ctx.raw).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US');
                        }
                    }
                }
            }
        }
    });

    // Deductions Breakdown - Doughnut Chart
    const deductionsCtx = document.getElementById('deductionsChart').getContext('2d');
    const deductionsData = <?= json_encode($deductions_breakdown) ?>;
    new Chart(deductionsCtx, {
        type: 'doughnut',
        data: {
            labels: deductionsData.map(d => d.label),
            datasets: [{
                data: deductionsData.map(d => d.value),
                backgroundColor: [
                    'rgba(13, 92, 78, 0.8)',  'rgba(13, 92, 78, 0.6)',  'rgba(13, 92, 78, 0.4)',
                    'rgba(217, 119, 6, 0.8)',  'rgba(217, 119, 6, 0.6)',
                    'rgba(108, 117, 151, 0.8)',
                    'rgba(13, 110, 255, 0.7)',  'rgba(13, 110, 255, 0.5)', 'rgba(13, 110, 255, 0.3)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(108, 117, 151, 0.6)', 'rgba(108, 117, 151, 0.4)', 'rgba(220, 53, 69, 0.5)',
                ],
                hoverBackgroundColor: [
                    'rgba(13, 92, 78, 1)',    'rgba(13, 92, 78, 0.9)',   'rgba(13, 92, 78, 0.8)',
                    'rgba(217, 119, 6, 1)',   'rgba(217, 119, 6, 0.9)',
                    'rgba(108, 117, 151, 1)',
                    'rgba(13, 110, 255, 0.9)', 'rgba(13, 110, 255, 0.8)', 'rgba(13, 110, 255, 0.7)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 151, 0.9)', 'rgba(108, 117, 151, 0.8)', 'rgba(220, 53, 69, 0.9)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { maxColumns: 2, font: { size: 10 } } },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.label + ': ₱' + Number(ctx.raw).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            }
        }
    });

    // Employees by Office - Bar Chart
    const empCtx = document.getElementById('employeesByOfficeChart').getContext('2d');
    const empByOfficeData = <?= json_encode($employees_by_office) ?>;
    new Chart(empCtx, {
        type: 'bar',
        data: {
            labels: empByOfficeData.map(r => r.office_name),
            datasets: [{
                label: 'Employees',
                data: empByOfficeData.map(r => parseInt(r.count)),
                backgroundColor: 'rgba(13, 92, 78, 0.7)',
                borderColor: 'rgba(13, 92, 78, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5 }
                }
            }
        }
    });

    // Employment Status - Doughnut Chart
    const statusCtx = document.getElementById('employmentStatusChart').getContext('2d');
    const empStatusData = <?= json_encode($employment_status_counts) ?>;
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: empStatusData.map(r => r.employment_status),
            datasets: [{
                data: empStatusData.map(r => parseInt(r.count)),
                backgroundColor: ['rgba(26, 125, 91, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(108, 117, 151, 0.8)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>