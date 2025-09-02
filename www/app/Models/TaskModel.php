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
        //Priority order: Urgent (1), High (2), Medium (3), Low (4), others (5)
        $priorityCase = "(CASE Task.priority
                            WHEN 'Urgent' THEN 1
                            WHEN 'High'   THEN 2
                            WHEN 'Medium' THEN 3
                            WHEN 'Low'    THEN 4
                            ELSE 5
                        END)";

        //Order by priority asc, then earliest due date asc (NULLs last), then duration asc
        $builder->orderBy($priorityCase, 'ASC', false);
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
        $now = utcNow();

        $lowerBound = ($now > $start) ? $now : $start;
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('Task.limit_date >=', $lowerBound->toDateTimeString())
            ->where('Task.limit_date <',  $end->toDateTimeString());

        $builder = $this->applyOrdering($builder);
        return $builder->findAll();
    }

    public function getTasksForUserFromDate(int $userId, string $startDate): array {
        $builder = $this->select('Task.*')
            ->join('tasks_users', 'Task.id_task = tasks_users.id_task')
            ->where('tasks_users.id_user', $userId)
            ->where('Task.limit_date >=', $startDate);

        $builder = $this->applyOrdering($builder);
        return $builder->findAll();
    }
    
    public function isOwnerByEmail(int $taskId, string $email): bool {
        $email = trim($email);
        if ($email === '') return false;

        $row = $this->select('person_of_interest')
                    ->where('id_task', $taskId)
                    ->first();

        if (!$row) return false;

        $poi = trim((string)($row['person_of_interest'] ?? ''));
        return ($poi !== '' && strcasecmp($poi, $email) === 0);
    }

    /**
     * Delete task and related links in tasks_users in a transaction.
     * Returns true on success.
     */
    public function deleteWithRelations(int $taskId): bool {
        $db = $this->db;
        $db->transStart();

        // clear links first (FKs commonly RESTRICT)
        $db->table('tasks_users')->where('id_task', $taskId)->delete();
        // delete the task
        $this->delete($taskId);

        $db->transComplete();
        return $db->transStatus();
    }
}
