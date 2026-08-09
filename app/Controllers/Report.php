<?php

namespace App\Controllers;

use App\Models\PayrollModel;

class Report extends BaseController
{
    public function index()
    {
        return view('report/index');
    }

    public function generate()
    {
        $model = new PayrollModel();
        $type = $this->request->getVar('report_type'); // Office, Period, or Employee

        $query = $model->select('payroll_records.*, employees.full_name, offices.office_name')
                       ->join('employees', 'employees.id = payroll_records.employee_id')
                       ->join('offices', 'offices.id = employees.office_id');

        if ($this->request->getVar('office_id')) {
            $query->where('employees.office_id', $this->request->getVar('office_id'));
        }

        $data['results'] = $query->findAll();
        return view('report/summary', $data);
    }
}