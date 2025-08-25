<?php
namespace App\Models;

use CodeIgniter\Model;

//keep the TasksUsersModel for assignment/unassignment and role lookups, but the TaskModel is responsible for fetching tasks.

class TasksUsersModel extends Model {
    protected $table      = 'tasks_users';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_user', 'id_task', 'role'];
    protected $returnType = 'array';

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
    public function getUsersByProjectId(int $projectId): array
    {
        return $this->db->table('user_project_role')
            ->select('Usuario.id_user, Usuario.name, Usuario.surnames')
            ->join('Usuario', 'Usuario.id_user = user_project_role.id_user')
            ->where('user_project_role.id_project', $projectId)
            ->groupBy('Usuario.id_user')
            ->get()
            ->getResultArray();
    }

     /**
     * Get users available for assignment based on the current user’s role:
     * - If the user is a manager or profile admin: return all users.
     * - If the user is a head of team: return users participating in any projects of that head.
     * - Otherwise: return only the current user.
     */
    public function getUsersForUserRole(int $currentUserId): array
    {
        $db = \Config\Database::connect();

        // Check for manager or admin
        $isAdmin = $db->table('Profile_Admin')->where('id_prof_admin', $currentUserId)->countAllResults() > 0;
        $isManager = $db->table('Manager')->where('id_manager', $currentUserId)->countAllResults() > 0;

        if ($isAdmin || $isManager) {
            return $db->table('Usuario')
                ->select('id_user, name, surnames')
                ->get()
                ->getResultArray();
        }

        // Check for head of team
        $isHead = $db->table('Head_of_Team')->where('id_head_of_team', $currentUserId)->countAllResults() > 0;
        if ($isHead) {
            return $db->table('user_project_role')
                ->select('Usuario.id_user, Usuario.name, Usuario.surnames')
                ->join('Usuario', 'Usuario.id_user = user_project_role.id_user')
                ->where('user_project_role.id_user !=', $currentUserId) // exclude self if desired
                ->whereIn('user_project_role.id_project', function ($builder) use ($currentUserId) {
                    return $builder->select('id_project')
                                   ->from('user_project_role')
                                   ->where('id_user', $currentUserId);
                })
                ->groupBy('Usuario.id_user')
                ->get()
                ->getResultArray();
        }

        // Default: return only the current user (for individual tasks)
        return $db->table('Usuario')
            ->select('id_user, name, surnames')
            ->where('id_user', $currentUserId)
            ->get()
            ->getResultArray();
    }
}
