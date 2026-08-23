<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\OfficeModel;

class Employee extends BaseController
{
    public function index()
    {
        $model = new EmployeeModel();
        $officeModel = new OfficeModel();
        
        $officeFilter = $this->request->getVar('office_id');
        $search = $this->request->getVar('search');

$query = $model->select('employees.*, offices.office_name')
                       ->join('offices', 'offices.id = employees.office_id', 'left');

        if ($officeFilter) $query->where('office_id', $officeFilter);
        if ($search) $query->like('full_name', $search)->orLike('employee_id', $search);

        $data['employees'] = $query->findAll();
        $data['offices'] = $officeModel->findAll();
        
        return view('employee/index', $data);
    }

public function store()
    {
        $model = new EmployeeModel();

        if (!$model->save($this->request->getPost())) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/employee')->with('success', 'Employee registered successfully.');
    }

    public function update($id)
    {
        $model = new EmployeeModel();
        $model->update($id, $this->request->getPost());
        return redirect()->to('/employee')->with('success', 'Employee updated successfully.');
    }

    public function delete($id)
    {
        $model = new EmployeeModel();
        $model->delete($id);
        return redirect()->to('/employee')->with('success', 'Employee removed.');
    }

    public function deleteAll()
    {
        $deductModel = new \App\Models\DeductionModel();
        $payrollModel = new \App\Models\PayrollModel();
        $empModel = new EmployeeModel();

        $payrollModel->emptyTable();
        $deductModel->emptyTable();
        $empModel->emptyTable();

        return redirect()->to('/employee')->with('success', 'All employee records deleted.');
    }

public function create()
{
    $officeModel = new OfficeModel();
    $model = new EmployeeModel();

    $year = date('Y');
    $count = $model->like('employee_id', "EMP-{$year}-", 'after')->countAllResults();
    $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    $data['generated_id'] = "EMP-{$year}-{$nextNumber}";

    $data['offices'] = $officeModel->findAll();
    return view('employee/create', $data);
}

public function edit($id)
{
    $model = new EmployeeModel();
    $officeModel = new OfficeModel();

    $data['employee'] = $model->find($id);
    $data['offices']  = $officeModel->findAll();

    if (!$data['employee']) {
        return redirect()->to('/employee')->with('errors', 'Employee not found.');
    }

    return view('employee/edit', $data);
}

public function import()
{
    $officeModel = new OfficeModel();
    $model = new EmployeeModel();
    $deductModel = new \App\Models\DeductionModel();
    $payrollModel = new \App\Models\PayrollModel();

    $file = $this->request->getFile('payroll_file');
    if (!$file || !$file->isValid()) {
        return redirect()->to('/employee')->with('errors', 'Please select a valid Excel file to import.');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());

    $year = date('Y');
    $nextNumber = $model->like('employee_id', "EMP-{$year}-", 'after')->countAllResults() + 1;
    $period = date('Y-m');

    $imported = 0;
    $updated  = 0;
    $skipped  = 0;

    $toFloat = function ($v) {
        if (is_string($v)) $v = str_replace([',', ' '], '', $v);
        return is_numeric($v) ? (float) $v : 0;
    };

    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        // "rata" and "VICE-SB rata" are RATA-only payroll vouchers with a different
        // column layout (no "MONTHLY RATE OF PAY" column, so everything after NAME/
        // DESIGNATION/PERIOD is shifted compared to the normal office sheets). They
        // get imported as their own office below, via a dedicated parser that only
        // trusts what these sheets actually contain (name, designation, RATA amount)
        // instead of the normal sheets' fixed column indices, which would otherwise
        // land on the wrong field or blank cells.
        $normalized = strtolower(trim(str_replace('-', ' ', $sheetName)));
        $isRataOnlySheet = ($normalized === 'rata' || $normalized === 'vice sb rata');

        $officeName = trim(preg_replace('/\s+\d+\s+\d+\s*$/', '', $sheetName));
        $office = $officeModel->where('office_name', $officeName)->first();
        $officeId = $office ? $office['id'] : $officeModel->insert(['office_name' => $officeName]);

        $rows = $spreadsheet->getSheetByName($sheetName)->toArray();

        if ($isRataOnlySheet) {
            $result = $this->importRataOnlySheet($rows, $model, $payrollModel, $officeId, $year, $nextNumber, $period);
            $imported   += $result['imported'];
            $updated    += $result['updated'];
            $nextNumber  = $result['nextNumber'];
            continue;
        }

        foreach ($rows as $i => $row) { // NEW: capture index to look at the next row
            $no            = $row[0] ?? null;
            $fullName      = trim($row[1] ?? '');
            $designation   = trim($row[2] ?? '');
            $salaryRate    = $row[4] ?? null;
            $contactNumber = trim($row[19] ?? '');

            if (!is_numeric($no) || $fullName === '' || is_numeric($fullName)) {
                continue;
            }

            $rawSalary = is_string($salaryRate) ? str_replace([',', ' '], '', $salaryRate) : $salaryRate;
            $hasValidSalary = is_numeric($rawSalary) && (float) $rawSalary > 0;

            $deductionData = [
                'gsis_premium'    => $toFloat($row[6]  ?? 0),
                'pagibig_premium' => $toFloat($row[9]  ?? 0),
                'phic'            => $toFloat($row[11] ?? 0),
                'bank_lbp'        => $toFloat($row[12] ?? 0),
                'bank_mcc'        => $toFloat($row[13] ?? 0),
                'bank_1stvb'      => $toFloat($row[14] ?? 0),
                'withholding_tax' => $toFloat($row[15] ?? 0),
            ];

            // Refund/Rata isn't always on the very next row (some blocks have a blank
            // spacer row first), so find the end of this employee's block — the next
            // numbered employee row, or the sheet's "Total" row — and take the FIRST
            // populated value in column 5 within that range.
            $blockEnd = $i + 1;
            while (isset($rows[$blockEnd])) {
                $blockNo    = $rows[$blockEnd][0] ?? null;
                $blockName  = trim($rows[$blockEnd][1] ?? '');
                $blockLabel = trim((string) ($rows[$blockEnd][16] ?? '')); // "1st/2nd Quincena" summary label lives here
                if (is_numeric($blockNo) && $blockName !== '' && !is_numeric($blockName)) {
                    break; // reached the next employee's row
                }
                if (strcasecmp(trim((string) $blockNo), 'Total') === 0) {
                    break; // reached the sheet's totals row
                }
                if (stripos($blockLabel, 'quincena') !== false) {
                    break; // reached the sheet-wide "1st Quincena"/"2nd Quincena" summary row —
                           // these sit BEFORE the "Total" row and column 0 is blank there, so without
                           // this check the last employee's block could swallow the sheet's own totals
                }
                $blockEnd++;
            }

            $refundRata = 0;
            for ($r = $i; $r < $blockEnd; $r++) {
                $rawRefund = trim((string) ($rows[$r][5] ?? ''));
                if ($rawRefund !== '' && $rawRefund !== '-') {
                    $refundRata = $toFloat($rawRefund);
                    break;
                }
            }

            $secondQuincena = 0;
            for ($r = $i + 1; $r < $blockEnd; $r++) {
                $rawQuincena = trim((string) ($rows[$r][17] ?? ''));
                if ($rawQuincena !== '' && $rawQuincena !== '-') {
                    $secondQuincena = $toFloat($rawQuincena);
                    break;
                }
            }

            $netPay        = $toFloat($row[16] ?? 0); // actual calculated Net Pay column from the sheet
            $firstQuincena = $toFloat($row[17] ?? 0); // 1st quincena, main row

            $existing = $model->where('office_id', $officeId)
                               ->where('full_name', $fullName)
                               ->first();

            if ($existing) {
                $data = ['position' => $designation ?: $existing['position']];
                if ($hasValidSalary) {
                    $data['salary_rate'] = (float) $rawSalary;
                }
                if ($contactNumber !== '' && preg_match('/^\d{7,15}$/', $contactNumber)) {
                    $data['contact_number'] = $contactNumber;
                }
                $model->update($existing['id'], $data);
                $updated++;

                $empId = $existing['id'];
            } else {
                $employeeId = "EMP-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $nextNumber++;

                $empId = $model->insert([
                    'employee_id'       => $employeeId,
                    'full_name'         => $fullName,
                    'office_id'         => $officeId,
                    'position'          => $designation ?: 'N/A',
                    'contact_number'    => (preg_match('/^\d{7,15}$/', $contactNumber) ? $contactNumber : null),
                    'salary_rate'       => $hasValidSalary ? (float) $rawSalary : 0,
                    'employment_status' => 'Regular',
                    'is_active'         => 1,
                ]);

                if (!$empId) {
                    log_message('error', 'Employee import failed for "{name}": {errors}', [
                        'name'   => $fullName,
                        'errors' => implode('; ', $model->errors() ?? []),
                    ]);
                    $skipped++;
                    continue;
                }

                $imported++;
            }

            // Upsert deductions
            $existingDeduction = $deductModel->where('employee_id', $empId)->first();
            if ($existingDeduction) {
                $deductModel->update($existingDeduction['id'], $deductionData);
            } else {
                $deductionData['employee_id'] = $empId;
                $deductModel->insert($deductionData);
            }

            // Upsert this period's payroll record with the sheet's own refund, net pay, and quincena values
            $grossPay = $hasValidSalary ? (float) $rawSalary : ($existing['salary_rate'] ?? 0);

            $payrollData = [
                'refund_rata'       => $refundRata,
                'gross_pay'         => $grossPay,
                'total_deductions'  => round($grossPay - $netPay, 2),
                'net_pay'           => $netPay,
                'first_quincena'    => $firstQuincena,
                'second_quincena'   => $secondQuincena,
                'cash_paid'         => $netPay,
                'period_of_service' => date('m/01/Y') . '-' . date('m/t/Y'),
            ];

            $existingPayroll = $payrollModel->where('employee_id', $empId)
                                             ->where('payroll_period', $period)
                                             ->first();
            if ($existingPayroll) {
                $payrollModel->update($existingPayroll['id'], $payrollData);
            } else {
                $payrollData['employee_id']    = $empId;
                $payrollData['payroll_period'] = $period;
                $payrollModel->insert($payrollData);
            }
        }
    }

    $message = "Import complete: {$imported} added, {$updated} updated.";
    if ($skipped > 0) {
        $message .= " {$skipped} row(s) skipped due to validation errors — check logs.";
    }

    return redirect()->to('/employee')->with('success', $message);
}

/**
 * Import a RATA-only payroll sheet ("rata", "VICE-SB rata") as its own office.
 *
 * These sheets don't have a "MONTHLY RATE OF PAY" column, so the RATA amount can
 * sit in column 4 or column 5 depending on the sheet - rather than hardcoding an
 * index, we scan the header block for whichever cell literally says "RATA" and use
 * that column. Only NAME, DESIGNATION, and the RATA amount are trusted from these
 * sheets; there's no reliable salary/deduction data here, so those are left at 0
 * rather than guessed at from the wrong column.
 *
 * The RATA amount is used as refund_rata, gross_pay, net_pay, and cash_paid, since
 * these vouchers show no deductions being taken from it.
 */
private function importRataOnlySheet(
    array $rows,
    EmployeeModel $model,
    \App\Models\PayrollModel $payrollModel,
    int $officeId,
    string $year,
    int $nextNumber,
    string $period
): array {
    // Locate the RATA and NET PAY columns by header text rather than a fixed
    // index - these RATA-only sheets don't share one consistent layout
    // (compare "rata" vs "VICE-SB rata"), so hardcoded indices break silently.
    $rataCol   = $this->findHeaderColumn($rows, ['rata']);
    $netPayCol = $this->findHeaderColumn($rows, ['net pay']);

    if ($rataCol === null) {
        // Sheet layout didn't match anything we recognize - nothing to do.
        return ['imported' => 0, 'updated' => 0, 'nextNumber' => $nextNumber];
    }

    $deductModel = new \App\Models\DeductionModel();

    $imported = 0;
    $updated  = 0;

    foreach ($rows as $i => $row) {
        $no          = $row[0] ?? null;
        $fullName    = trim($row[1] ?? '');
        $designation = trim($row[2] ?? '');

        if (!is_numeric($no) || $fullName === '' || is_numeric($fullName)) {
            continue;
        }

        // Find the end of this employee's block (next numbered row or the
        // sheet's "Total" row), same approach as the main sheet parser -
        // the RATA/NET PAY amount isn't always on the name row itself,
        // sometimes it's on a continuation row underneath.
        $blockEnd = $i + 1;
        while (isset($rows[$blockEnd])) {
            $blockNo   = $rows[$blockEnd][0] ?? null;
            $blockName = trim($rows[$blockEnd][1] ?? '');
            if (is_numeric($blockNo) && $blockName !== '' && !is_numeric($blockName)) {
                break;
            }
            if (strcasecmp(trim((string) $blockNo), 'Total') === 0) {
                break;
            }
            $blockEnd++;
        }

        $rataAmount = $this->firstNonBlankInColumn($rows, $i, $blockEnd, $rataCol);
        $netPay     = $netPayCol !== null
            ? $this->firstNonBlankInColumn($rows, $i, $blockEnd, $netPayCol)
            : null;

        // If a NET PAY figure exists and differs from the RATA amount, the
        // gap is a real deduction taken from that voucher - don't discard it.
        $totalDeductions = ($netPay !== null) ? round($rataAmount - $netPay, 2) : 0;
        $netPay = $netPay ?? $rataAmount;

        $existing = $model->where('office_id', $officeId)
                           ->where('full_name', $fullName)
                           ->first();

        if ($existing) {
            $model->update($existing['id'], ['position' => $designation ?: $existing['position']]);
            $updated++;
            $empId = $existing['id'];
        } else {
            $employeeId = "EMP-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;

            $empId = $model->insert([
                'employee_id'       => $employeeId,
                'full_name'         => $fullName,
                'office_id'         => $officeId,
                'position'          => $designation ?: 'N/A',
                'salary_rate'       => 0,
                'employment_status' => 'Regular',
                'is_active'         => 1,
            ]);

            if (!$empId) {
                log_message('error', 'RATA-only import failed for "{name}": {errors}', [
                    'name'   => $fullName,
                    'errors' => implode('; ', $model->errors() ?? []),
                ]);
                continue;
            }

            $imported++;
        }

        // Record the deduction as a lump sum on this voucher. These sheets'
        // deduction columns (GSIS/PAG-IBIG/bank refunds) don't sit at fixed,
        // consistent indices across sheets, so rather than mis-map them we
        // keep the true net pay accurate and surface the gap here for the
        // deduction screen to review/break down manually.
        if ($totalDeductions != 0) {
            $existingDeduction = $deductModel->where('employee_id', $empId)->first();
            $deductionData = ['other_deduct' => $totalDeductions];
            if ($existingDeduction) {
                $deductModel->update($existingDeduction['id'], $deductionData);
            } else {
                $deductionData['employee_id'] = $empId;
                $deductModel->insert($deductionData);
            }
        }

        $payrollData = [
            'refund_rata'       => $rataAmount,
            'gross_pay'         => $rataAmount,
            'total_deductions'  => $totalDeductions,
            'net_pay'           => $netPay,
            'cash_paid'         => $netPay,
            'period_of_service' => date('m/01/Y') . '-' . date('m/t/Y'),
        ];

        $existingPayroll = $payrollModel->where('employee_id', $empId)
                                         ->where('payroll_period', $period)
                                         ->first();
        if ($existingPayroll) {
            $payrollModel->update($existingPayroll['id'], $payrollData);
        } else {
            $payrollData['employee_id']    = $empId;
            $payrollData['payroll_period'] = $period;
            $payrollModel->insert($payrollData);
        }
    }

    return ['imported' => $imported, 'updated' => $updated, 'nextNumber' => $nextNumber];
}

/**
 * Scan the first ~12 rows of a sheet for a header cell matching one of the
 * given labels (case-insensitive) and return its column index.
 */
private function findHeaderColumn(array $rows, array $labels): ?int
{
    for ($r = 0; $r <= 11 && $r < count($rows); $r++) {
        foreach ($rows[$r] as $c => $val) {
            $cell = strtolower(trim((string) $val));
            if (in_array($cell, $labels, true)) {
                return $c;
            }
        }
    }
    return null;
}

/**
 * Return the first numeric, non-blank value in the given column across a
 * row range (an employee's block), or 0 if none found.
 */
private function firstNonBlankInColumn(array $rows, int $start, int $end, int $col): float
{
    for ($r = $start; $r < $end; $r++) {
        $raw = trim((string) ($rows[$r][$col] ?? ''));
        if ($raw !== '' && $raw !== '-') {
            $raw = str_replace([',', ' '], '', $raw);
            if (is_numeric($raw)) {
                return (float) $raw;
            }
        }
    }
    return 0.0;
}
}