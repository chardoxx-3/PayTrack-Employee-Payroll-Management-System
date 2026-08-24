<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;

class Payslip extends BaseController
{
public function index()
{
    $officeModel = new \App\Models\OfficeModel();
    $data['offices'] = $officeModel->findAll();
    return view('payslip/search', $data);
}

public function preview($employee_id)
    {
        $model = new PayrollModel();
        $data['payslip'] = $model->select('payroll_records.*, employees.full_name, employees.position, deductions.*')
                                 ->join('employees', 'employees.id = payroll_records.employee_id')
                                 ->join('deductions', 'deductions.employee_id = payroll_records.employee_id', 'left')
                                 ->where('payroll_records.employee_id', $employee_id)
                                 ->orderBy('payroll_period', 'DESC')
                                 ->first();

        return view('payslip/preview', $data);
    }

    public function individual($employee_id)
    {
        $payrollModel = new \App\Models\PayrollModel();
        $employeeModel = new \App\Models\EmployeeModel();
        $deductionModel = new \App\Models\DeductionModel();
        $officeModel = new \App\Models\OfficeModel();

        $employee = $employeeModel->find($employee_id);
        if (!$employee) {
            throw \CodeIgniter\Exceptions\PageNotFoundExceptionException::forPageNotFound();
        }

        $payroll = $payrollModel->select('payroll_records.*')
            ->where('payroll_records.employee_id', $employee_id)
            ->orderBy('payroll_period', 'DESC')
            ->first();

        $deduction = $deductionModel->where('employee_id', $employee_id)->first();

        $data['employee'] = array_merge($employee, $deduction ?? []);
        $data['employee']['full_name'] = $employee['full_name'];
        $data['employee']['position'] = $employee['position'] ?? '';
        $data['employee']['salary_rate'] = $payroll['salary_rate'] ?? $employee['salary_rate'] ?? 0;
        $data['employee']['net_pay'] = $payroll['net_pay'] ?? 0;
        $data['employee']['first_quincena'] = $payroll['first_quincena'] ?? 0;
        $data['employee']['second_quincena'] = $payroll['second_quincena'] ?? 0;
        $data['employee']['refund_rata'] = $payroll['refund_rata'] ?? 0;
        $data['employee']['gsis_premium'] = $deduction['gsis_premium'] ?? 0;
        $data['employee']['gsis_policy'] = $deduction['gsis_policy'] ?? 0;
        $data['employee']['gsis_other'] = $deduction['gsis_other'] ?? 0;
        $data['employee']['pagibig_premium'] = $deduction['pagibig_premium'] ?? 0;
        $data['employee']['pagibig_loan'] = $deduction['pagibig_loan'] ?? 0;
        $data['employee']['phic'] = $deduction['phic'] ?? 0;
        $data['employee']['withholding_tax'] = $deduction['withholding_tax'] ?? 0;
        $data['employee']['bank_lbp'] = $deduction['bank_lbp'] ?? 0;
        $data['employee']['bank_mcc'] = $deduction['bank_mcc'] ?? 0;
        $data['employee']['bank_1stvb'] = $deduction['bank_1stvb'] ?? 0;

        $data['office_name'] = '';
        if ($employee['office_id']) {
            $office = $officeModel->find($employee['office_id']);
            $data['office_name'] = $office['office_name'] ?? '';
        }

        $data['period_label'] = $payroll['payroll_period'] ?? date('F Y');
        $data['cut_off'] = $payroll['cut_off'] ?? '';

        return view('payslip/individual', $data);
    }

    public function batchPrint()
    {
        $office_id = $this->request->getVar('office_id');
        $period = $this->request->getVar('period');

        $model = new PayrollModel();
        $data['records'] = $model->select('payroll_records.*, employees.full_name, employees.position, employees.employee_id as emp_code, deductions.*')
                                 ->join('employees', 'employees.id = payroll_records.employee_id')
                                 ->join('deductions', 'deductions.employee_id = payroll_records.employee_id', 'left')
                                 ->where('employees.office_id', $office_id)
                                 ->where('payroll_period', $period)
                                 ->findAll();

        return view('payslip/batch_print', $data);
    }
}