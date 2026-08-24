<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
function safeOfficeName($office_id, $offices) {
    if (!$office_id) return 'All Offices';
    $idx = array_search($office_id, array_column($offices ?? [], 'id'));
    return ($idx !== false && isset($offices[$idx]['office_name'])) ? $offices[$idx]['office_name'] : 'All Offices';
}
function sumCol($arr, $col, $extra = 0) {
    return array_sum(array_filter(array_column($arr, $col))) + $extra;
}
function gsisTotal($emp) {
    return ($emp['gsis_premium'] ?? 0) + ($emp['gsis_policy'] ?? 0) + ($emp['gsis_other'] ?? 0)
         + ($emp['gsis_ouli'] ?? 0) + ($emp['gsis_diff'] ?? 0);
}
function pagibigTotal($emp) {
    return ($emp['pagibig_premium'] ?? 0) + ($emp['pagibig_loan'] ?? 0) + ($emp['pagibig_mp2'] ?? 0);
}
function phicTotal($emp) {
    return ($emp['phic'] ?? 0) + ($emp['phic_diff'] ?? 0);
}
function employeeDeductions($emp) {
    $t = 0;
    foreach (['gsis_premium','gsis_policy','gsis_other','gsis_ouli','gsis_diff',
             'pagibig_premium','pagibig_loan','pagibig_mp2','phic','phic_diff',
             'withholding_tax','loans','government_cont','other_deduct',
             'bank_lbp','bank_other_payables','bank_mcc','bank_1stvb','bank_rbt'] as $f) {
        $t += (float)($emp[$f] ?? 0);
    }
    return $t;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Payroll — <?= esc($period_label ?? date('F Y')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            .print-footer p {
                font-size: 0.65rem !important;
            }
            .print-footer .fw-bold {
                font-size: 0.75rem !important;
            }
            .page-break {
                break-before: page;
                page-break-before: always;
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
            <h5 class="fw-bold mb-0">Payroll — <?= esc($period_label ?? date('F Y')) ?> — <?= safeOfficeName($office_id ?? null, $offices ?? []) ?></h5>
            <p class="text-muted small mb-0">Period of Service: <?= esc($service_period ?? '') ?></p>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="<?= '/payroll/export' . ($office_id ? '?office_id=' . $office_id : '') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>
            <a href="/payroll" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <small class="text-muted mt-1 d-block">Tip: Uncheck "Headers &amp; footers" and check "Background graphics". Paper: A4 Landscape.</small>
        </div>
    </div>

    <?php
    $chunks = array_chunk($employees ?? [], 10);
    $pageNum = 1;
    $totalPages = count($chunks);
    ?>

    <?php foreach ($chunks as $chunkIdx => $employeesChunk): ?>
    <?php if ($chunkIdx > 0): ?>
    <div class="page-break"></div>
    <?php endif; ?>

    <!-- Print Header Block -->
    <div class="print-header-block mb-2">
        <div class="text-center mb-1">
            <h4 class="fw-bold mb-0">LGU-MAHINOG</h4>
            <p class="mb-0 small">MUNICIPAL PAYROLL</p>
        </div>
        <div class="text-center mb-2">
            <p class="small lh-sm mb-0">
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
                <?php $no = $chunkIdx * 10 + 1; foreach($employeesChunk as $emp): ?>
                <?php
                    $tDeductions = employeeDeductions($emp);
                    $tNetPay = ($emp['net_pay'] ?? (($emp['gross_pay'] ?? $emp['salary_rate']) - ($emp['total_deductions'] ?? $tDeductions)));
                ?>
                <tr class="border-bottom">
                    <td class="align-middle text-center" style="width: 40px;"><?= $no++ ?></td>
                    <td class="ps-4">
                        <div class="fw-bold"><?= esc($emp['full_name']) ?></div>
                        <small class="text-muted"><?= $emp['employee_id'] ?></small>
                    </td>
                    <td><?= esc($emp['position'] ?? '-') ?></td>
                    <td class="fw-bold text-teal"><?= number_format($emp['salary_rate'], 2) ?></td>
                    <td class="text-end border-start"><?= number_format($emp['refund_rata'] ?? 0, 2) ?></td>
                    <td class="text-end border-start"><?= number_format($emp['gsis_premium'] ?? 0, 2) ?></td>
                    <td class="text-end"><?= number_format($emp['gsis_policy'] ?? 0, 2) ?></td>
                    <td class="text-end"><?= number_format($emp['gsis_other'] ?? 0, 2) ?></td>
                    <td class="text-end border-start"><?= number_format($emp['pagibig_premium'] ?? 0, 2) ?></td>
                    <td class="text-end"><?= number_format($emp['pagibig_loan'] ?? 0, 2) ?></td>
                    <td class="text-center border-start"><?= number_format($emp['phic'] ?? 0, 2) ?></td>
                    <td class="text-end border-start"><?= number_format($emp['bank_lbp'] ?? 0, 2) ?></td>
                    <td class="text-end"><?= number_format($emp['bank_mcc'] ?? 0, 2) ?></td>
                    <td class="text-end"><?= number_format($emp['bank_1stvb'] ?? 0, 2) ?></td>
                    <td class="text-end border-start fw-bold"><?= number_format($emp['withholding_tax'] ?? 0, 2) ?></td>
                    <td class="fw-bold text-success border-start"><?= number_format($tNetPay, 2) ?></td>
                    <td class="text-center border-start">
                        <?= peso($emp['first_quincena'] ?? 0) ?><br>
                        <small class="text-muted"><?= peso($emp['second_quincena'] ?? 0) ?></small>
                    </td>
                    <td class="border-start text-center"><?= esc($emp['contact_number'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if (!empty($employees)): ?>
            <tfoot>
                <tr class="small">
                    <td colspan="16" class="text-end fw-bold">1st Quincena:</td>
                    <td class="text-muted border-start fw-bold"><?= peso(sumCol($employeesChunk, 'first_quincena')) ?></td>
                    <td class="border-start"></td>
                </tr>
                <tr class="small">
                    <td colspan="16" class="text-end fw-bold">2nd Quincena:</td>
                    <td class="text-muted border-start fw-bold"><?= peso(sumCol($employeesChunk, 'second_quincena')) ?></td>
                    <td class="border-start"></td>
                </tr>
                <tr class="fw-bold">
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="fw-bold"><?= number_format(sumCol($employeesChunk, 'salary_rate'), 2) ?></td>
                    <td class="text-end border-start"><?= number_format(sumCol($employeesChunk, 'refund_rata'), 2) ?></td>
                    <td class="text-end border-start"><?= number_format(sumCol($employeesChunk, 'gsis_premium'), 2) ?></td>
                    <td class="text-end"><?= number_format(sumCol($employeesChunk, 'gsis_policy'), 2) ?></td>
                    <td class="text-end"><?= number_format(sumCol($employeesChunk, 'gsis_other'), 2) ?></td>
                    <td class="text-end border-start"><?= number_format(sumCol($employeesChunk, 'pagibig_premium'), 2) ?></td>
                    <td class="text-end"><?= number_format(sumCol($employeesChunk, 'pagibig_loan'), 2) ?></td>
                    <td class="text-center border-start"><?= number_format(sumCol($employeesChunk, 'phic'), 2) ?></td>
                    <td class="text-end border-start"><?= number_format(sumCol($employeesChunk, 'bank_lbp'), 2) ?></td>
                    <td class="text-end"><?= number_format(sumCol($employeesChunk, 'bank_mcc'), 2) ?></td>
                    <td class="text-end"><?= number_format(sumCol($employeesChunk, 'bank_1stvb'), 2) ?></td>
                    <td class="text-end border-start fw-bold"><?= number_format(sumCol($employeesChunk, 'withholding_tax'), 2) ?></td>
                    <td class="fw-bold text-success border-start"><?= peso($total_net) ?></td>
                    <td class="border-start fw-bold"><?= peso(sumCol($employeesChunk, 'first_quincena') + sumCol($employeesChunk, 'second_quincena')) ?></td>
                    <td class="border-start"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>

    <!-- Certification / Approval Footer -->
    <div class="print-footer mt-3" style="<?= !empty($employees) ? '' : 'display:none;' ?>">
        <div style="display: flex; width: 100%; gap: 1rem;">
            <!-- Left: Statement (1) -->
            <div style="flex: 1; text-align: center;">
                <p class="small lh-sm mb-2">
                    <strong>(1)</strong> I HEREBY CERTIFY on my official oath that the above PAYROLL is correct, and that services above stated have been duly rendered. Payment for such services is also hereby approved from the appropriation indicated.
                </p>
                <p class="fw-bold mb-0">REY LAWRENCE K. TAN</p>
                <p class="small text-muted mb-0">Municipal Mayor</p>
            </div>
            <!-- Center: Statement (4) APPROVED -->
            <div style="flex: 1; text-align: center;">
                <p class="small mb-2"><strong>(4) APPROVED:</strong></p>
                <p class="fw-bold mb-0">REY LAWRENCE K. TAN</p>
                <p class="small text-muted mb-0">Municipal Mayor</p>
            </div>
            <!-- Right: Statement (5) -->
            <div style="flex: 1; text-align: center;">
                <p class="small lh-sm mb-2">
                    <strong>(5)</strong> I HEREBY CERTIFY on my official oath that I have paid in cash to each official and employee whose name appears on the above roll the amount set opposite his name, under column 17, the having signed or marked his name under column 20 above, in my presence and at the time that payment was made to him in acknowledgement of receipt of the money paid him.
                </p>
                <p class="fw-bold mb-0">MARY LUSSEL S. PACTO</p>
                <p class="small text-muted mb-0">Disbursing Officer</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-2 no-print">
        <small class="text-muted">Page <?= $pageNum++ ?> of <?= $totalPages ?></small>
    </div>
    <?php endforeach; ?>

    <?php if (empty($employees)): ?>
    <div class="text-center py-4">
        <p class="text-muted">No employees with payroll data for this period.</p>
    </div>
    <?php endif; ?>
</div>

<script>
window.onload = function() {
    window.print();
};
</script>
</body>
</html>
