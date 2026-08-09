<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    public function index()
    {
        if (session()->get('role') != 'admin') return redirect()->to('/dashboard');
        
        $model = new UserModel();
        $data['users'] = $model->findAll();
        return view('user/index', $data);
    }

    public function register()
    {
        $model = new UserModel();
        
        $data = [
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getVar('role'),
            'name'     => $this->request->getVar('name')
        ];

        // Ensure duplicate usernames are not allowed
        if ($model->where('username', $data['username'])->first()) {
            return redirect()->back()->with('error', 'Username already exists.');
        }

        $model->save($data);
        return redirect()->to('/user')->with('success', 'New user registered.');
    }

    public function settings()
    {
        // Account settings for the logged-in user to update their own password
        return view('user/settings');
    }
}