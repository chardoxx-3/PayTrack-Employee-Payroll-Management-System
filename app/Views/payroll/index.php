<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
.payroll-table thead th {
    background-color: var(--navy-800) !important;
    color: #fff !important;
}

.payroll-table th,
.payroll-table td {
    border: 1px solid #dee2e6 !important;
}

.payroll-table tbody tr:hover {
    background-color: var(--navy-subtle) !important;
}

.row-selected {
    background-color: var(--navy-subtle) !important;
    border-left: 3px solid var(--navy-600) !important;
}

.row-selected td {
    background-color: var(--navy-subtle) !important;
    color: #1e293b !important;
}

.row-selected td small,
.row-selected td .text-muted {
    color: #4a5f7a !important;
}

.row-selected td .badge {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
}

.row-selected td .btn {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
    border-color: var(--navy-600) !important;
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
        $totalDeductions = (float)($emp['total_deductions'] ?? 0);
        $salaryRate = (float)($emp['salary_rate'] ?? 0);
        $refundRata = (float)($emp['refund_rata'] ?? 0);
        
        // Calculate Total Net Pay = (Salary Rate + Refund/Rata) - Total Deductions
        $netPay = ($salaryRate + $refundRata) - $totalDeductions;
        
        // Calculate 1st Quincena & 2nd Quincena (Remaining Net Pay)
        if (isset($emp['first_quincena']) && $emp['first_quincena'] !== null && (float)$emp['first_quincena'] > 0 && (float)$emp['first_quincena'] <= $netPay) {
            $firstQ = (float)$emp['first_quincena'];
            $secondQ = round($netPay - $firstQ, 2);
        } else {
            $firstQ = round($netPay / 2, 2);
            $secondQ = round($netPay - $firstQ, 2);
        }
        
        $totalFirstQ += $firstQ;
        $totalSecondQ += $secondQ;
        $totalSalaryRate += $salaryRate;
        $totalRefund += $refundRata;
        $totalGsisPremium += (float)($emp['gsis_premium'] ?? 0);
        $totalGsisPolicy += (float)($emp['gsis_policy'] ?? 0);
        $totalGsisOther += (float)($emp['gsis_other'] ?? 0);
        $totalPagibigPremium += (float)($emp['pagibig_premium'] ?? 0);
        $totalPagibigLoan += (float)($emp['pagibig_loan'] ?? 0);
        $totalPhic += (float)($emp['phic'] ?? 0);
        $totalBankLbp += (float)($emp['bank_lbp'] ?? 0);
        $totalBankMcc += (float)($emp['bank_mcc'] ?? 0);
        $totalBank1stvb += (float)($emp['bank_1stvb'] ?? 0);
        $totalWithholdingTax += (float)($emp['withholding_tax'] ?? 0);
        $totalNetPay += $secondQ;
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
        <td class="fw-bold text-success border-start"><?= peso($secondQ) ?></td>
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
                                                <label class="form-label text-muted small fw-bold text-uppercase mb-0">Total Net Pay (After Deductions)</label>
                                            </div>
                                            <div class="fw-bold text-success fs-5" id="modalNetPay"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-0">Quincena Breakdown</label>
                            <button type="button" id="splitHalfBtn" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 0.75rem;">
                                <i class="fas fa-divide me-1"></i> Split 50/50 Half
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstQuincena" class="form-label text-muted small fw-bold text-uppercase mb-1">1st Quincena</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₱</span>
                                    <input type="text" inputmode="decimal" class="form-control money-format" id="firstQuincena" name="first_quincena" placeholder="0.00">
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Advance / 1st half pay</small>
                            </div>
                            <div class="col-md-6">
                                <label for="secondQuincena" class="form-label text-muted small fw-bold text-uppercase mb-1">2nd Quincena</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₱</span>
                                    <input type="text" inputmode="decimal" class="form-control money-format" id="secondQuincena" name="second_quincena" placeholder="0.00" readonly>
                                </div>
                                <small class="text-success fw-bold" id="remainingNetPayText" style="font-size: 0.75rem;">Remaining Net Pay: ₱0.00</small>
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
    const splitHalfBtn = document.getElementById('splitHalfBtn');
    const modalNetPayEl = document.getElementById('modalNetPay');
    const remainingTextEl = document.getElementById('remainingNetPayText');
    let currentNetPay = 0;

    function unformatMoney(value) {
        return (value || '').replace(/,/g, '');
    }

    function addCommas(raw) {
        if (raw === '') return '';
        let [intPart, decPart] = raw.split('.');
        intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return decPart !== undefined ? `${intPart}.${decPart}` : intPart;
    }

    function updateRemaining(secondQ) {
        if (remainingTextEl) {
            remainingTextEl.textContent = 'Remaining Net Pay: ₱' + secondQ.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    processButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const employeeName = btn.getAttribute('data-employee-name');
            const designation = btn.getAttribute('data-designation');
            const netPay = parseFloat(btn.getAttribute('data-net-pay')) || 0;
            const existingFirstQ = parseFloat(btn.getAttribute('data-first-quincena')) || 0;

            currentNetPay = netPay;

            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('modalDesignation').textContent = designation;
            modalNetPayEl.textContent = '₱' + netPay.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            processForm.action = '/payroll/process/' + btn.getAttribute('data-employee-id');

            let firstQ = (existingFirstQ > 0 && existingFirstQ <= netPay) ? existingFirstQ : Math.round((netPay / 2) * 100) / 100;
            let secondQ = Math.max(0, Math.round((netPay - firstQ) * 100) / 100);

            firstQuincenaInput.value = addCommas(firstQ.toFixed(2));
            secondQuincenaInput.value = addCommas(secondQ.toFixed(2));
            updateRemaining(secondQ);
        });
    });

    if (splitHalfBtn) {
        splitHalfBtn.addEventListener('click', function() {
            const half = Math.round((currentNetPay / 2) * 100) / 100;
            const secondQ = Math.max(0, Math.round((currentNetPay - half) * 100) / 100);
            firstQuincenaInput.value = addCommas(half.toFixed(2));
            secondQuincenaInput.value = addCommas(secondQ.toFixed(2));
            updateRemaining(secondQ);
        });
    }

    function recalculateFromFirstQ() {
        let raw = unformatMoney(firstQuincenaInput.value).replace(/[^0-9.]/g, '');
        const firstDot = raw.indexOf('.');
        if (firstDot !== -1) {
            raw = raw.slice(0, firstDot + 1) + raw.slice(firstDot + 1).replace(/\./g, '');
        }
        firstQuincenaInput.value = addCommas(raw);
        const firstQ = parseFloat(unformatMoney(firstQuincenaInput.value)) || 0;
        const secondQ = Math.max(0, Math.round((currentNetPay - firstQ) * 100) / 100);
        secondQuincenaInput.value = addCommas(secondQ.toFixed(2));
        updateRemaining(secondQ);
    }

    firstQuincenaInput.addEventListener('input', recalculateFromFirstQ);

    firstQuincenaInput.addEventListener('focus', function() {
        const raw = unformatMoney(this.value);
        if (raw === '' || parseFloat(raw) === 0) {
            this.value = '';
        }
    });

    firstQuincenaInput.addEventListener('blur', function() {
        const raw = unformatMoney(this.value);
        let firstQ = parseFloat(raw);
        if (isNaN(firstQ) || firstQ < 0) {
            firstQ = 0;
        } else if (firstQ > currentNetPay) {
            firstQ = currentNetPay;
        }
        this.value = addCommas(firstQ.toFixed(2));
        const secondQ = Math.max(0, Math.round((currentNetPay - firstQ) * 100) / 100);
        secondQuincenaInput.value = addCommas(secondQ.toFixed(2));
        updateRemaining(secondQ);
    });

    processForm.addEventListener('submit', function() {
        document.querySelectorAll('#processForm .money-format').forEach(input => {
            input.value = unformatMoney(input.value);
        });
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