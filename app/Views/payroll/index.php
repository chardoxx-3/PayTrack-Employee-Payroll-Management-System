<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
.payroll-table thead th {
    background-color: #0d2d27 !important;
    color: #e6f4f1 !important;
}

.payroll-table th,
.payroll-table td {
    border: 1px solid #dee2e6 !important;
}

.payroll-table tbody tr:hover {
    background-color: #f5faf6 !important;
}

.row-selected {
    background-color: #d4edda !important;
    border-left: 3px solid #0d5c4e !important;
}

.row-selected td {
    background-color: #d4edda !important;
    color: #1e293b !important;
}

.row-selected td small,
.row-selected td .text-muted {
    color: #4a635f !important;
}

.row-selected td .badge {
    color: #ffffff !important;
    background-color: rgba(13, 92, 78, 0.85) !important;
}

.row-selected td .btn {
    color: #ffffff !important;
    background-color: #0d5c4e !important;
    border-color: #0d5c4e !important;
}


</style>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}

?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Payroll Processing</h4>
<p class="text-muted small mb-1">Select an office to begin processing the period: <strong><?= date('F Y') ?></strong></p>
<p class="text-muted small mb-0">Period of Service: <strong><?= date('m/01/Y') . '-' . date('m/t/Y') ?></strong></p>
        </div>
<div class="d-flex gap-2">
    <a href="/deduction" class="btn btn-outline-dark btn-sm"><i class="fas fa-sliders-h me-1"></i> Manage Deductions</a>
    <a href="<?= '/payroll/print' . ($office_id ? '?office_id=' . $office_id : '') ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="fas fa-print me-1"></i> Print All</a>
    <a href="<?= '/payroll/export' . ($office_id ? '?office_id=' . $office_id : '') ?>" class="btn btn-success btn-sm"><i class="fas fa-file-export me-1"></i> Export All</a>
</div>
    </div>

    <div class="card border-0 shadow-sm p-3 mb-4 bg-light">
        <form action="/payroll" method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
<select name="office_id" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
    <option value="">Select Office Assignment...</option>
    <?php foreach($offices as $office): ?>
        <option value="<?= $office['id'] ?>" <?= (isset($office_id) && $office_id == $office['id']) ? 'selected' : '' ?>><?= $office['office_name'] ?></option>
    <?php endforeach; ?>
</select>
            </div>
            <div class="col-md-5">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0" placeholder="Quick find employee ID or name...">
                </div>
            </div>
        </form>
    </div>

<div class="table-responsive">
        <table class="table table-hover bg-white rounded shadow-sm align-middle payroll-table">
<thead class="text-muted small fw-bold">
    <tr>
        <th rowspan="2" class="text-center" style="vertical-align: middle; width: 40px;">NO.</th>
        <th class="ps-4" rowspan="2" style="vertical-align: middle; width: 150px;">EMPLOYEE</th>
        <th rowspan="2" style="vertical-align: middle; width: 120px;">DESIGNATION</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 100px;">MONTHLY RATE</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 100px;">REFUND RATA PERA ACA DIFFERENTIAL</th>
        <th colspan="3" class="text-center border-start">GSIS</th>
        <th colspan="2" class="text-center border-start">PAG-IBIG</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 100px;">PHIC PHIC-Diff</th>
        <th colspan="3" class="text-center border-start">BANKS / COOP'S</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 90px;">BIR TAX</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 90px;">NET PAY</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 150px;">QUINCENA</th>
        <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 100px;">SIGNATURE</th>
        <th rowspan="2" class="text-end pe-4" style="vertical-align: middle; width: 50px;">ACTION</th>
    </tr>
    <tr>
        <th class="text-center small border-start" style="width: 100px;">PREMIUM (Personal) OULI diff</th>
        <th class="text-center small" style="vertical-align: middle; width: 100px;">Conso Policy MPL</th>
        <th class="text-center small" style="vertical-align: middle; width: 100px;">GFAL EMRGYLN MPL LITE CPL</th>
        <th class="text-center small border-start" style="vertical-align: middle; width: 100px;">PREMIUM (Personal)</th>
        <th class="text-center small" style="vertical-align: middle; width: 100px;">SALARY CALAMITY</th>
        <th class="text-center small border-start" style="vertical-align: middle; width: 100px;">LBP Other Payables</th>
        <th class="text-center small" style="vertical-align: middle; width: 80px;">MCC (over)</th>
        <th class="text-center small" style="vertical-align: middle; width: 80px;">1stVB RBT</th>
    </tr>
</thead>
<tbody>
    <?php
    $no = 1;
    $totalFirstQ = 0;
    $totalSecondQ = 0;
    $totalSalaryRate = 0;
    $totalRefund = 0;
    $totalGsisPremium = 0;
    $totalGsisPolicy = 0;
    $totalGsisOther = 0;
    $totalPagibigPremium = 0;
    $totalPagibigLoan = 0;
    $totalPhic = 0;
    $totalBankLbp = 0;
    $totalBankMcc = 0;
    $totalBank1stvb = 0;
    $totalWithholdingTax = 0;
    $totalNetPay = 0;
    foreach($employees as $emp):
    ?>
    <?php
        $totalDeductions = $emp['total_deductions'] ?? 0;
        $netPay = $emp['net_pay'] ?? ($emp['salary_rate'] - $totalDeductions);
        $firstQ = $emp['first_quincena'] ?? round($netPay / 2, 2);
        $secondQ = $emp['second_quincena'] ?? round($netPay - $firstQ, 2);
        $totalFirstQ += $firstQ;
        $totalSecondQ += $secondQ;
        $totalSalaryRate += $emp['salary_rate'] ?? 0;
        $totalRefund += $emp['refund_rata'] ?? 0;
        $totalGsisPremium += $emp['gsis_premium'] ?? 0;
        $totalGsisPolicy += $emp['gsis_policy'] ?? 0;
        $totalGsisOther += $emp['gsis_other'] ?? 0;
        $totalPagibigPremium += $emp['pagibig_premium'] ?? 0;
        $totalPagibigLoan += $emp['pagibig_loan'] ?? 0;
        $totalPhic += $emp['phic'] ?? 0;
        $totalBankLbp += $emp['bank_lbp'] ?? 0;
        $totalBankMcc += $emp['bank_mcc'] ?? 0;
        $totalBank1stvb += $emp['bank_1stvb'] ?? 0;
        $totalWithholdingTax += $emp['withholding_tax'] ?? 0;
        $totalNetPay += $netPay;
    ?>
    <tr class="border-bottom">
        <td class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
        <td class="ps-4">
            <div class="fw-bold"><?= $emp['full_name'] ?></div>
        </td>
        <td><?= $emp['position'] ?></td>
        <td><?= peso($emp['salary_rate']) ?></td>
        <td class="text-muted small text-end border-start"><?= peso($emp['refund_rata'] ?? 0) ?></td>
        <td class="text-danger small text-end border-start"><?= peso($emp['gsis_premium'] ?? 0) ?></td>
        <td class="text-danger small text-end"><?= peso($emp['gsis_policy'] ?? 0) ?></td>
        <td class="text-danger small text-end"><?= peso($emp['gsis_other'] ?? 0) ?></td>
        <td class="text-danger small text-end border-start"><?= peso($emp['pagibig_premium'] ?? 0) ?></td>
        <td class="text-danger small text-end"><?= peso($emp['pagibig_loan'] ?? 0) ?></td>
        <td class="text-danger small text-end border-start"><?= peso($emp['phic'] ?? 0) ?></td>
        <td class="text-danger small text-end border-start"><?= peso($emp['bank_lbp'] ?? 0) ?></td>
        <td class="text-danger small text-end"><?= peso($emp['bank_mcc'] ?? 0) ?></td>
        <td class="text-danger small text-end"><?= peso($emp['bank_1stvb'] ?? 0) ?></td>
        <td class="text-danger small text-end border-start"><?= peso($emp['withholding_tax'] ?? 0) ?></td>
        <td class="fw-bold text-success border-start"><?= peso($netPay) ?></td>
        <td class="text-muted small border-start">1st Q: <?= peso($firstQ) ?><br>2nd Q: <?= peso($secondQ) ?></td>
        <td class="text-muted small border-start text-end"><?= esc($emp['contact_number'] ?? '') ?></td>
        <td class="text-center" style="width: 50px;">
            <div class="dropdown">
                <button class="btn btn-link text-dark p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 36px; min-height: 36px;">
                    <i class="fas fa-ellipsis-vertical fa-lg"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                    <button class="dropdown-item d-flex align-items-center gap-2 process-btn"
                        data-employee-id="<?= $emp['id'] ?>"
                        data-employee-name="<?= $emp['full_name'] ?>"
                        data-designation="<?= $emp['position'] ?>"
                        data-net-pay="<?= $netPay ?>"
                        data-first-quincena="<?= $firstQ ?>"
                        data-second-quincena="<?= $secondQ ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#processModal">
                            <i class="fas fa-calculator text-primary"></i> Process
                        </button>
                    </li>
                    <li>
                        <a href="/payslip/individual/<?= $emp['id'] ?>" class="dropdown-item d-flex align-items-center gap-2" target="_blank">
                            <i class="fas fa-print text-success"></i> Print
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($employees)): ?>
        <tr><td colspan="19" class="text-center text-muted py-4">No employees found.</td></tr>
    <?php endif; ?>

        <tr class="small">
            <td colspan="16" class="text-end fw-bold">1st Quincena:</td>
            <td class="text-muted border-start fw-bold">₱<?= number_format($totalFirstQ, 2) ?></td>
            <td colspan="1"></td>
            <td colspan="1"></td>
        </tr>
        <tr class="small">
            <td colspan="16" class="text-end fw-bold">2nd Quincena:</td>
            <td class="text-muted border-start fw-bold">₱<?= number_format($totalSecondQ, 2) ?></td>
            <td colspan="1"></td>
            <td colspan="1"></td>
        </tr>
        <tr class="fw-bold">
            <td colspan="3" class="text-center">TOTAL</td>
            <td class="text-end"><?= peso($totalSalaryRate) ?></td>
            <td class="text-end border-start"><?= peso($totalRefund) ?></td>
            <td class="text-end border-start"><?= peso($totalGsisPremium) ?></td>
            <td class="text-end"><?= peso($totalGsisPolicy) ?></td>
            <td class="text-end"><?= peso($totalGsisOther) ?></td>
            <td class="text-end border-start"><?= peso($totalPagibigPremium) ?></td>
            <td class="text-end"><?= peso($totalPagibigLoan) ?></td>
            <td class="text-end border-start"><?= peso($totalPhic) ?></td>
            <td class="text-end border-start"><?= peso($totalBankLbp) ?></td>
            <td class="text-end"><?= peso($totalBankMcc) ?></td>
            <td class="text-end"><?= peso($totalBank1stvb) ?></td>
            <td class="text-end border-start"><?= peso($totalWithholdingTax) ?></td>
            <td class="text-end border-start"><?= peso($totalNetPay) ?></td>
            <td class="text-end border-start"><?= peso($totalFirstQ + $totalSecondQ) ?></td>
            <td colspan="1"></td>
            <td colspan="1"></td>
        </tr>
    </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 rounded-top">
                <h5 class="modal-title fw-bold"><i class="fas fa-calculator me-2"></i>Process Payroll</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <form id="processForm" method="post" action="">
                    <?= csrf_field() ?>
                    <div class="bg-light p-4 border-bottom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1">Employee Name</label>
                                <div class="fw-bold text-dark" id="modalEmployeeName"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase mb-1">Designation</label>
                                <div class="fw-bold text-dark" id="modalDesignation"></div>
                            </div>
                            <div class="col-12">
                                <div class="card border-0 bg-white shadow-sm">
                                    <div class="card-body py-3 px-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <label class="form-label text-muted small fw-bold text-uppercase mb-0">Net Pay</label>
                                            </div>
                                            <div class="fw-bold text-success fs-5" id="modalNetPay"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstQuincena" class="form-label text-muted small fw-bold text-uppercase mb-1">1st Quincena</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₱</span>
                                    <input type="number" step="0.01" class="form-control" id="firstQuincena" name="first_quincena" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="secondQuincena" class="form-label text-muted small fw-bold text-uppercase mb-1">2nd Quincena</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₱</span>
                                    <input type="number" step="0.01" class="form-control" id="secondQuincena" name="second_quincena" placeholder="0.00" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="processForm" class="btn btn-primary px-4"><i class="fas fa-check me-1"></i> Process Payroll</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const processButtons = document.querySelectorAll('.process-btn');
    const processForm = document.getElementById('processForm');
    const firstQuincenaInput = document.getElementById('firstQuincena');
    const secondQuincenaInput = document.getElementById('secondQuincena');
    const modalNetPayEl = document.getElementById('modalNetPay');
    let currentNetPay = 0;

    processButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const employeeName = btn.getAttribute('data-employee-name');
            const designation = btn.getAttribute('data-designation');
            const netPay = parseFloat(btn.getAttribute('data-net-pay')) || 0;
            const existingFirstQ = parseFloat(btn.getAttribute('data-first-quincena')) || 0;
            const existingSecondQ = parseFloat(btn.getAttribute('data-second-quincena')) || 0;

            currentNetPay = netPay;

            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('modalDesignation').textContent = designation;
            modalNetPayEl.textContent = '₱' + netPay.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            processForm.action = '/payroll/process/' + btn.getAttribute('data-employee-id');

            if (existingFirstQ > 0 && existingSecondQ > 0) {
                firstQuincenaInput.value = existingFirstQ.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                secondQuincenaInput.value = existingSecondQ.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                firstQuincenaInput.value = '';
                secondQuincenaInput.value = '';
            }
        });
    });

    processForm.addEventListener('submit', function() {
        firstQuincenaInput.value = firstQuincenaInput.value.replace(/,/g, '');
        secondQuincenaInput.value = secondQuincenaInput.value.replace(/,/g, '');
    });

    firstQuincenaInput.addEventListener('input', function() {
        const firstQ = parseFloat(this.value.replace(/,/g, '')) || 0;
        const secondQ = currentNetPay - firstQ;
        secondQuincenaInput.value = secondQ > 0 ? secondQ.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
    });

    let selectedRow = null;

function selectRow(row) {
    if (selectedRow && selectedRow !== row) {
        selectedRow.classList.remove('row-selected');
        selectedRow.setAttribute('aria-selected', 'false');
    }
    row.classList.add('row-selected');
    row.setAttribute('aria-selected', 'true');
    selectedRow = row;
}

function deselectRow(row) {
    row.classList.remove('row-selected');
    row.setAttribute('aria-selected', 'false');
    if (selectedRow === row) selectedRow = null;
}

    document.querySelectorAll('.payroll-table tbody tr').forEach(function(row, index) {
        const firstCell = row.querySelector('td');
        if (!firstCell) return;
        firstCell.style.cursor = 'pointer';
        firstCell.setAttribute('tabindex', '0');
        firstCell.setAttribute('role', 'button');
        firstCell.setAttribute('aria-label', 'Select row ' + (index + 1));

        firstCell.addEventListener('click', function(e) {
            const currentRow = this.closest('tr');
            if (currentRow.classList.contains('row-selected')) {
                deselectRow(currentRow);
            } else {
                selectRow(currentRow);
            }
        });

        firstCell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const currentRow = this.closest('tr');
                if (currentRow.classList.contains('row-selected')) {
                    deselectRow(currentRow);
                } else {
                    selectRow(currentRow);
                }
            }
        });
    });

    document.querySelectorAll('.payroll-table tbody tr').forEach(function(row) {
        row.addEventListener('dblclick', function(e) {
            if (this.classList.contains('row-selected')) {
                deselectRow(this);
            } else {
                selectRow(this);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>