<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id'; // Internal ID
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
protected $allowedFields    = [
        'employee_id',
        'full_name', 
        'office_id', 
        'position', 
        'contact_number',
        'salary_rate', 
        'employment_status', 
        'is_active',
        'atm_account_no'
    ];

protected $validationRules = [
        'employee_id' => 'required|is_unique[employees.employee_id]',
        'full_name'   => 'required',
        'position'    => 'required',
        'office_id'   => 'required|numeric'
    ];

    /**
     * Search functionality as described in the requirements
     */
    public function searchEmployees($keyword)
    {
        return $this->table($this->table)
                    ->like('employee_id', $keyword)
                    ->orLike('full_name', $keyword)
                    ->findAll();
    }
}