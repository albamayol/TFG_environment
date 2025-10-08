<?php
namespace App\Models;

use CodeIgniter\Model;

class ManagerModel extends Model {
    protected $table      = 'Manager';
    protected $primaryKey = 'id_manager';
    protected $allowedFields = ['id_manager'];

    public function isManager(int $userId): bool {
        return $this->where('id_manager', $userId)->countAllResults() > 0;
    }

    public function getAllManagers() {
        $userModel = new UserModel();
        return $this->select('Manager.id_manager, Usuario.name, Usuario.surnames, Usuario.email')
                    ->join('Usuario', 'Manager.id_manager = Usuario.id_user')
                    ->findAll();
    }
}
