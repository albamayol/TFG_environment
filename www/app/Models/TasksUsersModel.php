<?php
namespace App\Models;

use CodeIgniter\Model;

class TasksUsersModel extends Model
{
    protected $table      = 'tasks_users';
    protected $primaryKey = false; // clave compuesta
    protected $allowedFields = ['id_user', 'id_task', 'role'];

    public function assignUserToTask(int $userId, int $taskId, string $role)
    {
        return $this->insert([
            'id_user' => $userId,
            'id_task' => $taskId,
            'role'    => $role,
        ]);
    }

    public function removeUserFromTask(int $userId, int $taskId)
    {
        return $this->where([
            'id_user' => $userId,
            'id_task' => $taskId,
        ])->delete();
    }
    public function getUserRoleInTask(int $userId, int $taskId)
    {
        return $this->where([
            'id_user' => $userId,
            'id_task' => $taskId,
        ])->first();
    }

    public function getUsersByTask(int $taskId)
    {
        return $this->where('id_task', $taskId)->findAll();
    }

    public function getTasksByUser(int $userId)
    {
        return $this->where('id_user', $userId)->findAll();
    }
}
