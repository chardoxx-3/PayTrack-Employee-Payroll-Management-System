<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-size: 14px; }
        .payslip-page { 
            background: white; 
            padding: 40px; 
            margin: 20px auto; 
            width: 210mm; /* A4 width */
            min-height: 148mm; /* A5 height */
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        @media print {
            body { background: white; margin: 0; padding: 0; }
            .payslip-page { margin: 0; box-shadow: none; page-break-after: always; width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print p-4 bg-dark text-white text-center">
        <h5 class="mb-3">Batch Printing Mode: <?= count($records) ?> Payslips Generated</h5>
        <button onclick="window.print()" class="btn btn-primary px-5">Launch Print Dialog</button>
        <a href="/payslip" class="btn btn-outline-light ms-2">Exit</a>
    </div>

<?php foreach($records as $row): ?>
    <div class="payslip-page">
        <div class="d-flex justify-content-between mb-4">
            <h5 class="fw-bold">PAYROLL SLIP</h5>
            <span class="text-muted"><?= $row['payroll_period'] ?></span>
        </div>
        <table class="table table-bordered">
            <tr>
                <td class="bg-light fw-bold" width="25%">Employee Name</td>
                <td><?= $row['full_name'] ?></td>
                <td class="bg-light fw-bold" width="25%">ID Number</td>
                <td><?= $row['emp_code'] ?? $row['employee_id'] ?></td>
            </tr>
            <tr>
                <td class="bg-light fw-bold">Position</td>
                <td><?= $row['position'] ?? '' ?></td>
                <td class="bg-light fw-bold">Period of Service</td>
                <td><?= $row['period_of_service'] ?? $row['payroll_period'] ?></td>
            </tr>
        </table>
        <div class="row">
            <div class="col-4">
                <p class="fw-bold border-bottom small">GSIS</p>
                <div class="d-flex justify-content-between small"><span>Premium</span><span><?= number_format($row['gsis_premium'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>Conso/MPL</span><span><?= number_format($row['gsis_policy'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>GFAL/EMRGYLN</span><span><?= number_format($row['gsis_other'] ?? 0, 2) ?></span></div>
            </div>
            <div class="col-4">
                <p class="fw-bold border-bottom small">PAG-IBIG</p>
                <div class="d-flex justify-content-between small"><span>Premium</span><span><?= number_format($row['pagibig_premium'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>Salary Loan/MP2</span><span><?= number_format($row['pagibig_loan'] ?? 0, 2) ?></span></div>
                <p class="fw-bold border-bottom small mt-2">OTHER</p>
                <div class="d-flex justify-content-between small"><span>PHIC</span><span><?= number_format($row['phic'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>BIR W/T Tax</span><span><?= number_format($row['withholding_tax'] ?? 0, 2) ?></span></div>
            </div>
            <div class="col-4">
                <p class="fw-bold border-bottom small">BANK'S / COOP'S</p>
                <div class="d-flex justify-content-between small"><span>LBP/Other</span><span><?= number_format($row['bank_lbp'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>MCC (Over)</span><span><?= number_format($row['bank_mcc'] ?? 0, 2) ?></span></div>
                <div class="d-flex justify-content-between small"><span>1stVB/RBT</span><span><?= number_format($row['bank_1stvb'] ?? 0, 2) ?></span></div>
            </div>
        </div>
        <div class="row mt-3 pt-2 border-top text-center">
            <div class="col-4">
                <small class="text-muted d-block">TOTAL DEDUCTIONS</small>
                <span class="fw-bold text-danger">-<?= number_format($row['total_deductions'], 2) ?></span>
            </div>
            <div class="col-4">
                <small class="text-muted d-block">1ST QUINCENA</small>
                <span class="fw-bold"><?= number_format($row['first_quincena'] ?? 0, 2) ?></span>
            </div>
            <div class="col-4">
                <small class="text-muted d-block">2ND QUINCENA</small>
                <span class="fw-bold"><?= number_format($row['second_quincena'] ?? 0, 2) ?></span>
            </div>
        </div>
        <div class="mt-4 text-end">
            <h4 class="fw-bold">NET PAY: ₱<?= number_format($row['net_pay'], 2) ?></h4>
        </div>
        <div class="row mt-5 pt-4">
            <div class="col-6 text-center">
                <div class="border-top d-inline-block px-4 pt-1">Employee Signature</div>
            </div>
            <div class="col-6 text-center">
                <div class="border-top d-inline-block px-4 pt-1">Date Received</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</body>
</html>