<?php
namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model {
    protected $table      = 'Role';
    protected $primaryKey = 'id_role';

    protected $allowedFields = ['name', 'description', 'skills', 'simulated'];

    public function getActionsByRole(int $roleId) {
        return $this->db->table('role_actions')
            ->select('Action.*')
            ->join('Action', 'Action.id_actions = role_actions.id_actions')
            ->where('role_actions.id_role', $roleId)
            ->get()->getResultArray();
    }

    public function assignActionToRole(int $roleId, int $actionId) {
        $roleAction = new RoleActionsModel();
        return $roleAction->addActionToRole($actionId, $roleId);
    }
    
    public function removeActionFromRole(int $roleId, int $actionId) {
        $roleAction = new RoleActionsModel();
        return $roleAction->removeActionFromRole($actionId, $roleId);
    }
    
    public function getRoleByName(string $name) {
        return $this->where('name', $name)->first();
    }
    
    public function getRoleById(int $id) {
        return $this->find($id);
    }
    
    public function getAllRoles(){
        return $this->findAll();
    }
    
    public function addRole(array $data) {
        return $this->insert($data);
    }
    
    public function updateRole(int $id, array $data) {
        return $this->update($id, $data);
    }
}
