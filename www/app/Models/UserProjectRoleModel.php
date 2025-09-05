<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\ProjectModel;

class UserProjectRoleModel extends Model {
    protected $table      = 'user_project_role';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_user', 'id_role', 'id_project'];

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $projectModel;

    public function __construct() {
        parent::__construct();
        $this->projectModel = new ProjectModel();
    }

    public function assignRole(int $userId, int $roleId, int $projectId) {
        return $this->insert([
            'id_user'    => $userId,
            'id_role'    => $roleId,
            'id_project' => $projectId,
        ]);
    }

    public function getRolesOfProjectByUser(int $userId, int $projectId) {
        return $this->where([
            'id_user'    => $userId,
            'id_project' => $projectId,
        ])->findAll();
    }

    public function getUsersByProject(int $projectId) {
        return $this->where('id_project', $projectId)->findAll();
    }

    public function isUserInProject(int $userId, int $projectId): bool {
        return $this->where('id_user', $userId)
                ->where('id_project', $projectId)
                ->select('1')
                ->limit(1)
                ->get()
                ->getNumRows() > 0;
    }           
}       