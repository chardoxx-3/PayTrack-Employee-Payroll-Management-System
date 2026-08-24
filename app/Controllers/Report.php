<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\OfficeModel;

class Report extends BaseController
{
    public function index()
    {
        $officeModel = new OfficeModel();
        $payrollModel = new PayrollModel();

        $data['offices'] = $officeModel->getOfficesOrdered();
        $data['period'] = date('Y-m');

        $summary = $payrollModel->getPayrollSummary($data['period']);
        $data['total_gross'] = $summary['gross_pay'] ?? 0;
        $data['total_deductions'] = $summary['total_deductions'] ?? 0;
        $data['total_net'] = $summary['net_pay'] ?? 0;
        $data['total_employees'] = $payrollModel->where('payroll_period', $data['period'])->countAllResults();

        return view('report/index', $data);
    }

    public function generate()
    {
        $payrollModel = new PayrollModel();
        $officeModel = new OfficeModel();

        $type = $this->request->getVar('report_type') ?? 'period';
        $period = $this->request->getVar('period') ?? date('Y-m');
        $officeId = $this->request->getVar('office_id') ?? 'all';

        $data['report_type'] = $type;
        $data['period'] = $period;
        $data['office_id'] = $officeId;

        if ($type === 'office') {
            $data['results'] = [];
            $offices = $officeModel->getOfficesOrdered();
            foreach ($offices as $office) {
                $officeData = $payrollModel->select('payroll_records.*, employees.full_name, employees.position, offices.office_name')
                    ->join('employees', 'employees.id = payroll_records.employee_id')
                    ->join('offices', 'offices.id = employees.office_id')
                    ->where('employees.office_id', $office['id'])
                    ->where('payroll_records.payroll_period', $period)
                    ->findAll();

                if (!empty($officeData)) {
                    $totalGross = 0;
                    $totalDeduct = 0;
                    $totalNet = 0;
                    foreach ($officeData as $row) {
                        $totalGross += $row['gross_pay'];
                        $totalDeduct += $row['total_deductions'];
                        $totalNet += $row['net_pay'];
                    }
                    $data['results'][] = [
                        'office_name' => $office['office_name'],
                        'employees' => $officeData,
                        'total_gross' => $totalGross,
                        'total_deductions' => $totalDeduct,
                        'total_net' => $totalNet,
                        'count' => count($officeData),
                    ];
                }
            }
            $data['office_name'] = 'All Offices';
        } elseif ($type === 'deductions') {
            $query = $payrollModel->select('payroll_records.*, employees.full_name, offices.office_name, deductions.*')
                ->join('employees', 'employees.id = payroll_records.employee_id')
                ->join('offices', 'offices.id = employees.office_id')
                ->join('deductions', 'deductions.employee_id = employees.id', 'left')
                ->where('payroll_records.payroll_period', $period);

            if ($officeId !== 'all') {
                $query->where('employees.office_id', $officeId);
                $office = $officeModel->find($officeId);
                $data['office_name'] = $office['office_name'] ?? 'Unknown';
            } else {
                $data['office_name'] = 'All Offices';
            }

            $data['results'] = $query->findAll();
        } else {
            $query = $payrollModel->select('payroll_records.*, employees.full_name, employees.position, offices.office_name')
                ->join('employees', 'employees.id = payroll_records.employee_id')
                ->join('offices', 'offices.id = employees.office_id')
                ->where('payroll_records.payroll_period', $period);

            if ($officeId !== 'all') {
                $query->where('employees.office_id', $officeId);
                $office = $officeModel->find($officeId);
                $data['office_name'] = $office['office_name'] ?? 'Unknown';
            } else {
                $data['office_name'] = 'All Offices';
            }

            $data['results'] = $query->findAll();
        }

        return view('report/summary', $data);
    }
}