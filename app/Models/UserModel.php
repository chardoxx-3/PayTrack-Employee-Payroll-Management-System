<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'password', 'name', 'role', 'created_at'];

    // Validation rules to ensure no duplicate usernames as per requirements
    protected $validationRules = [
        'username' => 'required|is_unique[users.username]|min_length[4]',
        'password' => 'required|min_length[6]',
        'role'     => 'required'
    ];
    
    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken. Please choose another.'
        ]
    ];
}