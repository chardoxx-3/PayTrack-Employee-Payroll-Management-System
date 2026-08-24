<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Payroll — <?= date('F Y') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans', sans-serif !important; }
        body { background: #fff; margin: 0; padding: 0; }
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
        .payroll-table {
            margin-bottom: 0;
        }
        .text-teal { color: #0d5c4e !important; }
        .print-header-block h4 {
            font-size: 1.1rem;
        }
        .print-header-block p {
            font-size: 0.8rem;
        }
        .print-header-block p.lh-sm {
            line-height: 1.3;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .table-responsive { overflow: visible; }
            .payroll-table thead th {
                background-color: #0d2d27 !important;
                color: #e6f4f1 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            /* Shrink the table so all 18 columns fit on A4 landscape */
            .payroll-table {
                font-size: 0.65rem;
            }
            .payroll-table thead th,
            .payroll-table tbody td {
                padding: 3px 4px !important;
                font-size: 0.65rem;
            }
            .payroll-table thead th {
                font-size: 0.6rem;
            }
            .table-responsive {
                zoom: 0.82;
            }
            @page {
                size: A4 landscape;
                margin: 0.25in;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <?php
                $officeDisplay = 'All Offices';
                if ($office_id) {
                    $officeIndex = array_search($office_id, array_column($offices ?? [], 'id'));
                    if ($officeIndex !== false && isset($offices[$officeIndex]['office_name'])) {
                        $officeDisplay = $offices[$officeIndex]['office_name'];
                    }
                }
            ?>
            <h5 class="fw-bold mb-0">Payroll — <?= $period_label ?> — <?= $officeDisplay ?></h5>
            <p class="text-muted small mb-0">Period of Service: <?= $service_period ?></p>
        </div>
        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print
        </button>
        <small class="text-muted mt-1 d-block no-print">Tip: Uncheck "Headers &amp; footers" and check "Background graphics" in your browser's print dialog. Paper: A4 Landscape.</small>
    </div>

    <!-- Print Header Block -->
    <div class="print-header-block mb-3">
        <div class="text-center mb-2">
            <h4 class="fw-bold mb-0">LGU-MAHINOG</h4>
            <p class="mb-0 small">MUNICIPAL PAYROLL</p>
        </div>
        <div class="text-center mb-3">
            <p class="mb-0 small lh-sm">
                We hereby acknowledge to have received from <strong class="text-decoration-underline">MARY LUSSEL S. PACTO</strong>. Treasurer of <strong class="text-decoration-underline">Mahinog, Camiguin</strong> the sums herein specified opposite our respective names, the same, being full compensation for our services rendered during the period stated below, to the correctness of which we hereby severally certify.
            </p>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="table-responsive">
        <table class="table table-border-bottom-0 payroll-table align-middle">
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
                    <td colspan="16" class="text-end fw-bold">1st Quincena:</td>
                    <td class="text-muted border-start fw-bold"><?= peso(array_sum(array_filter(array_column($employees, 'first_quincena')))) ?></td>
                    <td class="border-start"></td>
                </tr>
                <tr class="small">
                    <td colspan="16" class="text-end fw-bold">2nd Quincena:</td>
                    <td class="text-muted border-start fw-bold"><?= peso(array_sum(array_filter(array_column($employees, 'second_quincena')))) ?></td>
                    <td class="border-start"></td>
                </tr>
                <tr class="fw-bold">
                    <td colspan="3" class="text-center">TOTAL</td>
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
                    <td class="border-start fw-bold"><?= peso(array_sum(array_filter(array_column($employees, 'first_quincena'))) + array_sum(array_filter(array_column($employees, 'second_quincena')))) ?></td>
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
</body>
</html>