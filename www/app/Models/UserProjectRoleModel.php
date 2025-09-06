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

    public function getProjectIdsByUserId(int $userId): array {
        return $this->where('id_user', $userId)->findAll();
    }

    public function isUserInProject(int $userId, int $projectId): bool {
        return $this->where('id_user', $userId)
                ->where('id_project', $projectId)
                ->select('1')
                ->limit(1)
                ->get()
                ->getNumRows() > 0;
    }   
    
    public function getUserEmailsByProjectId(int $projectId): array {
        return $this->select('Usuario.id_user, Usuario.email')
            ->join('Usuario', 'Usuario.id_user = user_project_role.id_user')
            ->where('user_project_role.id_project', $projectId)
            ->groupBy('Usuario.id_user, Usuario.email')
            ->get()
            ->getResultArray();
    }

    /**
     * Participants of every project the given user participates in
     * (useful for Heads of Team).
     */
    public function getParticipantsEmailsForUserProjects(int $userId): array {
        return $this->select('Usuario.id_user, Usuario.email')
            ->join('Usuario', 'Usuario.id_user = user_project_role.id_user')
            ->whereIn('user_project_role.id_project', function($b) use ($userId) {
                return $b->select('id_project')
                        ->from($this->table)
                        ->where('id_user', $userId);
            })
            ->groupBy('Usuario.id_user, Usuario.email')
            ->get()
            ->getResultArray();
    }
}       