<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
?>
<style>
.deduction-table thead th {
    background-color: #e8f5e9 !important;
    color: #1e293b !important;
}

.deduction-table th,
.deduction-table td {
    border: 1px solid #dee2e6 !important;
}

.deduction-table tbody tr:hover {
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
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>

<div class="table-responsive">
    <table class="table table-hover bg-white rounded shadow-sm align-middle deduction-table">
        <thead class="text-muted small fw-bold">
            <tr>
                <th rowspan="2" class="text-center" style="vertical-align: middle; width: 40px;">NO.</th>
                <th class="ps-4" rowspan="2" style="vertical-align: middle;">EMPLOYEE</th>
                <th rowspan="2" style="vertical-align: middle;">DESIGNATION</th>
                <th rowspan="2" style="vertical-align: middle;">MONTHLY RATE</th>
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">REFUND</th>
                <th colspan="3" class="text-center border-start">GSIS</th>
                <th colspan="2" class="text-center border-start">PAG-IBIG</th>
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">PHIC</th>
                <th colspan="3" class="text-center border-start">BANKS / COOP'S</th>
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">BIR TAX</th>
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">NET PAY</th>
                <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">QUINCENA</th>
                <th rowspan="2" class="text-center" style="vertical-align: middle;">STATUS</th>
                <th rowspan="2" class="text-end pe-4" style="vertical-align: middle;">ACTION</th>
            </tr>
            <tr>
                <th class="text-center small border-start">Premium</th>
                <th class="text-center small">Conso/MPL</th>
                <th class="text-center small">GFAL/Other</th>
                <th class="text-center small border-start">Premium</th>
                <th class="text-center small">Loan/MP2</th>
                <th class="text-center small border-start">LBP</th>
                <th class="text-center small">MCC</th>
                <th class="text-center small">1stVB</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($records as $r): ?>
            <?php
                $totalDeductions = ($r['gsis_premium'] ?? 0) + ($r['gsis_policy'] ?? 0) + ($r['gsis_other'] ?? 0)
                                 + ($r['pagibig_premium'] ?? 0) + ($r['pagibig_loan'] ?? 0)
                                 + ($r['phic'] ?? 0)
                                 + ($r['bank_lbp'] ?? 0) + ($r['bank_mcc'] ?? 0) + ($r['bank_1stvb'] ?? 0)
                                 + ($r['withholding_tax'] ?? 0);
                $netPay  = $r['net_pay'] ?? (($r['salary_rate'] ?? 0) - $totalDeductions);
                $firstQ  = $r['first_quincena'] ?? round($netPay / 2, 2);
                $secondQ = $r['second_quincena'] ?? round($netPay - $firstQ, 2);
            ?>
    <tr class="border-bottom">
        <td rowspan="2" class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
                <td class="ps-4" rowspan="2">
<div class="fw-bold"><?= esc($r['full_name']) ?></div>
<small class="text-muted"><?= esc($r['employee_id']) ?></small>
                </td>
                <td rowspan="2"><?= esc($r['position']) ?></td>
                <td rowspan="2"><?= peso($r['salary_rate'] ?? 0) ?></td>
                <td rowspan="2" class="text-muted small text-end border-start"><?= peso($r['refund_rata'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end border-start"><?= peso($r['gsis_premium'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end"><?= peso($r['gsis_policy'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end"><?= peso($r['gsis_other'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end border-start"><?= peso($r['pagibig_premium'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end"><?= peso($r['pagibig_loan'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end border-start"><?= peso($r['phic'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end border-start"><?= peso($r['bank_lbp'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end"><?= peso($r['bank_mcc'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end"><?= peso($r['bank_1stvb'] ?? 0) ?></td>
                <td rowspan="2" class="text-danger small text-end border-start"><?= peso($r['withholding_tax'] ?? 0) ?></td>
                <td rowspan="2" class="fw-bold text-success border-start"><?= peso($netPay) ?></td>
                <td class="text-muted small border-start">1st Q: <?= peso($firstQ) ?></td>
                <td rowspan="2" class="text-center">
                    <?php if (!empty($r['payroll_id'])): ?>
                        <span class="badge bg-soft-success text-success rounded-pill">Processed</span>
                    <?php else: ?>
                        <span class="badge bg-soft-warning text-warning rounded-pill">Pending</span>
                    <?php endif; ?>
                </td>
                <td rowspan="2" class="text-end pe-4 text-nowrap">
                    <a href="/deduction/manage/<?= $r['id'] ?>" class="btn btn-primary btn-sm px-3 rounded-pill">Edit</a>
                </td>
            </tr>
            <tr class="border-bottom">
                <td class="text-muted small border-start">2nd Q: <?= peso($secondQ) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($records)): ?>
            <tr><td colspan="19" class="text-center text-muted py-4">No employees found.</td></tr>
            <?php endif; ?>
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
        if (selectedRow.nextElementSibling) {
            selectedRow.nextElementSibling.classList.remove('row-selected');
        }
    }
    row.classList.add('row-selected');
    row.setAttribute('aria-selected', 'true');
    if (row.nextElementSibling) {
        row.nextElementSibling.classList.add('row-selected');
    }
    selectedRow = row;
}

function deselectRow(row) {
    row.classList.remove('row-selected');
    row.setAttribute('aria-selected', 'false');
    if (row.nextElementSibling) {
        row.nextElementSibling.classList.remove('row-selected');
    }
    if (selectedRow === row) selectedRow = null;
}

    document.querySelectorAll('.deduction-table tbody td[rowspan]').forEach(function(noCell) {
        noCell.style.cursor = 'pointer';
        noCell.setAttribute('tabindex', '0');
        noCell.setAttribute('role', 'button');
        noCell.setAttribute('aria-label', 'Select row ' + noCell.textContent.trim());

        noCell.addEventListener('click', function(e) {
            const row = this.closest('tr');
            if (row.classList.contains('row-selected')) {
                deselectRow(row);
            } else {
                selectRow(row);
            }
        });

        noCell.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const row = this.closest('tr');
                if (row.classList.contains('row-selected')) {
                    deselectRow(row);
                } else {
                    selectRow(row);
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