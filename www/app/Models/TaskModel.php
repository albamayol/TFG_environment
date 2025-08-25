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

    private function applyOrdering($builder) {
        // Priority order: Urgent (1), High (2), Medium (3), Low (4), others (5)
        $priorityCase = "(CASE Task.priority
                            WHEN 'Urgent' THEN 1
                            WHEN 'High'   THEN 2
                            WHEN 'Medium' THEN 3
                            WHEN 'Low'    THEN 4
                            ELSE 5
                        END)";

        // Order by priority asc, then earliest due date asc (NULLs last), then duration asc
        // NOTE: For NULLS LAST on MySQL < 8, emulate with IS NULL predicate first
        $builder->orderBy($priorityCase, 'ASC', false);

        // Emulate NULLS LAST: first non-NULLs (0), then NULLs (1)
        $builder->orderBy('(Task.limit_date IS NULL)', 'ASC', false);
        $builder->orderBy('Task.limit_date', 'ASC');
        $builder->orderBy('Task.duration', 'ASC');

        return $builder;
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
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId);
        
        $builder = $this->applyOrdering($builder);
        return $builder->findAll();
    }

    /**
     * Return tasks assigned to a user with limit_date within a specific range.
     */
    public function getTasksForUserInRange(int $userId, string $startDate, string $endDate): array
    {
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('limit_date >=', $startDate)
            ->where('limit_date <', $endDate);

        $builder = $this->applyOrdering($builder);
        return $builder->findAll();
    }

    /**
     * Return tasks assigned to a user that are past their limit_date and not marked as Done.
     */
    public function getOverdueTasksForUser(int $userId): array {
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('Task.limit_date <', utcNow()->toDateTimeString())
            ->where('Task.state !=', 'Done');

        $builder = $this->applyOrdering($builder);
        return $builder->findAll();
    }

    
    /**
     * New: tasks without a limit_date (for "Later" bucket).
     */
    public function getTasksForUserWithNoLimitDate(int $userId): array
    {
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('Task.limit_date', null);

        $this->applyOrdering($builder);

        return $builder->findAll();
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
