<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;
use App\Models\DeductionModel;

class Payroll extends BaseController
{
public function index()
{
    $officeId = $this->request->getVar('office_id');
    $period = date('Y-m');
    $empModel = new EmployeeModel();
    $officeModel = new \App\Models\OfficeModel();

    $data['offices'] = $officeModel->getOfficesOrdered();

    // Default to MAYOR'S office when no office_id is set (first page load)
    if (!$officeId && !empty($data['offices'])) {
        foreach ($data['offices'] as $office) {
            if (stripos($office['office_name'], 'MAYOR') !== false) {
                $officeId = $office['id'];
                break;
            }
        }
    }

$empModel->select('employees.*,
                    deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                    deductions.pagibig_premium, deductions.pagibig_loan,
                    deductions.phic, deductions.withholding_tax,
                    deductions.bank_lbp, deductions.bank_mcc, deductions.bank_1stvb,
                    payroll_records.id as payroll_id, payroll_records.refund_rata,
                    payroll_records.total_deductions, payroll_records.net_pay,
                    payroll_records.first_quincena, payroll_records.second_quincena')
             ->join('deductions', 'deductions.employee_id = employees.id', 'left')
             ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left');

    $data['employees'] = $officeId ?
        $empModel->where('employees.office_id', $officeId)->findAll() :
        $empModel->findAll();

    $data['office_id'] = $officeId;

    return view('payroll/index', $data);
}

public function process($employee_id)
    {
        $empModel = new EmployeeModel();
        $deductModel = new DeductionModel();
        $payrollModel = new PayrollModel();

        $emp = $empModel->find($employee_id);

        if (!$emp) {
            return redirect()->to('/payroll')->with('error', 'Employee not found.');
        }

        $deduct = $deductModel->where('employee_id', $employee_id)->first();

        $deduct = $deduct ?? [
            'withholding_tax' => 0,
            'loans'           => 0,
            'government_cont' => 0,
            'other_deduct'    => 0,
            'gsis_premium'    => 0,
            'gsis_policy'     => 0,
            'gsis_other'      => 0,
            'pagibig_premium' => 0,
            'pagibig_loan'    => 0,
            'phic'            => 0,
            'bank_lbp'        => 0,
            'bank_mcc'        => 0,
            'bank_1stvb'      => 0,
        ];

        $refund_rata = (float) ($this->request->getVar('refund_rata') ?? 0);

        $gross_pay = $emp['salary_rate'];

        $total_deductions =
            ($deduct['withholding_tax'] ?? 0) +
            ($deduct['loans'] ?? 0) +
            ($deduct['government_cont'] ?? 0) +
            ($deduct['other_deduct'] ?? 0) +
            ($deduct['gsis_premium'] ?? 0) +
            ($deduct['gsis_policy'] ?? 0) +
            ($deduct['gsis_other'] ?? 0) +
            ($deduct['pagibig_premium'] ?? 0) +
            ($deduct['pagibig_loan'] ?? 0) +
            ($deduct['phic'] ?? 0) +
            ($deduct['bank_lbp'] ?? 0) +
            ($deduct['bank_mcc'] ?? 0) +
            ($deduct['bank_1stvb'] ?? 0);

        $net_pay = $gross_pay - $total_deductions;

        $first_quincena_input  = (float) ($this->request->getVar('first_quincena') ?? 0);
        $second_quincena_input = (float) ($this->request->getVar('second_quincena') ?? 0);

        if ($first_quincena_input > 0 && $second_quincena_input > 0) {
            $first_quincena  = round($first_quincena_input, 2);
            $second_quincena = round($second_quincena_input, 2);
        } else {
            $first_quincena  = round($net_pay / 2, 2);
            $second_quincena = round($net_pay - $first_quincena, 2);
        }

        $period = date('Y-m');

        $payrollData = [
            'employee_id'       => $employee_id,
            'payroll_period'    => $period,
            'period_of_service' => $this->request->getVar('period_of_service') ?? date('m/01/Y') . '-' . date('m/t/Y'),
            'refund_rata'       => $refund_rata,
            'gross_pay'         => $gross_pay,
            'total_deductions'  => $total_deductions,
            'net_pay'           => $net_pay,
            'first_quincena'    => $first_quincena,
            'second_quincena'   => $second_quincena,
            'cash_paid'         => $net_pay,
        ];

        $existing = $payrollModel->where('employee_id', $employee_id)
                                  ->where('payroll_period', $period)
                                  ->first();

        if ($existing) {
            $payrollModel->update($existing['id'], $payrollData);
        } else {
            $payrollModel->insert($payrollData);
        }

        return redirect()->to('/payroll')->with('success', 'Payroll calculated for ' . $emp['full_name']);
    }
}