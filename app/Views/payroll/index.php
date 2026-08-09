<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Payroll Processing</h4>
<p class="text-muted small mb-1">Select an office to begin processing the period: <strong><?= date('F Y') ?></strong></p>
<p class="text-muted small mb-0">Period of Service: <strong><?= date('m/01/Y') . '-' . date('m/t/Y') ?></strong></p>
        </div>
<div class="d-flex gap-2">
    <a href="/deduction" class="btn btn-outline-dark btn-sm"><i class="fas fa-sliders-h me-1"></i> Manage Deductions</a>
    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-export me-1"></i> Export Excel</button>
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
        <table class="table table-hover bg-white rounded shadow-sm align-middle">
<thead class="bg-light text-muted small fw-bold">
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
    <?php $no = 1; foreach($employees as $emp): ?>
    <?php
        $totalDeductions = $emp['total_deductions'] ?? 0;
        $netPay = $emp['net_pay'] ?? ($emp['salary_rate'] - $totalDeductions);
        $firstQ = $emp['first_quincena'] ?? round($netPay / 2, 2);
        $secondQ = $emp['second_quincena'] ?? round($netPay - $firstQ, 2);
    ?>
    <tr>
        <td rowspan="2" class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
        <td class="ps-4" rowspan="2">
            <div class="fw-bold"><?= $emp['full_name'] ?></div>
            <small class="text-muted"><?= $emp['employee_id'] ?></small>
        </td>
        <td rowspan="2"><?= $emp['position'] ?></td>
        <td rowspan="2">₱<?= number_format($emp['salary_rate'], 2) ?></td>
        <td rowspan="2" class="text-muted small text-end border-start">₱<?= number_format($emp['refund_rata'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end border-start">₱<?= number_format($emp['gsis_premium'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end">₱<?= number_format($emp['gsis_policy'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end">₱<?= number_format($emp['gsis_other'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end border-start">₱<?= number_format($emp['pagibig_premium'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end">₱<?= number_format($emp['pagibig_loan'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end border-start">₱<?= number_format($emp['phic'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end border-start">₱<?= number_format($emp['bank_lbp'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end">₱<?= number_format($emp['bank_mcc'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end">₱<?= number_format($emp['bank_1stvb'] ?? 0, 2) ?></td>
        <td rowspan="2" class="text-danger small text-end border-start">₱<?= number_format($emp['withholding_tax'] ?? 0, 2) ?></td>
        <td rowspan="2" class="fw-bold text-success border-start">₱<?= number_format($netPay, 2) ?></td>
        <td class="text-muted small border-start">1st Q: ₱<?= number_format($firstQ, 2) ?></td>
        <td rowspan="2" class="text-center">
            <?php if (!empty($emp['payroll_id'])): ?>
                <span class="badge bg-soft-success text-success rounded-pill">Processed</span>
            <?php else: ?>
                <span class="badge bg-soft-warning text-warning rounded-pill">Pending</span>
            <?php endif; ?>
        </td>
        <td rowspan="2" class="text-end pe-4 text-nowrap">
            <a href="/payroll/process/<?= $emp['id'] ?>" class="btn btn-primary btn-sm px-3 rounded-pill">Process</a>
        </td>
    </tr>
    <tr class="border-bottom">
        <td class="text-muted small border-start">2nd Q: ₱<?= number_format($secondQ, 2) ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>