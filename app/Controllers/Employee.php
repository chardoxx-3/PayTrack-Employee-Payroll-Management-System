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

    $file = $this->request->getFile('payroll_file');
    if (!$file || !$file->isValid()) {
        return redirect()->to('/employee')->with('errors', 'Please select a valid Excel file to import.');
    }

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());

    $year = date('Y');
    $nextNumber = $model->like('employee_id', "EMP-{$year}-", 'after')->countAllResults() + 1;

    $imported = 0;
    $updated  = 0;

    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        // Skip supplemental RATA-only sheets — they duplicate employees already
        // covered by the main office sheets and carry no monthly rate.
        if (stripos($sheetName, 'rata') !== false) {
            continue;
        }

        $officeName = trim(preg_replace('/\s+\d+\s+\d+\s*$/', '', $sheetName));
        $office = $officeModel->where('office_name', $officeName)->first();
        $officeId = $office ? $office['id'] : $officeModel->insert(['office_name' => $officeName]);

        $rows = $spreadsheet->getSheetByName($sheetName)->toArray();

        foreach ($rows as $row) {
            $no          = $row[0] ?? null;
            $fullName    = trim($row[1] ?? '');
            $designation = trim($row[2] ?? '');
            $salaryRate  = $row[4] ?? null;

            // Real employee rows have a numeric "No." and a name; header/blank rows don't.
            if (!is_numeric($no) || $fullName === '') {
                continue;
            }

            $existing = $model->where('office_id', $officeId)
                               ->where('full_name', $fullName)
                               ->first();

            if ($existing) {
                $data = ['position' => $designation ?: $existing['position']];
                if (is_numeric($salaryRate) && (float) $salaryRate > 0) {
                    $data['salary_rate'] = (float) $salaryRate;
                }
                $model->update($existing['id'], $data);
                $updated++;
            } else {
                $employeeId = "EMP-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $nextNumber++;

                $model->insert([
                    'employee_id'       => $employeeId,
                    'full_name'         => $fullName,
                    'office_id'         => $officeId,
                    'position'          => $designation,
                    'salary_rate'       => is_numeric($salaryRate) ? (float) $salaryRate : 0,
                    'employment_status' => 'Regular',
                    'is_active'         => 1,
                ]);
                $imported++;
            }
        }
    }

    return redirect()->to('/employee')->with('success', "Import complete: {$imported} added, {$updated} updated.");
}
}