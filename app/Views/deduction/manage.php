<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .erp-card {
        border: 1px solid var(--line);
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .erp-card .card-header {
        background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-700) 100%);
        border: none;
    }
    .bg-navy { background-color: var(--navy-800) !important; }
    .bg-navy-light { background-color: var(--navy-subtle) !important; }
    .text-navy { color: var(--navy-700) !important; }
    .pay-summary-strip {
        background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-700) 100%);
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }
    .pay-summary-strip .pay-label {
        color: var(--navy-subtle);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        margin-bottom: 0.4rem;
        display: block;
    }
    .pay-summary-strip .input-group-text {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        color: var(--navy-subtle);
    }
    .pay-summary-strip input {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        font-weight: 700;
    }
    .pay-summary-strip input#net_pay_display {
        color: #4ade80;
    }
    .pay-summary-strip input#net_pay_display.text-danger {
        color: #f87171 !important;
    }
    .deduction-section {
        border: 1px solid var(--line);
        border-left: 4px solid var(--navy-600);
        padding: 1.25rem 1.5rem 1.5rem;
        margin-bottom: 1.25rem;
        background: #fff;
    }
    .deduction-section:last-child {
        margin-bottom: 0;
    }
    .deduction-section-header {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 1.1rem;
    }
    .deduction-section-icon {
        width: 32px;
        height: 32px;
        background: var(--navy-700);
        color: var(--navy-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .deduction-section-title {
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--navy-800);
        margin: 0;
    }
    .form-label.deduction-label {
        color: var(--ink-soft) !important;
        font-weight: 600;
    }
    .input-group-text.deduction-prefix {
        background: var(--navy-subtle) !important;
        border: 1px solid #dee2e6 !important;
        color: var(--navy-700) !important;
    }
    .deduction-input.form-control {
        border: 1px solid #dee2e6 !important;
    }
    .deduction-input.form-control:focus {
        border-color: var(--navy-600);
        box-shadow: 0 0 0 0.15rem rgba(15, 42, 74, 0.15);
    }
    .info-bar {
        background: var(--navy-subtle);
        padding: 0.75rem 1rem;
        border: 1px solid var(--line);
    }
</style>
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/employee">Employees</a></li>
            <li class="breadcrumb-item active">Deduction Management</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- LEFT: Employee Information -->
        <div class="col-lg-4">
            <div class="card erp-card h-100">
                <div class="card-header text-white py-3">
                    <h6 class="mb-0 fw-bold">Employee Information</h6>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="avatar bg-navy text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;font-size:2rem;">
                        <?= substr($employee['full_name'], 0, 1) ?>
                    </div>
                    <h5 class="fw-bold mb-1"><?= esc($employee['full_name']) ?></h5>
                    <p class="text-muted small mb-2"><?= esc($employee['position']) ?></p>

                    <?php if (!empty($employee['is_active'])): ?>
                        <span class="badge bg-success mb-3">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary mb-3">Inactive</span>
                    <?php endif; ?>

                    <hr>

                    <div class="text-start">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small fw-bold">Employee ID</span>
                            <span class="small"><?= esc($employee['employee_id']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small fw-bold">Office</span>
                            <span class="small"><?= esc($employee['office_name'] ?? '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small fw-bold">Contact No.</span>
                            <span class="small"><?= esc($employee['contact_number'] ?? '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small fw-bold">Employment Status</span>
                            <span class="small"><?= esc($employee['employment_status'] ?? '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted small fw-bold">Monthly Rate</span>
                            <span class="small fw-bold text-navy">₱<?= number_format($employee['salary_rate'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Pay Details Form -->
        <div class="col-lg-8">
            <div class="card erp-card">
                <div class="card-header text-white py-3">
                    <h6 class="mb-0 fw-bold">Modify Monthly Deductions</h6>
                </div>
                <div class="card-body p-4">
                    <form action="/deduction/update" method="post">
                        <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">

                        <!-- PAY SUMMARY STRIP -->
                        <div class="pay-summary-strip">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="pay-label">MONTHLY RATE</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="text" inputmode="decimal" id="salary_rate" name="salary_rate" class="form-control deduction-input money-format" value="<?= number_format($employee['salary_rate'], 2) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="pay-label">NET PAY</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="text" id="net_pay_display" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="pay-label">REFUND / RATA</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="text" inputmode="decimal" id="refund_rata" name="refund_rata" class="form-control money-format" value="<?= number_format($refund_rata ?? 0, 2) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- GSIS -->
                            <div class="col-12">
                                <div class="deduction-section">
                                    <div class="deduction-section-header">
                                        <span class="deduction-section-icon"><i class="fas fa-shield-alt"></i></span>
                                        <p class="deduction-section-title">GSIS</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">PREMIUM (PERSONAL)</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="gsis_premium" class="form-control deduction-input money-format" value="<?= number_format($deductions['gsis_premium'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">CONSO POLICY / MPL</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="gsis_policy" class="form-control deduction-input money-format" value="<?= number_format($deductions['gsis_policy'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">GFAL / EMRGYLN / MPL LITE / CPL</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="gsis_other" class="form-control deduction-input money-format" value="<?= number_format($deductions['gsis_other'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PAG-IBIG -->
                            <div class="col-12">
                                <div class="deduction-section">
                                    <div class="deduction-section-header">
                                        <span class="deduction-section-icon"><i class="fas fa-home"></i></span>
                                        <p class="deduction-section-title">Pag-IBIG</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">PREMIUM (PERSONAL)</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="pagibig_premium" class="form-control deduction-input money-format" value="<?= number_format($deductions['pagibig_premium'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">SALARY LOAN / MP2</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="pagibig_loan" class="form-control deduction-input money-format" value="<?= number_format($deductions['pagibig_loan'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BANKS / COOP'S -->
                            <div class="col-12">
                                <div class="deduction-section">
                                    <div class="deduction-section-header">
                                        <span class="deduction-section-icon"><i class="fas fa-university"></i></span>
                                        <p class="deduction-section-title">Banks / Coop's</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">LBP / OTHER PAYABLES</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="bank_lbp" class="form-control deduction-input money-format" value="<?= number_format($deductions['bank_lbp'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">MCC (OVER)</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="bank_mcc" class="form-control deduction-input money-format" value="<?= number_format($deductions['bank_mcc'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label deduction-label small">1STVB / RBT</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="bank_1stvb" class="form-control deduction-input money-format" value="<?= number_format($deductions['bank_1stvb'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- OTHER -->
                            <div class="col-12">
                                <div class="deduction-section">
                                    <div class="deduction-section-header">
                                        <span class="deduction-section-icon"><i class="fas fa-file-invoice"></i></span>
                                        <p class="deduction-section-title">Other Deductions</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">PHIC (PHILHEALTH)</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="phic" class="form-control deduction-input money-format" value="<?= number_format($deductions['phic'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">BIR W/T TAX</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="tax" class="form-control deduction-input money-format" value="<?= number_format($deductions['withholding_tax'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">LOANS</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="loans" class="form-control deduction-input money-format" value="<?= number_format($deductions['loans'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">GOVERNMENT CONTRIBUTION</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="gov" class="form-control deduction-input money-format" value="<?= number_format($deductions['government_cont'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label deduction-label small">OTHER DEDUCTIONS</label>
                                            <div class="input-group">
                                                <span class="input-group-text deduction-prefix">₱</span>
                                                <input type="text" inputmode="decimal" name="others" class="form-control deduction-input money-format" value="<?= number_format($deductions['other_deduct'] ?? 0, 2) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> These values will be used for the current payroll period computation.</p>
                            <button type="submit" class="btn btn-success px-5 fw-bold">Update Deductions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function unformatMoney(value) {
    return (value || '').replace(/,/g, '');
}

function addCommas(raw) {
    if (raw === '') return '';
    let [intPart, decPart] = raw.split('.');
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    return decPart !== undefined ? `${intPart}.${decPart}` : intPart;
}

function recalcNetPay() {
    const monthlyRate = parseFloat(unformatMoney(document.getElementById('salary_rate').value)) || 0;
    let totalDeductions = 0;
    document.querySelectorAll('.deduction-input:not(#salary_rate)').forEach(input => {
        totalDeductions += parseFloat(unformatMoney(input.value)) || 0;
    });
    const netPay = monthlyRate - totalDeductions;

    const netPayInput = document.getElementById('net_pay_display');
    const netPaySign = netPayInput.previousElementSibling;

    netPayInput.value = netPay.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    if (netPay < 0) {
        netPayInput.classList.remove('text-success');
        netPayInput.classList.add('text-danger');
        netPaySign.classList.remove('bg-success');
        netPaySign.classList.add('bg-danger');
    } else {
        netPayInput.classList.remove('text-danger');
        netPayInput.classList.add('text-success');
        netPaySign.classList.remove('bg-danger');
        netPaySign.classList.add('bg-success');
    }
}

document.querySelectorAll('.money-format').forEach(input => {
    input.addEventListener('focus', function () {
        const raw = unformatMoney(this.value);
        if (raw === '' || parseFloat(raw) === 0) {
            this.value = '';
        }
    });

    input.addEventListener('input', function () {
        let raw = unformatMoney(this.value).replace(/[^0-9.]/g, '');
        const firstDot = raw.indexOf('.');
        if (firstDot !== -1) {
            raw = raw.slice(0, firstDot + 1) + raw.slice(firstDot + 1).replace(/\./g, '');
        }
        this.value = addCommas(raw);
        if (this.classList.contains('deduction-input')) {
            recalcNetPay();
        }
    });

    input.addEventListener('blur', function () {
        const raw = unformatMoney(this.value);
        if (raw === '' || isNaN(parseFloat(raw))) {
            this.value = '0.00';
        } else {
            this.value = addCommas(parseFloat(raw).toFixed(2));
        }
        if (this.classList.contains('deduction-input')) {
            recalcNetPay();
        }
    });
});

document.querySelector('form').addEventListener('submit', function () {
    document.querySelectorAll('.money-format').forEach(input => {
        input.value = unformatMoney(input.value);
    });
});

recalcNetPay();
</script>

<?= $this->endSection() ?>
