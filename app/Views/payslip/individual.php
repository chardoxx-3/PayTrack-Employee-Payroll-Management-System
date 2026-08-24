<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
function peso($value) {
    return $value > 0 ? '₱' . number_format($value, 2) : '';
}
function fmt($value) {
    $value = (float)($value ?? 0);
    return $value != 0 ? number_format($value, 2) : '';
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
<style>
    * { font-family: 'Arial', sans-serif !important; }
    body { background: #fff; margin: 0; padding: 0; }
    .payslip-header { text-align: center; margin-bottom: 10px; }
    .payslip-header h3 { margin: 0; font-size: 16px; font-weight: bold; text-transform: uppercase; }
    .payslip-header p { margin: 2px 0; font-size: 12px; }
    .payslip-section { margin-bottom: 15px; }
    .payslip-section h5 { font-size: 13px; font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #000; padding-bottom: 2px; }
    .payslip-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 12px; }
    .payslip-row.total { font-weight: bold; border-top: 1px solid #000; padding-top: 3px; margin-top: 3px; }
    .payslip-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .payslip-table th, .payslip-table td { border: 1px solid #000; padding: 4px; text-align: left; }
    .payslip-table th { background: #f0f0f0; font-weight: bold; text-align: center; }
    .text-end { text-align: right !important; }
    .text-center { text-align: center !important; }
    .fw-bold { font-weight: bold; }
    .mt-2 { margin-top: 10px; }
    .mt-3 { margin-top: 15px; }
    .mb-2 { margin-bottom: 10px; }
    .mb-3 { margin-bottom: 15px; }
    .border-bottom { border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
    .signature-box { border: 1px solid #000; padding: 10px; text-align: center; min-height: 80px; }
    .signature-box p { margin: 3px 0; font-size: 11px; }
    @media print {
        .no-print { display: none !important; }
        body { background: #fff; }
        .payslip-container { box-shadow: none !important; border: none !important; }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h5 class="fw-bold mb-0">Payslip — <?= esc($employee['full_name'] ?? '') ?></h5>
            <p class="text-muted small mb-0">Period: <?= esc($period_label ?? date('F Y')) ?></p>
        </div>
        <div>
            <button type="button" class="btn btn-success btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="/payroll" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="payslip-container mx-auto bg-white p-4" style="max-width: 850px; border: 1px solid #ddd;">
        <div class="payslip-header">
            <h3>LGU - MAHINOG</h3>
            <p>MUNICIPAL PAYROLL</p>
            <p>We hereby acknowledge to have received from <strong class="text-decoration-underline">MARY LUSSEL S. PACTO</strong>. Treasurer of <strong class="text-decoration-underline">Mahinog, Camiguin</strong> the sums herein specified opposite our respective names, the same, being full compensation for our services rendered during the period stated below, to the correctness of which we hereby severally certify.</p>
        </div>

        <div class="payslip-section">
            <div class="row">
                <div class="col-6">
                    <div class="payslip-row"><span>Name:</span><span class="fw-bold"><?= esc($employee['full_name'] ?? '') ?></span></div>
                    <div class="payslip-row"><span>Office:</span><span><?= esc($office_name ?? '') ?></span></div>
                </div>
                <div class="col-6 text-end">
                    <div class="payslip-row"><span>Period of Service:</span><span><?= esc($period_label ?? date('F Y')) ?></span></div>
                    <div class="payslip-row"><span>Cut-off:</span><span><?= esc($cut_off ?? '') ?></span></div>
                </div>
            </div>
        </div>

        <div class="payslip-section">
            <h5>EARNINGS</h5>
            <div class="payslip-row"><span>Monthly Rate</span><span class="fw-bold"><?= peso($employee['salary_rate'] ?? 0) ?></span></div>
            <div class="payslip-row"><span>Refund / Rata / Pera / ACA Differential</span><span><?= peso($employee['refund_rata'] ?? 0) ?></span></div>
            <div class="payslip-row total"><span>NET PAY FOR THE MONTH</span><span class="fw-bold"><?= peso($net_pay ?? 0) ?></span></div>
        </div>

        <div class="payslip-section">
            <h5>DEDUCTIONS</h5>
            <table class="payslip-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Deduction</th>
                        <th style="width: 40%;">Description</th>
                        <th class="text-end" style="width: 35%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" class="fw-bold">GSIS</td>
                    </tr>
                    <tr>
                        <td class="text-center">Premium (Personal) OULI diff</td>
                        <td>GSIS Premium</td>
                        <td class="text-end"><?= peso($employee['gsis_premium'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">Conso Policy MPL</td>
                        <td>Consolidation Policy / MPL</td>
                        <td class="text-end"><?= peso($employee['gsis_policy'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">GFAL EMRGYLN MPL LITE CPL</td>
                        <td>GFAL / Emergency Loan / MPL Lite / CPL</td>
                        <td class="text-end"><?= peso($employee['gsis_other'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="fw-bold">PAG-IBIG</td>
                    </tr>
                    <tr>
                        <td class="text-center">PREMIUM (Personal)</td>
                        <td>Pag-IBIG Premium</td>
                        <td class="text-end"><?= peso($employee['pagibig_premium'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">SALARY CALAMITY MP2</td>
                        <td>Salary Loan / Calamity Loan / MP2</td>
                        <td class="text-end"><?= peso($employee['pagibig_loan'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="fw-bold">BANKS / COOP'S</td>
                    </tr>
                    <tr>
                        <td class="text-center">LBP Other Payables</td>
                        <td>LBP / Other Payables</td>
                        <td class="text-end"><?= peso($employee['bank_lbp'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">MCC (over)</td>
                        <td>MCC (Over)</td>
                        <td class="text-end"><?= peso($employee['bank_mcc'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">1stVB RBT</td>
                        <td>1stVB / RBT</td>
                        <td class="text-end"><?= peso($employee['bank_1stvb'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="fw-bold">OTHER DEDUCTIONS</td>
                    </tr>
                    <tr>
                        <td class="text-center">PHIC PHIC-Diff</td>
                        <td>PHIC / PHIC Differential</td>
                        <td class="text-end"><?= peso(phicTotal($employee)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">BIR W/T TAX</td>
                        <td>BIR Withholding Tax</td>
                        <td class="text-end"><?= peso($employee['withholding_tax'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">LOANS</td>
                        <td>Other Loans</td>
                        <td class="text-end"><?= peso($employee['loans'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">GOVERNMENT CONT</td>
                        <td>Government Contribution</td>
                        <td class="text-end"><?= peso($employee['government_cont'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <td class="text-center">OTHER DEDUCT</td>
                        <td>Other Deductions</td>
                        <td class="text-end"><?= peso($employee['other_deduct'] ?? 0) ?></td>
                    </tr>
                    <tr class="total">
                        <td colspan="2">Total Deductions</td>
                        <td class="text-end"><?= peso(employeeDeductions($employee)) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="payslip-section">
            <div class="row">
                <div class="col-6">
                    <div class="payslip-row"><span>1st Half</span><span class="fw-bold"><?= peso($employee['first_quincena'] ?? 0) ?></span></div>
                </div>
                <div class="col-6 text-end">
                    <div class="payslip-row"><span>2nd Half</span><span class="fw-bold"><?= peso($employee['second_quincena'] ?? 0) ?></span></div>
                </div>
            </div>
        </div>

        <div class="payslip-section mt-3">
            <div class="row">
                <div class="col-6">
                    <div class="signature-box">
                        <p class="fw-bold">REY LAWRENCE K. TAN</p>
                        <p>Municipal Mayor</p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="signature-box">
                        <p class="fw-bold">MARY LUSSEL S. PACTO</p>
                        <p>Local Treasurer</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 text-center">
            <p class="small text-muted">This is a system-generated payslip. No signature required.</p>
        </div>
    </div>
</div>

<script>
window.onload = function() {
    window.print();
};
window.addEventListener('afterprint', function() {
    window.close();
});
</script>
<?= $this->endSection() ?>
