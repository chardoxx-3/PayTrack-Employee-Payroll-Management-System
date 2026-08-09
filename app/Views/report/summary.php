<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold"><i class="fas fa-table me-2"></i>Payroll Summary Report</h5>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm"><i class="fas fa-print me-2"></i>Print Report</button>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small">
                    <tr>
                        <th class="ps-4">EMPLOYEE NAME</th>
                        <th>OFFICE</th>
                        <th>GROSS PAY</th>
                        <th>DEDUCTIONS</th>
                        <th class="text-end pe-4">NET PAYABLE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalGross = 0; $totalDeduct = 0; $totalNet = 0;
                    foreach($results as $row): 
                        $totalGross += $row['gross_pay'];
                        $totalDeduct += $row['total_deductions'];
                        $totalNet += $row['net_pay'];
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= $row['full_name'] ?></td>
                        <td><?= $row['office_name'] ?></td>
                        <td>₱<?= number_format($row['gross_pay'], 2) ?></td>
                        <td class="text-danger">₱<?= number_format($row['total_deductions'], 2) ?></td>
                        <td class="text-end pe-4 fw-bold">₱<?= number_format($row['net_pay'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="2" class="ps-4">TOTALS FOR PERIOD</td>
                        <td>₱<?= number_format($totalGross, 2) ?></td>
                        <td>₱<?= number_format($totalDeduct, 2) ?></td>
                        <td class="text-end pe-4 fw-bold text-success">₱<?= number_format($totalNet, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>