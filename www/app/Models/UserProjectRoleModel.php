<?php
namespace App\Models;

use CodeIgniter\Model;

class UserProjectRoleModel extends Model {
    protected $table      = 'user_project_role';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_user', 'id_role', 'id_project'];

    public function assignRole(int $userId, int $roleId, int $projectId) {
        return $this->insert([
            'id_user'    => $userId,
            'id_role'    => $roleId,
            'id_project' => $projectId,
        ]);
    }

    public function removeRole(int $userId, int $roleId, int $projectId) {
        return $this->where([
            'id_user'    => $userId,
            'id_role'    => $roleId,
            'id_project' => $projectId,
        ])->delete();
    }

    public function getRolesOfProjectByUser(int $userId, int $projectId) {
        return $this->where([
            'id_user'    => $userId,
            'id_project' => $projectId,
        ])->findAll();
    }

    public function isUserInProject(int $userId, int $projectId): bool {
        return $this->where([
            'id_user'    => $userId,
            'id_project' => $projectId,
        ])->countAllResults() > 0;
    }

    public function getUsersByProject(int $projectId) {
        return $this->where('id_project', $projectId)->findAll();
    }
    
    public function assignRole() {
        $uprModel = new UserProjectRoleModel();
        $uprModel->save([
            'id_user'    => $this->request->getPost('id_user'),
            'id_role'    => $this->request->getPost('id_role'),
            'id_project' => $this->request->getPost('id_project'),
        ]);

        return redirect()->back()->with('message', 'Rol asignado correctamente');
    }
}