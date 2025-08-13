<?php
namespace App\Models;

use CodeIgniter\Model;

class ProfileAdminModel extends Model
{
    protected $table      = 'Profile_Admin';
    protected $primaryKey = 'id_prof_admin';
    protected $allowedFields = ['id_prof_admin'];

    public function isAdmin(int $userId): bool
    {
        return $this->where('id_prof_admin', $userId)->countAllResults() > 0;
    }
}
