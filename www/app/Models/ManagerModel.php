<?php
namespace App\Models;

use CodeIgniter\Model;

class ManagerModel extends Model
{
    protected $table      = 'Manager';
    protected $primaryKey = 'id_manager';
    protected $allowedFields = ['id_manager'];

    public function isManager(int $userId): bool
    {
        return $this->where('id_manager', $userId)->countAllResults() > 0;
    }
}
