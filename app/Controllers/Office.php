<?php

namespace App\Controllers;

use App\Models\OfficeModel;

class Office extends BaseController
{
public function store()
    {
        $model = new OfficeModel();
        $name = $this->request->getPost('office_name');

        if (!$name) {
            return $this->response->setJSON(['success' => false, 'message' => 'Office name is required.']);
        }

        $id = $model->insert(['office_name' => $name]);

        return $this->response->setJSON([
            'success' => true,
            'office'  => ['id' => $id, 'office_name' => $name]
        ]);
    }

    public function delete($id)
    {
        $model = new OfficeModel();

        // Prevent deleting an office still assigned to employees
        $inUse = $model->db->table('employees')->where('office_id', $id)->countAllResults();
        if ($inUse > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Cannot delete — {$inUse} employee(s) are still assigned to this office."
            ]);
        }

        $model->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
}