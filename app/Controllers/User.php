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

    public function updatePassword()
    {
        $userId = session()->get('id');
        if (!$userId) return redirect()->to('/auth/login');

        $model = new UserModel();
        $user = $model->find($userId);

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        if (empty($newPassword) || strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters long.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New password and confirmation do not match.');
        }

        $model->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);
        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}