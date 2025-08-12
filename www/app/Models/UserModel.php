<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'User';
    protected $primaryKey = 'id_user';

    protected $allowedFields = [
        'name', 'surnames', 'email', 'password', 'birthdate', 'address',
        'dni_nie', 'telephone', 'soft_skills', 'technical_skills', 'simulated'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'email'    => 'required|valid_email|is_unique[User.email,id_user,{id_user}]',
        'password' => 'required|min_length[8]',
        'name'     => 'required',
        'surnames' => 'required'
    ];

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function getRoles(int $userId)
    {
        return $this->db->table('user_project_role')
            ->select('user_project_role.*, Role.name AS role_name')
            ->join('Role', 'Role.id_role = user_project_role.id_role')
            ->where('id_user', $userId)
            ->get()->getResultArray();
    }
}
