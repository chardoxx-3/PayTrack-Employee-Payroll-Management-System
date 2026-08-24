<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    body.print-page {
        background: #fff;
        font-family: 'IBM Plex Sans', sans-serif;
    }
    .print-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .print-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .print-header h3 {
        font-weight: 700;
        color: #0d2d27;
        margin-bottom: 0.25rem;
    }
    .print-header p {
        color: #574a30;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    .print-controls {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .print-controls .btn {
        min-width: 120px;
    }
    .print-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }
    .print-table thead th {
        background: linear-gradient(135deg, #0d2d27 0%, #0d5c4e 100%);
        color: #e6f4f1;
        padding: 6px 8px;
        text-align: center;
        font-weight: 600;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
    .print-table tbody td {
        border: 1px solid #dee2e6;
        padding: 5px 8px;
        vertical-align: middle;
    }
    .print-table tbody tr:nth-child(even) td {
        background-color: #f8f9fa;
    }
    .print-table tbody tr:hover td {
        background-color: #e8f5f0;
    }
    .print-table .text-teal {
        color: #0d5c4e;
    }
    .print-table .text-end {
        text-align: right;
    }
    .print-table .text-center {
        text-align: center;
    }
    .print-table .fw-bold {
        font-weight: 600;
    }
    .print-table .text-muted {
        color: #6b7280 !important;
    }
    .print-table tfoot td {
        background: #e8f5f0;
        font-weight: 700;
        border-top: 2px solid #0d5c4e;
    }
    .office-filter-bar {
        background: #f5faf6;
        border: 1px solid #e7dcc0;
        border-radius: 6px;
        padding: 12px 20px;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid py-4 print-page">
    <div class="print-container">
        <!-- Header -->
        <div class="print-header">
            <h3>LGU-MAHINOG — Municipal Payroll</h3>
            <p><?= $period_label ?> | Period of Service: <strong><?= $service_period ?></strong></p>
        </div>

        <!-- Office Filter -->
        <form action="/payroll/print" method="get" class="office-filter-bar">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <select name="office_id" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                        <option value="">All Offices</option>
                        <?php foreach($offices as $office): ?>
                            <option value="<?= $office['id'] ?>" <?= (isset($office_id) && $office_id == $office['id']) ? 'selected' : '' ?>><?= $office['office_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small fw-bold">Showing: <?= $office_id ? ($offices[array_search($office_id, array_column($offices, 'id'))]['office_name'] ?? '') : 'All Offices' ?></span>
                </div>
                <div class="col-md-3 text-md-end">
                    <span class="badge bg-teal text-white px-3 py-2"><?= count($employees) ?> Employee(s)</span>
                </div>
            </div>
        </form>

        <!-- Print Controls -->
        <div class="print-controls">
            <button type="button" class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Payroll
            </button>
            <a href="<?= '/payroll/export' . ($office_id ? '?office_id=' . $office_id : '') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-file-excel me-1"></i> Export to Excel
            </a>
            <a href="/payroll" class="btn btn-outline-dark">
                <i class="fas fa-arrow-left me-1"></i> Back to Processing
            </button>
        </div>

        <?php
        function peso($value) {
            return $value > 0 ? '₱' . number_format($value, 2) : '—';
        }
        ?>

        <!-- Payroll Table -->
        <div class="table-responsive">
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 3%;">NO.</th>
                        <th style="width: 12%;">EMPLOYEE</th>
                        <th style="width: 10%;">DESIGNATION</th>
                        <th style="width: 10%;">MONTHLY RATE</th>
                        <th style="width: 10%;">REFUND/RATA</th>
                        <th style="width: 12%;">DEDUCTIONS</th>
                        <th style="width: 8%;">GSIS</th>
                        <th style="width: 8%;">PAG-IBIG</th>
                        <th style="width: 6%;">PHIC</th>
                        <th style="width: 8%;">BIR TAX</th>
                        <th style="width: 8%;">NET PAY</th>
                        <th style="width: 5%;">1st Q</th>
                        <th style="width: 5%;">2nd Q</th>
                        <th style="width: 5%;">SIGNATURE</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="bg-light text-muted text-center" style="font-weight: 600;">(blank)</th>
                        <th colspan="1" class="bg-light text-muted text-center" style="font-weight: 600;">(total)</th>
                        <th class="bg-light" style="font-size: 0.65rem;">G<br>+Diff<br>OULI</th>
                        <th class="bg-light" style="font-size: 0.65rem;">Prem<br>Loan<br>MP2</th>
                        <th class="bg-light">PHIC<br>+Diff</th>
                        <th class="bg-light">W/T Tax</th>
                        <th class="bg-light">Net</th>
                        <th class="bg-light">1st</th>
                        <th class="bg-light">2nd</th>
                        <th class="bg-light">Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($employees as $emp): ?>
                    <?php
                        $gsisTotal = ($emp['gsis_premium'] ?? 0) + ($emp['gsis_policy'] ?? 0) + ($emp['gsis_other'] ?? 0) + ($emp['gsis_ouli'] ?? 0) + ($emp['gsis_diff'] ?? 0);
                        $pagibigTotal = ($emp['pagibig_premium'] ?? 0) + ($emp['pagibig_loan'] ?? 0) + ($emp['pagibig_mp2'] ?? 0);
                        $phicTotal = ($emp['phic'] ?? 0) + ($emp['phic_diff'] ?? 0);
                        $totalDeductions = $gsisTotal + $pagibigTotal + $phicTotal + ($emp['bank_lbp'] ?? 0) + ($emp['bank_mcc'] ?? 0) + ($emp['bank_1stvb'] ?? 0) + ($emp['withholding_tax'] ?? 0) + ($emp['loans'] ?? 0) + ($emp['government_cont'] ?? 0) + ($emp['other_deduct'] ?? 0) + ($emp['bank_other_payables'] ?? 0) + ($emp['bank_rbt'] ?? 0);
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= esc($emp['full_name']) ?></div>
                            <small class="text-muted"><?= $emp['employee_id'] ?></small>
                        </td>
                        <td><small><?= esc($emp['position'] ?? '-') ?></small></td>
                        <td class="text-end text-teal fw-bold"><?= number_format($emp['salary_rate'], 2) ?></td>
                        <td class="text-end"><?= number_format($emp['refund_rata'] ?? 0, 2) ?></td>
                        <td class="text-end fw-bold"><?= number_format($totalDeductions, 2) ?></td>
                        <td class="text-end"><?= number_format($gsisTotal, 2) ?></td>
                        <td class="text-end"><?= number_format($pagibigTotal, 2) ?></td>
                        <td class="text-end"><?= number_format($phicTotal, 2) ?></td>
                        <td class="text-end"><?= number_format($emp['withholding_tax'] ?? 0, 2) ?></td>
                        <td class="text-end fw-bold text-success"><?= number_format($emp['net_pay'] ?? ($emp['salary_rate'] - $totalDeductions), 2) ?></td>
                        <td class="text-end"><?= number_format($emp['first_quincena'] ?? 0, 2) ?></td>
                        <td class="text-end"><?= number_format($emp['second_quincena'] ?? 0, 2) ?></td>
                        <td class="text-center"><?= esc($emp['contact_number'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($employees)): ?>
                    <tr><td colspan="14" class="text-center text-muted py-4">No employees with payroll data for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <?php if (!empty($employees)): ?>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">TOTALS:</td>
                        <td class="text-end"><?= number_format(array_sum(array_map(fn($e) => $e['refund_rata'] ?? 0, $employees)), 2) ?></td>
                        <td class="text-end"><?= number_format($total_deductions, 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_column($employees, 'gsis_premium')) + array_sum(array_column($employees, 'gsis_policy')) + array_sum(array_column($employees, 'gsis_other')) + array_sum(array_column($employees, 'gsis_ouli')) + array_sum(array_column($employees, 'gsis_diff')), 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_filter(array_column($employees, 'pagibig_premium'))) + array_sum(array_filter(array_column($employees, 'pagibig_loan'))) + array_sum(array_filter(array_column($employees, 'pagibig_mp2'))), 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_filter(array_column($employees, 'phic'))) + array_sum(array_filter(array_column($employees, 'phic_diff'))), 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_filter(array_column($employees, 'withholding_tax'))), 2) ?></td>
                        <td class="text-end fw-bold"><?= number_format($total_net, 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_filter(array_column($employees, 'first_quincena'))), 2) ?></td>
                        <td class="text-end"><?= number_format(array_sum(array_filter(array_column($employees, 'second_quincena'))), 2) ?></td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                </tfoot>
            </table>
        </div>

        <!-- Summary -->
        <?php if (!empty($employees)): ?>
        <div class="row g-3 mt-3">
            <div class="col-md-3">
                <div class="card erp-card border-0 shadow-sm text-center p-3">
                    <div class="text-muted small fw-bold">Total Employees</div>
                    <h4 class="fw-bold text-teal mb-0"><?= count($employees) ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card erp-card border-0 shadow-sm text-center p-3">
                    <div class="text-muted small fw-bold">Total Gross Pay</div>
                    <h4 class="fw-bold text-teal mb-0">₱<?= number_format($total_gross, 2) ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card erp-card border-0 shadow-sm text-center p-3">
                    <div class="text-muted small fw-bold">Total Deductions</div>
                    <h4 class="fw-bold text-danger mb-0">₱<?= number_format($total_deductions, 2) ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card erp-card border-0 shadow-sm text-center p-3">
                    <div class="text-muted small fw-bold">Total Net Pay</div>
                    <h4 class="fw-bold text-success mb-0">₱<?= number_format($total_net, 2) ?></h4>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
