<?php
namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model {
    protected $table      = 'Project';
    protected $primaryKey = 'id_project';

    protected $allowedFields = [
        'name', 'description', 'state', 'start_date',
        'end_date', 'simulated'
    ];
    public $returnType = 'array';

    public function getActive() {
        return $this->where('state', 'Active')->findAll();
    }

    public function addUserRole(int $projectId, int $userId, int $roleId) {
        $uprModel = new UserProjectRoleModel();
        return $uprModel->assignRole($userId, $roleId, $projectId);
    }
    public function getProjectById(int $projectId) {
        return $this->find($projectId);
    }
    public function removeUserRole(int $projectId, int $userId) {
        $uprModel = new UserProjectRoleModel();
        return $uprModel->removeRole($userId, $projectId);
    }

    public function isUserInProject(int $userId, int $projectId): bool {
        $uprModel = new UserProjectRoleModel();
        return $uprModel->isUserInProject($userId, $projectId);
    }

    public function getProjectUsers(int $projectId) {
        $uprModel = new UserProjectRoleModel();
        return $uprModel->getUsersByProject($projectId);
    }
    
    /**
     * Get projects for a user:
     * - If the user is a head of team, return only projects they participate in.
     * - Otherwise, return all projects.
     */
    public function getProjectsForUser(int $userId): array
    {
        $db = \Config\Database::connect();
        // Check if the user is a head of team
        $isHead = $db->table('Head_of_Team')->where('id_head_of_team', $userId)->countAllResults() > 0;

        if (! $isHead) {
            return $this->findAll();
        }

        // For head of team, join the user_project_role table
        return $this->select('Project.*')
            ->join('user_project_role', 'Project.id_project = user_project_role.id_project')
            ->where('user_project_role.id_user', $userId)
            ->groupBy('Project.id_project')
            ->findAll();
    }
}
