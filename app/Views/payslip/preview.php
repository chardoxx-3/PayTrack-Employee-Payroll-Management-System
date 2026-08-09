<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="text-center mb-4 d-print-none">
        <button onclick="window.print()" class="btn btn-dark px-4 me-2"><i class="fas fa-print me-2"></i> Print Payslip</button>
        <a href="/payslip" class="btn btn-light border px-4">Back to Search</a>
    </div>

    <!-- Payslip Paper Design -->
    <div class="payslip-card mx-auto bg-white p-5 shadow-lg" style="max-width: 800px; border-top: 5px solid #2563eb;">
        <div class="row mb-4">
            <div class="col-6">
                <h4 class="fw-bold text-primary mb-0">COMPANY NAME</h4>
                <p class="small text-muted">Payroll Management System</p>
            </div>
            <div class="col-6 text-end">
                <h5 class="fw-bold mb-0">PAY ADVICE</h5>
                <p class="text-muted">Period: <?= $payslip['payroll_period'] ?></p>
            </div>
        </div>

        <div class="row mb-5 border-bottom pb-3">
            <div class="col-6">
                <small class="text-muted d-block">EMPLOYEE NAME</small>
                <span class="fw-bold text-uppercase"><?= $payslip['full_name'] ?></span>
            </div>
            <div class="col-6 text-end">
                <small class="text-muted d-block">DESIGNATION</small>
                <span class="fw-bold"><?= $payslip['position'] ?></span>
            </div>
        </div>

<div class="row mb-4">
            <div class="col-md-6 border-end">
                <h6 class="fw-bold text-success mb-3 border-bottom pb-2">EARNINGS</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Monthly Rate of Pay</span>
                    <span>₱<?= number_format($payslip['gross_pay'], 2) ?></span>
                </div>
                <?php if (!empty($payslip['refund_rata'])): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>Refund / Rata (Pera / ACA Diff.)</span>
                    <span>₱<?= number_format($payslip['refund_rata'], 2) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($payslip['period_of_service'])): ?>
                <div class="mt-3 small text-muted">
                    Period of Service: <?= esc($payslip['period_of_service']) ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-danger mb-3 border-bottom pb-2">DEDUCTIONS</h6>

                <p class="fw-bold small text-muted mb-1 mt-2">GSIS</p>
                <div class="d-flex justify-content-between small mb-1"><span>Premium (Personal)</span><span>₱<?= number_format($payslip['gsis_premium'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Conso Policy / MPL</span><span>₱<?= number_format($payslip['gsis_policy'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>GFAL / EMRGYLN / MPL Lite / CPL</span><span>₱<?= number_format($payslip['gsis_other'] ?? 0, 2) ?></span></div>

                <p class="fw-bold small text-muted mb-1 mt-3">PAG-IBIG</p>
                <div class="d-flex justify-content-between small mb-1"><span>Premium (Personal)</span><span>₱<?= number_format($payslip['pagibig_premium'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Salary Loan / MP2</span><span>₱<?= number_format($payslip['pagibig_loan'] ?? 0, 2) ?></span></div>

                <p class="fw-bold small text-muted mb-1 mt-3">BANK'S / COOP'S</p>
                <div class="d-flex justify-content-between small mb-1"><span>LBP / Other Payables</span><span>₱<?= number_format($payslip['bank_lbp'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>MCC (Over)</span><span>₱<?= number_format($payslip['bank_mcc'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>1stVB / RBT</span><span>₱<?= number_format($payslip['bank_1stvb'] ?? 0, 2) ?></span></div>

                <p class="fw-bold small text-muted mb-1 mt-3">OTHER</p>
                <div class="d-flex justify-content-between small mb-1"><span>PHIC</span><span>₱<?= number_format($payslip['phic'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small mb-1"><span>BIR W/T Tax</span><span>₱<?= number_format($payslip['withholding_tax'] ?? 0, 2) ?></span></div>

                <div class="d-flex justify-content-between mt-3 pt-2 border-top fw-bold">
                    <span>Total Deductions</span>
                    <span>₱<?= number_format($payslip['total_deductions'], 2) ?></span>
                </div>
            </div>
        </div>

        <div class="row mb-3 text-center">
            <div class="col-6 border-end">
                <small class="text-muted d-block">1ST QUINCENA</small>
                <span class="fw-bold">₱<?= number_format($payslip['first_quincena'] ?? 0, 2) ?></span>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">2ND QUINCENA</small>
                <span class="fw-bold">₱<?= number_format($payslip['second_quincena'] ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="bg-light p-4 rounded d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0">NET PAYABLE</h4>
                <small class="text-muted">Direct Deposit to Registered Account</small>
            </div>
            <h2 class="fw-bold text-primary mb-0">₱<?= number_format($payslip['net_pay'], 2) ?></h2>
        </div>

        <div class="mt-5 text-center">
            <p class="small text-muted">This is a system-generated document. No signature required.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white; }
        .d-print-none { display: none !important; }
        .payslip-card { box-shadow: none !important; border: 1px solid #eee !important; margin-top: 0 !important; }
    }
</style>
<?= $this->endSection() ?>