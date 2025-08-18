<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table      = 'Task';
    protected $primaryKey = 'id_task';

    protected $allowedFields = [
        'id_project', 'name', 'description', 'state', 'priority',
        'limit_date', 'duration', 'origin_of_task', 'person_of_interest', 'simulated'
    ];

    protected $useTimestamps = false;

    public function findByProject(int $projectId) {
        return $this->where('id_project', $projectId)->findAll();
    }

    public function assignUser(int $taskId, int $userId, string $role) {
        $tasksUsers = new TasksUsersModel();
        return $tasksUsers->assignUserToTask($userId, $taskId, $role);
    }

    public function overdue() {
        return $this->where('limit_date <', date('Y-m-d H:i:s'))
                    ->where('state !=', 'Done')
                    ->findAll();
    }
}
