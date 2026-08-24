<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip — <?= esc($employee['full_name'] ?? '') ?></title>
    <style>
        * { font-family: Arial, sans-serif !important; box-sizing: border-box; }
        body { background: #fff; margin: 0; padding: 0; color: #000; }
        .payslip-page {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 35px 55px 40px 55px;
        }
        .payslip-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .payslip-header .logo-area {
            text-align: left;
            margin-bottom: 10px;
        }
        .payslip-header .logo-area img {
            height: 70px;
            width: auto;
        }
        .payslip-header h1 {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 2px;
        }
        .payslip-header .subtitle {
            font-size: 11px;
            color: #333;
            margin-top: 4px;
            line-height: 1.4;
        }
        .divider {
            border-top: 2px solid #000;
            margin-top: 8px;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 55px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .info-col {
            width: 50%;
        }
        .info-item {
            margin-bottom: 6px;
            font-size: 14px;
        }
        .info-item .label {
            font-weight: normal;
            margin-right: 8px;
        }
        .info-item .value {
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-col.right {
            text-align: right;
        }
        .details-section {
            margin-bottom: 70px;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        .details-col {
            width: 50%;
        }
        .details-col.left {
            padding-right: 10px;
        }
        .details-col.right {
            padding-left: 10px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #000;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            font-size: 13px;
            border-bottom: 1px dotted #ccc;
        }
        .detail-item .label {
            flex: 1;
        }
        .detail-item .amount {
            text-align: right;
            font-weight: normal;
            min-width: 120px;
        }
        .total-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            gap: 20px;
        }
        .total-item {
            width: 50%;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .total-item .label {
            display: inline;
        }
        .total-item .amount {
            float: right;
        }
        .net-pay-box {
            margin-top: 30px;
            border-top: 2px solid #000;
            border-bottom: 3px double #000;
            padding: 10px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: right;
        }
        .half-pay-box {
            margin-top: 25px;
            display: flex;
            gap: 20px;
        }
        .half-item {
            width: 50%;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 8px 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .half-item.right {
            text-align: right;
            justify-content: flex-end;
        }
        .text-end { text-align: right !important; }
        .fw-bold { font-weight: bold; }
        @media print {
            body { background: #fff; }
            .payslip-page { padding: 20px 40px 20px 40px; }
            @page {
                size: A4 portrait;
                margin: 20px 40px 20px 40px;
            }
        }
    </style>
</head>
<body>

<div class="payslip-page">
    <div class="payslip-header">
        <div class="logo-area">
            <svg width="70" height="70" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="45" stroke="#000" stroke-width="2" fill="none"/>
                <circle cx="50" cy="50" r="38" stroke="#000" stroke-width="1" fill="none"/>
                <text x="50" y="45" text-anchor="middle" font-size="10" font-weight="bold" fill="#000">LGU</text>
                <text x="50" y="58" text-anchor="middle" font-size="8" fill="#000">MAHINOG</text>
                <text x="50" y="68" text-anchor="middle" font-size="7" fill="#000">Camiguin</text>
            </svg>
        </div>
        <h1>Municipal Payroll</h1>
        <div class="subtitle">
            We hereby acknowledge to have received from <strong style="text-decoration: underline;">MARY LUSSEL S. PACTO</strong>, Treasurer of <strong style="text-decoration: underline;">Mahinog, Camiguin</strong> the sums herein specified opposite our respective names, the same, being full compensation for our services rendered during the period stated below, to the correctness of which we hereby severally certify.
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-col">
                <div class="info-item">
                    <span class="label">Office:</span>
                    <span class="value"><?= esc($office_name ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Name:</span>
                    <span class="value"><?= esc($employee['full_name'] ?? '') ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Designation:</span>
                    <span class="value"><?= esc($employee['position'] ?? '') ?></span>
                </div>
            </div>
            <div class="info-col right">
                <div class="info-item">
                    <span class="label">Pay Period Start Date:</span>
                    <span class="value"><?= esc($periodStart) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Pay Period End Date:</span>
                    <span class="value"><?= esc($periodEnd) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="details-section">
        <div class="details-row">
            <div class="details-col left">
                <div class="section-title">Earnings:</div>
                <div class="detail-item">
                    <span class="label">Basic Pay</span>
                    <span class="amount"><?= peso($basicPay) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">RATA</span>
                    <span class="amount"><?= peso($rata) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">PERA/ACA</span>
                    <span class="amount"><?= peso($rata) ?></span>
                </div>
            </div>
            <div class="details-col right">
                <div class="section-title">Deductions:</div>
                <div class="detail-item">
                    <span class="label">GSIS Personal Premium</span>
                    <span class="amount"><?= peso($employee['gsis_premium'] ?? 0) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">PAG-IBIG Personal Premium</span>
                    <span class="amount"><?= peso($employee['pagibig_premium'] ?? 0) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">PHILHEALTH Personal Premium</span>
                    <span class="amount"><?= peso(phicTotal($employee)) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">WITHHOLDING TAX</span>
                    <span class="amount"><?= peso($employee['withholding_tax'] ?? 0) ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">PAG-IBIG MP2</span>
                    <span class="amount"><?= peso($employee['pagibig_mp2'] ?? $employee['pagibig_loan'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="total-box">
        <div class="total-item">
            <span class="label">Total Earnings</span>
            <span class="amount"><?= peso($totalEarnings) ?></span>
        </div>
        <div class="total-item">
            <span class="label">Total Deductions</span>
            <span class="amount"><?= peso($totalDeductions) ?></span>
        </div>
    </div>

    <div class="net-pay-box">
        NET PAY FOR THE MONTH <?= peso($netPay) ?>
    </div>

    <div class="half-pay-box">
        <div class="half-item">
            <span>1ST HALF</span>
            <span><?= peso($firstHalf) ?></span>
        </div>
        <div class="half-item right">
            <span>2ND HALF</span>
            <span><?= peso($secondHalf) ?></span>
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
</body>
</html>
