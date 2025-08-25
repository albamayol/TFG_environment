<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\TasksUsersModel;
use App\Models\ProjectModel;

use CodeIgniter\I18n\Time;

class Tasks extends BaseController {
    protected $taskModel;
    protected $tasksUsersModel;
    protected $projectModel;
    protected $userModel;

    public function __construct() {
        $this->taskModel = new TaskModel();
        $this->tasksUsersModel = new TasksUsersModel();
        $this->projectModel = new ProjectModel();
        $this->userModel = new UserModel();
    }

    public function myDay() {
        helper('datetime');

        $userId = session()->get('id_user');
        $tasks = $this->taskModel->getTodayTasksForUser($userId);
        $tasks = $this->decorateForDisplay($tasks); 
        $userName = $this->userModel->find($userId)['name'] ?? '';

        //tracker de las tasks de hoy
        $total = count($tasks);
        $completed = 0;
        foreach ($tasks as $task) {
            if ($task['state'] === 'Done') {
                $completed++;
            }
        }

        // compute permission flag
        $canCreateTask = $this->canCreateTasks($userId);

        // Get user's timezone from session or default to UTC
        $tz = session()->get('user_timezone') ?? 'UTC';
        $today = Time::now('UTC')->setTimezone($tz)->format('d-m-Y');
        $dayOfWeek = Time::now('UTC')->setTimezone($tz)->format('l jS \of F Y'); 
        
        $data = [
            'tasks'   => $tasks,
            'userName' => $userName,
            'today'   => $today,
            'dayOfWeek' => $dayOfWeek,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'canCreateTask' => $canCreateTask,
        ];

        return view('/Tasks/MyDay', $data);
    }

    public function myTasks() {
        helper('datetime');

        $userId = session()->get('id_user');

        $canCreateTask = $this->canCreateTasks($userId);

        // UTC day boundaries
        [$startToday, $endToday] = getUtcDayBounds();
        $endWeek   = $startToday->addDays(7);
        $startLater = $startToday->addDays(8);
        $endLater  = $startToday->addDays(30);
        
        $data['tasks']       = $this->decorateForDisplay($this->taskModel->getTasksForUser($userId));
        $data['tasksToday']  = $this->decorateForDisplay($this->taskModel->getTasksForUserInRange(
            $userId, $startToday->toDateTimeString(), $endToday->toDateTimeString()));
        $data['tasksWeek']   = $this->decorateForDisplay($this->taskModel->getTasksForUserInRange(
            $userId, $startToday->toDateTimeString(), $endWeek->toDateTimeString()));
        $data['tasksLater']  = $this->decorateForDisplay($this->taskModel->getTasksForUserInRange(
            $userId, $startLater->toDateTimeString(), $endLater->toDateTimeString()));
        $data['tasksExpired'] = $this->decorateForDisplay($this->taskModel->getOverdueTasksForUser($userId));

        $data['canCreateTask'] = $canCreateTask;
        
        return view('/Tasks/MyTasks', $data);
    }

    public function show($id) {
        $task = $this->taskModel->find($id);
        if (!$task) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('/Tasks/show', ['task' => $task]);
    }

     /**
     * Show the "Create Task" form to privileged users.
     */
    public function create()
    {
        $userId = session()->get('id_user');

        // Check if the user has a privileged role
        if (! $this->canCreateTasks($userId)) {
            return redirect()->to('/Tasks/MyDay')->with('error', 'Unauthorized');
        }

        // Fetch projects accessible to this user
        $projects = $this->projectModel->getProjectsForUser($userId);

        // Fetch the initial list of users:
        // If the user is a manager/profile admin, get all users.
        // If the user is a head of team, get participants of their projects.
        $users = $this->tasksUsersModel->getUsersForUserRole($userId);

        return view('/Tasks/createTask', [
            'projects'    => $projects,
            'users'       => $users,
            'selfUserId'  => $userId,
        ]);
    }

    /**
     * Save the task to the database and assign users.
     */
    public function save() {
        helper('datetime');
        //get current user id
        $userId = session()->get('id_user');

        // Ensure only privileged users can create tasks
        if (! $this->canCreateTasks($userId)) {
            return redirect()->to('/Tasks/MyDay')->with('error', 'Unauthorized');
        }

        // Validate input
        $rules = [
            'name'  => 'required|min_length[3]',
            'description' => 'required|min_length[3]',
            'priority' => 'required',
            'limit_date' => 'permit_empty',
            'duration' => 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]',
            'priority' => 'in_list[Low,Medium,High,Urgent]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        //conversion of date to global UTC for further reconversion to local timezone from the user
        $limitDateLocal = $this->request->getPost('limit_date'); // e.g. '2025-08-25T09:30' or ''
        $limitDateUtc   = null;

        if ($limitDateLocal !== null && $limitDateLocal !== '') {
            // Accept both 'YYYY-MM-DDTHH:mm' and 'YYYY-MM-DDTHH:mm:ss'
            $tmpDate = str_replace('T', ' ', $limitDateLocal);
            if (strlen($tmpDate) === 16) { // no seconds
                $tmpDate .= ':00';
            }
            // Convert from user's tz (session) to UTC string
            $limitDateUtc = fromUserTimezone($tmpDate); // returns 'Y-m-d H:i:s' in UTC
        }

        // Capture POST fields
        $name        = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $priority    = $this->request->getPost('priority') ?? 'Medium';
        //$limitDateRaw   = $this->request->getPost('limit_date') ?: null;
        //$limitDate   = $limitDateUtc; // Store UTC date or null
        $duration    = $this->request->getPost('duration') ?: null;
        $projectId   = $this->request->getPost('id_project') ?: null;
        $assignedId  = $this->request->getPost('assigned_user');
        $isIndividual = $this->request->getPost('individual') ? true : false;
        $simulated   = $this->request->getPost('simulated') ? 1 : 0;

        // Determine origin_of_task and person_of_interest
        $origin = 'Individual';
        if ($projectId) {
            $project = $this->projectModel->find($projectId);
            if ($project) {
                $origin = $project['name'];
            }
        }

        //get user email from session
        $user = $this->userModel->find($userId);
        $userEmail = $user ? $user['email'] : '';

        // Build task data
        $taskData = [
            'id_project'      => $projectId,
            'name'            => $name,
            'description'     => $description,
            'state'           => 'To Do',
            'priority'        => $priority,
            'limit_date'      => $limitDateUtc,
            'duration'        => $duration,
            'origin_of_task'  => $origin,
            'person_of_interest' => $userEmail, // store current user
            'simulated'       => $simulated,
        ];

        // Insert task
        $taskId = $this->taskModel->insert($taskData);

        // Determine assignment:
        // If individual is checked OR no user selected, assign to self.
        if ($isIndividual || empty($assignedId)) {
            $this->tasksUsersModel->assignUserToTask($userId, $taskId, 'Owner');
        } else {
            $this->tasksUsersModel->assignUserToTask($assignedId, $taskId, 'Assigned');
        }

        return redirect()->to('/Tasks/MyTasks')->with('message', 'Task created successfully');
    }

    /**
     * AJAX endpoint to get users for a given project.
     * Returns JSON array with id_user, name, surnames.
     */
    public function usersForProject($projectId = null)
    {
        $userId = session()->get('id_user');

        // Use ID 0 or null to indicate "no project selected"
        $projectId = empty($projectId) ? null : (int) $projectId;

        if ($projectId) {
            $users = $this->tasksUsersModel->getUsersByProjectId($projectId);
        } else {
            // No project selected: return all possible users for current user role
            $users = $this->tasksUsersModel->getUsersForUserRole($userId);
        }

        return $this->response->setJSON($users);
    }

    /**
     * Determine if the current user can create tasks.
     */
    private function canCreateTasks(int $userId): bool
    {
        $db = \Config\Database::connect();
        return
            $db->table('Profile_Admin')->where('id_prof_admin', $userId)->countAllResults() > 0 ||
            $db->table('Manager')->where('id_manager', $userId)->countAllResults() > 0 ||
            $db->table('Head_of_Team')->where('id_head_of_team', $userId)->countAllResults() > 0;
    }

    private function decorateForDisplay(array $tasks, string $fmt = 'Y-m-d H:i'): array {
        helper('datetime'); // ensures toUserTimezone() is available
        return array_map(function ($t) use ($fmt) {
            $utc = $t['limit_date'] ?? null;
            $t['limit_date_display'] = $utc ? toUserTimezone($utc, $fmt) : null;
            return $t;
        }, $tasks);
    }
}
