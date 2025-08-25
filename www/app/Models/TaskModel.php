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

    public $returnType = 'array';

    protected $useTimestamps = false;

    public function findByProject(int $projectId) {
        return $this->where('id_project', $projectId)->findAll();
    }

    public function assignUser(int $taskId, int $userId, string $role) {
        $tasksUsers = new TasksUsersModel();
        return $tasksUsers->assignUserToTask($userId, $taskId, $role);
    }

    public function overdue() {
        return $this->where('limit_date <', utcNow()->toDateTimeString())
                    ->where('state !=', 'Done')
                    ->findAll();
    }

    /**
     * Return all tasks assigned to a specific user.
     */
    public function getTasksForUser(int $userId): array
    {
        return $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->findAll();
    }

    /**
     * Return tasks assigned to a user with limit_date within a specific range.
     */
    public function getTasksForUserInRange(int $userId, string $startDate, string $endDate): array
    {
        return $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('limit_date >=', $startDate)
            ->where('limit_date <', $endDate)
            ->findAll();
    }

    /**
     * Return tasks assigned to a user that are past their limit_date and not marked as Done.
     */
    public function getOverdueTasksForUser(int $userId): array {
        return $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('Task.limit_date <', utcNow()->toDateTimeString())
            ->where('Task.state !=', 'Done')
            ->findAll();
    }

     /**
     * Return today’s tasks for a user.
     */
    public function getTodayTasksForUser(int $userId): array {
        [$start, $end] = getUtcDayBounds();
        return $this->getTasksForUserInRange(
            $userId,
            $start->toDateTimeString(),
            $end->toDateTimeString()
        );
    }
}
