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
    .payroll-table tbody tr {
        border: 1px solid #dee2e6 !important;
    }
    @media print {
        body { background: #fff; }
        .no-print { display: none !important; }
        .payroll-table { margin-bottom: 0; }
    }
</style>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h5 class="fw-bold mb-0">Payroll — <?= $period_label ?> — <?= $office_id ? ($offices[array_search($office_id, array_column($offices, 'id'))]['office_name'] ?? 'All Offices' : 'All Offices') ?></h5>
            <p class="text-muted small mb-0">Period of Service: <?= $service_period ?></p>
        </div>
        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover bg-white rounded shadow-sm align-middle payroll-table">
            <thead class="text-muted small fw-bold">
                <tr>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle; width: 40px;">NO.</th>
                    <th rowspan="2" class="ps-4" style="vertical-align: middle;">EMPLOYEE</th>
                    <th rowspan="2" style="vertical-align: middle;">DESIGNATION</th>
                    <th rowspan="2" style="vertical-align: middle;">MONTHLY RATE</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">REFUND</th>
                    <th colspan="3" class="text-center border-start">GSIS</th>
                    <th colspan="2" class="text-center border-start">PAG-IBIG</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">PHIC</th>
                    <th colspan="3" class="text-center border-start">BANKS / COOP'S</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">BIR W/T TAX</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">NET PAY</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">QUINCENA</th>
                    <th rowspan="2" class="text-center border-start" style="vertical-align: middle;">SIGNATURE</th>
                </tr>
                <tr>
                    <th class="text-center">PREMIUM</th>
                    <th class="text-center">CONSO</th>
                    <th class="text-center">GFAL</th>
                    <th class="text-center">PREMIUM</th>
                    <th class="text-center">LOAN</th>
                    <th class="text-center">LBP</th>
                    <th class="text-center">MCC</th>
                    <th class="text-center">1stVB</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($employees as $emp): ?>
                <tr class="border-bottom">
                    <td class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
                    <td class="ps-4">
                        <div class="fw-bold"><?= esc($emp['full_name']) ?></div>
                    </td>
                    <td><?= esc($emp['position']) ?></td>
                    <td class="fw-bold text-teal"><?= number_format($emp['salary_rate'], 2) ?></td>
                    <td class="text-end border-start"><?= peso($emp['refund_rata'] ?? 0) ?></td>
                    <td class="text-end border-start"><?= peso($emp['gsis_premium'] ?? 0) ?></td>
                    <td class="text-end"><?= peso($emp['gsis_policy'] ?? 0) ?></td>
                    <td class="text-end"><?= peso($emp['gsis_other'] ?? 0) ?></td>
                    <td class="text-end border-start"><?= peso($emp['pagibig_premium'] ?? 0) ?></td>
                    <td class="text-end"><?= peso($emp['pagibig_loan'] ?? 0) ?></td>
                    <td class="text-center border-start"><?= peso($emp['phic'] ?? 0) ?></td>
                    <td class="text-end border-start"><?= peso($emp['bank_lbp'] ?? 0) ?></td>
                    <td class="text-end"><?= peso($emp['bank_mcc'] ?? 0) ?></td>
                    <td class="text-end"><?= peso($emp['bank_1stvb'] ?? 0) ?></td>
                    <td class="text-end border-start fw-bold"><?= peso($emp['withholding_tax'] ?? 0) ?></td>
                    <td class="fw-bold text-success border-start"><?= peso($emp['net_pay'] ?? 0) ?></td>
                    <td class="text-center border-start">
                        <?= peso($emp['first_quincena'] ?? 0) ?><br>
                        <small class="text-muted"><?= peso($emp['second_quincena'] ?? 0) ?></small>
                    </td>
                    <td class="border-start text-center"><?= esc($emp['contact_number'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($employees)): ?>
                <tr><td colspan="18" class="text-center text-muted py-4">No employees found.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($employees)): ?>
            <tfoot>
                <tr class="small">
                    <td colspan="3" class="text-center fw-bold">TOTAL</td>
                    <td class="fw-bold"><?= peso(array_sum(array_column($employees, 'salary_rate'))) ?></td>
                    <td class="text-end border-start"><?= peso(array_sum(array_column($employees, 'refund_rata'))) ?></td>
                    <td class="text-end border-start"><?= peso(array_sum(array_filter(array_column($employees, 'gsis_premium')))) ?></td>
                    <td class="text-end"><?= peso(array_sum(array_filter(array_column($employees, 'gsis_policy')))) ?></td>
                    <td class="text-end"><?= peso(array_sum(array_filter(array_column($employees, 'gsis_other')))) ?></td>
                    <td class="text-end border-start"><?= peso(array_sum(array_filter(array_column($employees, 'pagibig_premium')))) ?></td>
                    <td class="text-end"><?= peso(array_sum(array_filter(array_column($employees, 'pagibig_loan')))) ?></td>
                    <td class="text-center border-start"><?= peso(array_sum(array_filter(array_column($employees, 'phic')))) ?></td>
                    <td class="text-end border-start"><?= peso(array_sum(array_filter(array_column($employees, 'bank_lbp')))) ?></td>
                    <td class="text-end"><?= peso(array_sum(array_filter(array_column($employees, 'bank_mcc')))) ?></td>
                    <td class="text-end"><?= peso(array_sum(array_filter(array_column($employees, 'bank_1stvb')))) ?></td>
                    <td class="text-end border-start fw-bold"><?= peso(array_sum(array_filter(array_column($employees, 'withholding_tax')))) ?></td>
                    <td class="fw-bold text-success border-start"><?= peso($total_net) ?></td>
                    <td class="border-start"><?= peso(array_sum(array_filter(array_column($employees, 'first_quincena'))) + array_sum(array_filter(array_column($employees, 'second_quincena')))) ?></td>
                    <td class="border-start"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
};
</script>

<?= $this->endSection() ?>
