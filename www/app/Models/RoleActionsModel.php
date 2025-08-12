<?php
namespace App\Models;

use CodeIgniter\Model;

class RoleActionsModel extends Model
{
    protected $table      = 'role_actions';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_actions', 'id_role'];

    public function addActionToRole(int $actionId, int $roleId)
    {
        return $this->insert([
            'id_actions' => $actionId,
            'id_role'    => $roleId,
        ]);
    }

    public function removeActionFromRole(int $actionId, int $roleId)
    {
        return $this->where([
            'id_actions' => $actionId,
            'id_role'    => $roleId,
        ])->delete();
    }
}
