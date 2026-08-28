<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
?>
<style>
.deduction-table thead th {
    background-color: var(--navy-800) !important;
    color: #fff !important;
}

.deduction-table th,
.deduction-table td {
    border: 1px solid #dee2e6 !important;
}

.deduction-table tbody tr:hover {
    background-color: var(--navy-subtle) !important;
}

.deduction-table .row-selected {
    background-color: var(--navy-subtle) !important;
    border-left: 3px solid var(--navy-600) !important;
}

.deduction-table .row-selected td {
    background-color: var(--navy-subtle) !important;
    color: #1e293b !important;
}

.deduction-table .row-selected td small,
.deduction-table .row-selected td .text-muted {
    color: #4a5f7a !important;
}

.deduction-table .row-selected td .badge {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
}

.deduction-table .row-selected td .btn {
    color: #ffffff !important;
    background-color: var(--navy-600) !important;
    border-color: var(--navy-600) !important;
}
</style>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Deduction Management</h4>
            <p class="text-muted small mb-1">Select an office to manage deductions for the period: <strong><?= date('F Y') ?></strong></p>
            <p class="text-muted small mb-0">Period of Service: <strong><?= date('m/01/Y') . '-' . date('m/t/Y') ?></strong></p>
        </div>
        <a href="/payroll" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i> Back to Payroll</a>
    </div>

    <div class="card border-0 shadow-sm p-3 mb-4 bg-light">
        <form action="/deduction" method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="office_id" class="form-select border-0 shadow-sm" onchange="this.form.submit()">
                    <option value="">Select Office Assignment...</option>
                    <?php foreach ($offices as $office): ?>
                        <option value="<?= $office['id'] ?>" <?= ($office_id == $office['id']) ? 'selected' : '' ?>>
                            <?= esc($office['office_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0" 
                           placeholder="Search employee ID or name..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Search</button>
            </div>
        </form>
    </div>

<div class="table-responsive">
    <table class="table table-hover bg-white rounded shadow-sm align-middle deduction-table">
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
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 90px;">BIR W/T TAX</th>
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
            foreach ($records as $r):
            ?>
            <?php
                $totalDeductions = ($r['gsis_premium'] ?? 0) + ($r['gsis_policy'] ?? 0) + ($r['gsis_other'] ?? 0)
                                 + ($r['pagibig_premium'] ?? 0) + ($r['pagibig_loan'] ?? 0)
                                 + ($r['phic'] ?? 0)
                                 + ($r['bank_lbp'] ?? 0) + ($r['bank_mcc'] ?? 0) + ($r['bank_1stvb'] ?? 0)
                                 + ($r['withholding_tax'] ?? 0);
                $netPay  = $r['net_pay'] ?? (($r['salary_rate'] ?? 0) - $totalDeductions);
                $firstQ  = $r['first_quincena'] ?? round($netPay / 2, 2);
                $secondQ = $r['second_quincena'] ?? round($netPay - $firstQ, 2);
                $totalFirstQ += $firstQ;
                $totalSecondQ += $secondQ;
                $totalSalaryRate += $r['salary_rate'] ?? 0;
                $totalRefund += $r['refund_rata'] ?? 0;
                $totalGsisPremium += $r['gsis_premium'] ?? 0;
                $totalGsisPolicy += $r['gsis_policy'] ?? 0;
                $totalGsisOther += $r['gsis_other'] ?? 0;
                $totalPagibigPremium += $r['pagibig_premium'] ?? 0;
                $totalPagibigLoan += $r['pagibig_loan'] ?? 0;
                $totalPhic += $r['phic'] ?? 0;
                $totalBankLbp += $r['bank_lbp'] ?? 0;
                $totalBankMcc += $r['bank_mcc'] ?? 0;
                $totalBank1stvb += $r['bank_1stvb'] ?? 0;
                $totalWithholdingTax += $r['withholding_tax'] ?? 0;
                $totalNetPay += $netPay;
            ?>
    <tr class="border-bottom">
        <td class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
        <td class="ps-4">
<div class="fw-bold"><?= esc($r['full_name']) ?></div>
        </td>
                <td><?= esc($r['position']) ?></td>
                <td><?= peso($r['salary_rate'] ?? 0) ?></td>
                <td class="text-muted small text-end border-start"><?= peso($r['refund_rata'] ?? 0) ?></td>
                <td class="text-danger small text-end border-start"><?= peso($r['gsis_premium'] ?? 0) ?></td>
                <td class="text-danger small text-end"><?= peso($r['gsis_policy'] ?? 0) ?></td>
                <td class="text-danger small text-end"><?= peso($r['gsis_other'] ?? 0) ?></td>
                <td class="text-danger small text-end border-start"><?= peso($r['pagibig_premium'] ?? 0) ?></td>
                <td class="text-danger small text-end"><?= peso($r['pagibig_loan'] ?? 0) ?></td>
                <td class="text-danger small text-end border-start"><?= peso($r['phic'] ?? 0) ?></td>
                <td class="text-danger small text-end border-start"><?= peso($r['bank_lbp'] ?? 0) ?></td>
                <td class="text-danger small text-end"><?= peso($r['bank_mcc'] ?? 0) ?></td>
                <td class="text-danger small text-end"><?= peso($r['bank_1stvb'] ?? 0) ?></td>
                <td class="text-danger small text-end border-start"><?= peso($r['withholding_tax'] ?? 0) ?></td>
                <td class="fw-bold text-success border-start"><?= peso($netPay) ?></td>
                <td class="text-muted small border-start">1st Q: <?= peso($firstQ) ?><br>2nd Q: <?= peso($secondQ) ?></td>
                <td class="text-muted small border-start text-end"><?= esc($r['contact_number'] ?? '') ?></td>
                <td class="text-end pe-4 text-nowrap">
                    <a href="/deduction/manage/<?= $r['id'] ?>" class="btn btn-success btn-sm px-3 rounded-pill">Edit</a>
                </td>
    </tr>
            <?php endforeach; ?>
            <?php if (empty($records)): ?>
            <tr><td colspan="19" class="text-center text-muted py-4">No employees found.</td></tr>
            <?php endif; ?>

            <tr class="small">
                <td colspan="16" class="text-end fw-bold">1st Quincena:</td>
                <td class="text-muted border-start fw-bold">₱<?= number_format($totalFirstQ, 2) ?></td>
                <td colspan="1"></td>
            </tr>
            <tr class="small">
                <td colspan="16" class="text-end fw-bold">2nd Quincena:</td>
                <td class="text-muted border-start fw-bold">₱<?= number_format($totalSecondQ, 2) ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    document.querySelectorAll('.deduction-table tbody tr').forEach(function(row, index) {
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

    document.querySelectorAll('.deduction-table tbody tr').forEach(function(row) {
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