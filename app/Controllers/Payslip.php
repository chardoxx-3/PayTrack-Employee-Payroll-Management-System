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