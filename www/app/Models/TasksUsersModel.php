<?php
namespace App\Models;

use CodeIgniter\Model;

class TasksUsersModel extends Model {
    protected $table      = 'tasks_users';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_user', 'id_task', 'role'];
    protected $returnType = 'array';
    protected $userModel;
    protected $userProjectRoleModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->userProjectRoleModel = new UserProjectRoleModel();
    }

    public function assignUserToTask(int $userId, int $taskId, string $role) {
        return $this->insert([
            'id_user' => $userId,
            'id_task' => $taskId,
            'role'    => $role,
        ]);
    }

    public function removeUserFromTask(int $userId, int $taskId) {
        return $this->where([
            'id_user' => $userId,
            'id_task' => $taskId,
        ])->delete();
    }
    public function getUserRoleInTask(int $userId, int $taskId) {
        return $this->where([
            'id_user' => $userId,
            'id_task' => $taskId,
        ])->first();
    }

    public function getUsersByTask(int $taskId) {
        return $this->where('id_task', $taskId)->findAll();
    }

    public function getTasksByUser(int $userId) {
        return $this->where('id_user', $userId)->findAll();
    }

    /**
     * Get the users participating in a project.
     */
    public function getUsersByProjectId(int $projectId): array {
        return $this->userProjectRoleModel->getUserEmailsByProjectId($projectId);
    }

     /**
     * Get users available for assignment based on the current user’s role:
     * - If the user is a manager or profile admin: return all users.
     * - If the user is a head of team: return users participating in any projects of that head.
     * - Otherwise: return only the current user.
     */
    public function getUsersForUserRole(int $currentUserId): array {

        // Check for manager or admin
        $isAdmin = $this->userModel->isRole('Profile_Admin', $currentUserId);
        $isManager = $this->userModel->isRole('Manager', $currentUserId);

        if ($isAdmin || $isManager) {
            return $this->userModel->getSmallInfoForDisplay();
        }

        // Check for head of team
        $isHead = $this->userModel->isRole('Head_Of_Team', $currentUserId);
        if ($isHead) {
            return $this->userProjectRoleModel->getParticipantsEmailsForUserProjects($currentUserId);
        }

        // Default: return only the current user (for individual tasks)
        return $this->userModel->getSmallInfoForDisplayById($currentUserId);
    }

    public function getTasksWithAssigneesByProject(int $projectId): array {
        return $this->db->table('Task t')
        ->select("
            t.id_task,
            t.name,
            t.state,
            t.priority,
            t.limit_date,
            t.duration,
            t.simulated,
            GROUP_CONCAT(DISTINCT u.email ORDER BY u.email SEPARATOR ', ') AS assignees
        ")
        ->join('tasks_users', 'tasks_users.id_task = t.id_task', 'left')     // keep tasks with no assignees
        ->join('Usuario u', 'u.id_user = tasks_users.id_user', 'left')
        ->where('t.id_project', $projectId)
        ->groupBy('t.id_task, t.name, t.state, t.priority, t.limit_date, t.duration, t.simulated')
        ->orderBy('(t.limit_date IS NULL)', 'ASC', false)
        ->orderBy('t.limit_date', 'ASC')
        ->orderBy('t.duration', 'ASC')
        ->get()
        ->getResultArray();
    }
}
