<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'name'     => 'System Administrator',
            'role'     => 'admin',
        ];

        $this->db->table('users')->ignore(true)->insert($data);
    }
}
