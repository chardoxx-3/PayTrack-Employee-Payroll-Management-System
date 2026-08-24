<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php
        $reportLabels = [
            'office' => 'Office-wise Summary',
            'period' => 'Monthly Payroll Record',
            'deductions' => 'Deduction Analysis'
        ];
        $reportLabel = $reportLabels[$report_type] ?? 'Payroll Summary Report';
        $periodFormatted = date('F Y', strtotime($period . '-01'));
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0"><i class="fas fa-table me-2"></i><?= esc($reportLabel) ?></h5>
            <p class="text-muted small mb-0">Period: <strong><?= esc($periodFormatted) ?></strong> | Office: <strong><?= esc($office_name) ?></strong></p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fas fa-print me-2"></i>Print Report</button>
    </div>

    <?php if (empty($results)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>No records found</h5>
                <p class="mb-0">There are no payroll records for <?= esc($periodFormatted) ?> in <?= esc($office_name) ?>.</p>
            </div>
        </div>
    <?php elseif ($report_type === 'office'): ?>
        <?php foreach ($results as $officeGroup): ?>
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-dark text-white py-2">
                    <h6 class="mb-0 fw-bold"><?= esc($officeGroup['office_name']) ?> <span class="badge bg-light text-dark ms-2"><?= $officeGroup['count'] ?> Employee<?= $officeGroup['count'] == 1 ? '' : 's' ?></span></h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th class="ps-4">EMPLOYEE NAME</th>
                                <th>DESIGNATION</th>
                                <th class="text-end">GROSS PAY</th>
                                <th class="text-end">DEDUCTIONS</th>
                                <th class="text-end pe-4">NET PAYABLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($officeGroup['employees'] as $row): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= esc($row['full_name']) ?></td>
                                <td><?= esc($row['position']) ?></td>
                                <td class="text-end">₱<?= number_format($row['gross_pay'], 2) ?></td>
                                <td class="text-end text-danger">₱<?= number_format($row['total_deductions'], 2) ?></td>
                                <td class="text-end pe-4 fw-bold">₱<?= number_format($row['net_pay'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="ps-4 fw-bold">OFFICE TOTAL</td>
                                <td class="text-end fw-bold">₱<?= number_format($officeGroup['total_gross'], 2) ?></td>
                                <td class="text-end fw-bold text-danger">₱<?= number_format($officeGroup['total_deductions'], 2) ?></td>
                                <td class="text-end pe-4 fw-bold text-success">₱<?= number_format($officeGroup['total_net'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif ($report_type === 'deductions'): ?>
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">EMPLOYEE NAME</th>
                            <th>OFFICE</th>
                            <th class="text-end">GSIS</th>
                            <th class="text-end">PAG-IBIG</th>
                            <th class="text-end">PHIC</th>
                            <th class="text-end">BANKS/COOP</th>
                            <th class="text-end">W/TAX</th>
                            <th class="text-end pe-4">TOTAL DEDUCT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalGsis = 0; $totalPagibig = 0; $totalPhic = 0;
                            $totalBank = 0; $totalTax = 0; $totalDeduct = 0;
                        ?>
                        <?php foreach ($results as $row):
                            $gsis = ($row['gsis_premium'] ?? 0) + ($row['gsis_policy'] ?? 0) + ($row['gsis_other'] ?? 0) + ($row['gsis_ouli'] ?? 0) + ($row['gsis_diff'] ?? 0);
                            $pagibig = ($row['pagibig_premium'] ?? 0) + ($row['pagibig_loan'] ?? 0) + ($row['pagibig_mp2'] ?? 0);
                            $phic = ($row['phic'] ?? 0) + ($row['phic_diff'] ?? 0);
                            $bank = ($row['bank_lbp'] ?? 0) + ($row['bank_mcc'] ?? 0) + ($row['bank_1stvb'] ?? 0) + ($row['bank_other_payables'] ?? 0) + ($row['bank_rbt'] ?? 0);
                            $tax = $row['withholding_tax'] ?? 0;
                            $totalGsis += $gsis;
                            $totalPagibig += $pagibig;
                            $totalPhic += $phic;
                            $totalBank += $bank;
                            $totalTax += $tax;
                            $totalDeduct += $row['total_deductions'];
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= esc($row['full_name']) ?></td>
                            <td><?= esc($row['office_name']) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($gsis, 2) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($pagibig, 2) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($phic, 2) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($bank, 2) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($tax, 2) ?></td>
                            <td class="text-end pe-4 fw-bold">₱<?= number_format($row['total_deductions'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="2" class="ps-4">TOTALS</td>
                            <td class="text-end">₱<?= number_format($totalGsis, 2) ?></td>
                            <td class="text-end">₱<?= number_format($totalPagibig, 2) ?></td>
                            <td class="text-end">₱<?= number_format($totalPhic, 2) ?></td>
                            <td class="text-end">₱<?= number_format($totalBank, 2) ?></td>
                            <td class="text-end">₱<?= number_format($totalTax, 2) ?></td>
                            <td class="text-end pe-4 fw-bold text-success">₱<?= number_format($totalDeduct, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-4">EMPLOYEE NAME</th>
                            <th>OFFICE</th>
                            <th class="text-end">GROSS PAY</th>
                            <th class="text-end">DEDUCTIONS</th>
                            <th class="text-end pe-4">NET PAYABLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalGross = 0; $totalDeduct = 0; $totalNet = 0;
                        ?>
                        <?php foreach ($results as $row):
                            $totalGross += $row['gross_pay'];
                            $totalDeduct += $row['total_deductions'];
                            $totalNet += $row['net_pay'];
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= esc($row['full_name']) ?></td>
                            <td><?= esc($row['office_name']) ?></td>
                            <td class="text-end">₱<?= number_format($row['gross_pay'], 2) ?></td>
                            <td class="text-end text-danger">₱<?= number_format($row['total_deductions'], 2) ?></td>
                            <td class="text-end pe-4 fw-bold">₱<?= number_format($row['net_pay'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <td colspan="2" class="ps-4">TOTALS FOR PERIOD</td>
                            <td class="text-end">₱<?= number_format($totalGross, 2) ?></td>
                            <td class="text-end">₱<?= number_format($totalDeduct, 2) ?></td>
                            <td class="text-end pe-4 fw-bold text-success">₱<?= number_format($totalNet, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>