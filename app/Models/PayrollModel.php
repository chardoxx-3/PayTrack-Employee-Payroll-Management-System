<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table            = 'payroll_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
protected $allowedFields    = [
        'employee_id', 
        'payroll_period', 
        'period_of_service',
        'refund_rata',
        'gross_pay', 
        'total_deductions', 
        'net_pay', 
        'first_quincena',
        'second_quincena',
        'cash_paid',
        'processed_by'
    ];

    /**
     * Logic for Batch Payslip Printing
     * Retrieves all processed payroll for a specific office and period
     */
    public function getBatchData($office_id, $period)
    {
        return $this->select('payroll_records.*, employees.full_name, employees.position, offices.office_name')
                    ->join('employees', 'employees.id = payroll_records.employee_id')
                    ->join('offices', 'offices.id = employees.office_id')
                    ->where('employees.office_id', $office_id)
                    ->where('payroll_records.payroll_period', $period)
                    ->findAll();
    }

    /**
     * Summary Report logic
     */
    public function getPayrollSummary($period)
    {
        return $this->selectSum('gross_pay')
                    ->selectSum('total_deductions')
                    ->selectSum('net_pay')
                    ->where('payroll_period', $period)
                    ->first();
    }
}