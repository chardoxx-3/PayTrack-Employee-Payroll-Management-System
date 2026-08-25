<?php

namespace App\Controllers;

use App\Models\DeductionModel;
use App\Models\EmployeeModel;

class Deduction extends BaseController
{
public function manage($employee_id)
{
    $empModel = new EmployeeModel();
    $deductModel = new DeductionModel();
    $payrollModel = new \App\Models\PayrollModel();

    $data['employee'] = $empModel->select('employees.*, offices.office_name')
                                  ->join('offices', 'offices.id = employees.office_id', 'left')
                                  ->find($employee_id);
    $data['deductions'] = $deductModel->where('employee_id', $employee_id)->first();

    // Pull the latest payroll record just to prefill the refund/rata amount
    $latestPayroll = $payrollModel->where('employee_id', $employee_id)
                                   ->orderBy('id', 'DESC')
                                   ->first();
    $data['refund_rata'] = $latestPayroll['refund_rata'] ?? 0;

    return view('deduction/manage', $data);
}

// NEW
public function update()
{
    $model = new DeductionModel();
    $emp_id = $this->request->getVar('employee_id');

    $data = [
        'withholding_tax'  => $this->request->getVar('tax'),
        'loans'            => $this->request->getVar('loans'),
        'government_cont'  => $this->request->getVar('gov'),
        'other_deduct'     => $this->request->getVar('others'),
        'gsis_premium'     => $this->request->getVar('gsis_premium'),
        'gsis_policy'      => $this->request->getVar('gsis_policy'),
        'gsis_other'       => $this->request->getVar('gsis_other'),
        'gsis_ouli'        => $this->request->getVar('gsis_ouli'),          // NEW
        'gsis_diff'        => $this->request->getVar('gsis_diff'),          // NEW
        'pagibig_premium'  => $this->request->getVar('pagibig_premium'),
        'pagibig_loan'     => $this->request->getVar('pagibig_loan'),
        'pagibig_mp2'      => $this->request->getVar('pagibig_mp2'),        // NEW
        'phic'             => $this->request->getVar('phic'),
        'phic_diff'        => $this->request->getVar('phic_diff'),         // NEW
        'bank_lbp'         => $this->request->getVar('bank_lbp'),
        'bank_other_payables' => $this->request->getVar('bank_other_payables'), // NEW
        'bank_mcc'         => $this->request->getVar('bank_mcc'),
        'bank_1stvb'       => $this->request->getVar('bank_1stvb'),
        'bank_rbt'         => $this->request->getVar('bank_rbt'),           // NEW
    ];

    // Handle case where no deduction row exists yet for this employee
    $existing = $model->where('employee_id', $emp_id)->first();
    if ($existing) {
        $model->where('employee_id', $emp_id)->set($data)->update();
    } else {
        $data['employee_id'] = $emp_id;
        $model->insert($data);
    }

    // Save Monthly Rate back to the employees table
    // (the manage.php form's salary_rate field posts here, but was never persisted)
    $empModel = new \App\Models\EmployeeModel();
    $salary_rate = $this->request->getVar('salary_rate');
    if ($salary_rate !== null && $salary_rate !== '') {
        $empModel->update($emp_id, ['salary_rate' => (float) $salary_rate]);
    }

    // NEW — persist the ATM/bank account number shown in the payroll's SIGNATURE column
    $atm_account_no = $this->request->getVar('atm_account_no');
    if ($atm_account_no !== null) {
        $empModel->update($emp_id, ['atm_account_no' => $atm_account_no]);
    }

    // Recalculate & save the current period's payroll record so the Payroll list
    // reflects the updated Monthly Rate and deductions right away, without
    // needing to click "Process" again just to refresh the numbers.
    $payrollModel = new \App\Models\PayrollModel();
    $period = date('Y-m');
    $refund_rata = (float) ($this->request->getVar('refund_rata') ?? 0);

    $gross_pay = (float) ($salary_rate !== null && $salary_rate !== ''
        ? $salary_rate
        : ($empModel->find($emp_id)['salary_rate'] ?? 0));

    $total_deductions =
        (float) ($data['withholding_tax'] ?? 0) +
        (float) ($data['loans'] ?? 0) +
        (float) ($data['government_cont'] ?? 0) +
        (float) ($data['other_deduct'] ?? 0) +
        (float) ($data['gsis_premium'] ?? 0) +
        (float) ($data['gsis_policy'] ?? 0) +
        (float) ($data['gsis_other'] ?? 0) +
        (float) ($data['gsis_ouli'] ?? 0) +           // NEW
        (float) ($data['gsis_diff'] ?? 0) +            // NEW
        (float) ($data['pagibig_premium'] ?? 0) +
        (float) ($data['pagibig_loan'] ?? 0) +
        (float) ($data['pagibig_mp2'] ?? 0) +           // NEW
        (float) ($data['phic'] ?? 0) +
        (float) ($data['phic_diff'] ?? 0) +             // NEW
        (float) ($data['bank_lbp'] ?? 0) +
        (float) ($data['bank_other_payables'] ?? 0) +   // NEW
        (float) ($data['bank_mcc'] ?? 0) +
        (float) ($data['bank_1stvb'] ?? 0) +
        (float) ($data['bank_rbt'] ?? 0);               // NEW

    $net_pay = ($gross_pay + $refund_rata) - $total_deductions;

    $existingPayroll = $payrollModel->where('employee_id', $emp_id)
                                     ->where('payroll_period', $period)
                                     ->first();

    if ($existingPayroll && isset($existingPayroll['first_quincena']) && (float)$existingPayroll['first_quincena'] > 0 && (float)$existingPayroll['first_quincena'] <= $net_pay) {
        $first_quincena  = (float)$existingPayroll['first_quincena'];
        $second_quincena = round($net_pay - $first_quincena, 2);
    } else {
        $first_quincena  = round($net_pay / 2, 2);
        $second_quincena = round($net_pay - $first_quincena, 2);
    }

    $payrollData = [
        'refund_rata'      => $refund_rata,
        'gross_pay'        => $gross_pay,
        'total_deductions' => $total_deductions,
        'net_pay'          => $second_quincena,
        'first_quincena'   => $first_quincena,
        'second_quincena'  => $second_quincena,
        'cash_paid'        => $second_quincena,
    ];

    if ($existingPayroll) {
        $payrollModel->update($existingPayroll['id'], $payrollData);
    } else {
        $payrollData['employee_id']    = $emp_id;
        $payrollData['payroll_period'] = $period;
        $payrollModel->insert($payrollData);
    }

    return redirect()->to('/payroll')->with('success', 'Deductions updated.');
}

    public function index()
{
    $empModel    = new EmployeeModel();
    $officeModel = new \App\Models\OfficeModel();

    $keyword  = $this->request->getVar('search');
    $officeId = $this->request->getVar('office_id');
    $period   = date('Y-m');

    $data['offices'] = $officeModel->findAll();

    if (!$officeId && !empty($data['offices'])) {
        foreach ($data['offices'] as $office) {
            if (stripos($office['office_name'], 'MAYOR') !== false) {
                $officeId = $office['id'];
                break;
            }
        }
    }

    $empModel->select('employees.*, offices.office_name,
                        deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                        deductions.gsis_ouli, deductions.gsis_diff,
                        deductions.pagibig_premium, deductions.pagibig_loan, deductions.pagibig_mp2,
                        deductions.phic, deductions.phic_diff, deductions.withholding_tax,
                        deductions.loans, deductions.government_cont, deductions.other_deduct,
                        deductions.bank_lbp, deductions.bank_other_payables, deductions.bank_mcc,
                        deductions.bank_1stvb, deductions.bank_rbt,
                        payroll_records.id as payroll_id, payroll_records.refund_rata,
                        payroll_records.net_pay, payroll_records.first_quincena, payroll_records.second_quincena')
             ->join('offices', 'offices.id = employees.office_id', 'left')
             ->join('deductions', 'deductions.employee_id = employees.id', 'left')
             ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left');

    if ($officeId) $empModel->where('employees.office_id', $officeId);
    if ($keyword)  $empModel->groupStart()
                             ->like('employees.full_name', $keyword)
                             ->orLike('employees.employee_id', $keyword)
                             ->groupEnd();

    $data['records']   = $empModel->orderBy('employees.id', 'ASC')->findAll();
    $data['offices']   = $officeModel->findAll();
    $data['search']    = $keyword;
    $data['office_id'] = $officeId;

    return view('deduction/index', $data);
}
}