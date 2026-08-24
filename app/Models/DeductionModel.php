<?php

namespace App\Models;

use CodeIgniter\Model;

class DeductionModel extends Model
{
    protected $table            = 'deductions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
protected $allowedFields    = [
    'employee_id', 
    'withholding_tax', 
    'loans', 
    'government_cont', 
    'other_deduct',
    'gsis_premium',
    'gsis_policy',
    'gsis_other',
    'gsis_ouli',           // NEW — GSIS "OULI" row, stacks under the same GSIS column as gsis_premium
    'gsis_diff',           // NEW — GSIS "DIFF-GSIS" row, stacks under the same GSIS column
    'pagibig_premium',
    'pagibig_loan',
    'pagibig_mp2',         // NEW — Pag-IBIG "MP2" row, stacks under the same column as pagibig_loan
    'phic',
    'phic_diff',           // NEW — "PHIC-Diff" row, stacks under the same column as phic
    'bank_lbp',
    'bank_other_payables', // NEW — "Other Payables" row, stacks under the same column as bank_lbp
    'bank_mcc',
    'bank_1stvb',
    'bank_rbt'             // NEW — "RBT" row, stacks under the same column as bank_1stvb
];

    /**
     * Get deductions joined with employee name for the management UI
     */
    public function getDeductionsByOffice($office_id)
    {
        return $this->select('deductions.*, employees.full_name, employees.employee_id as emp_code')
                    ->join('employees', 'employees.id = deductions.employee_id')
                    ->where('employees.office_id', $office_id)
                    ->findAll();
    }

    /**
 * Search employees + their deductions for the Deduction management search page.
 * RIGHT JOIN so employees with no deduction row yet still show up (defaults to 0).
 */
public function searchWithDeductions($keyword = null, $office_id = null, $period = null)
{
    $period = $period ?? date('Y-m');

    $builder = $this->select('employees.id as id, employees.employee_id as emp_code, employees.full_name, 
                               employees.position, employees.office_id, employees.salary_rate,
                               deductions.withholding_tax, deductions.loans, deductions.government_cont, deductions.other_deduct,
                               deductions.gsis_premium, deductions.gsis_policy, deductions.gsis_other,
                               deductions.gsis_ouli, deductions.gsis_diff,
                               deductions.pagibig_premium, deductions.pagibig_loan, deductions.pagibig_mp2, deductions.phic,
                               deductions.phic_diff,
                               deductions.bank_lbp, deductions.bank_other_payables, deductions.bank_mcc,
                               deductions.bank_1stvb, deductions.bank_rbt,
                               payroll_records.id as payroll_id, payroll_records.refund_rata,
                               payroll_records.net_pay, payroll_records.first_quincena, payroll_records.second_quincena')
                     ->join('employees', 'employees.id = deductions.employee_id', 'right')
                     ->join('payroll_records', "payroll_records.employee_id = employees.id AND payroll_records.payroll_period = '{$period}'", 'left');

    if (!empty($keyword)) {
        $builder->groupStart()
                ->like('employees.full_name', $keyword)
                ->orLike('employees.employee_id', $keyword)
                ->groupEnd();
    }

    if (!empty($office_id)) {
        $builder->where('employees.office_id', $office_id);
    }

    return $builder->orderBy('employees.full_name', 'ASC')->findAll();
}
}