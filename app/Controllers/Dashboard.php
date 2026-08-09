<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to('/auth/login');

        // Logic to fetch overview counts for the dashboard cards
        $db = \Config\Database::connect();
        $data['total_employees'] = $db->table('employees')->countAll();
        $data['total_offices'] = $db->table('offices')->countAll();
        
        return view('dashboard/index', $data);
    }
}