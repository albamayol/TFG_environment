<?php

namespace App\Controllers;

use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\TasksUsersModel;
use App\Models\ProjectModel;

use CodeIgniter\I18n\Time;
use CodeIgniter\Database\Exceptions\DatabaseException;

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

        //tracker of today's tasks
        $total = count($tasks);
        $completed = 0;
        foreach ($tasks as $task) {
            if ($task['state'] === 'Done') {
                $completed++;
            }
        }

        $canCreateTask = $this->canCreateTasks($userId);

        //Get user's timezone from session or default to UTC
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
        $currentUser = $this->userModel->find((int) session('id_user'));
        $data['currentUserEmail'] = $currentUser['email'] ?? null;

        return view('/Tasks/MyDay', $data);
    }

    public function myTasks() {
        helper('datetime');

        $userId = session()->get('id_user');

        $canCreateTask = $this->canCreateTasks($userId);

        // UTC day boundaries
        [$startToday, $endToday] = getUtcDayBounds();
        $nowUtc    = utcNow();
        $weekEnd   = $startToday->addDays(7);   //end of week
        $todayStart = $nowUtc > $startToday ? $nowUtc : $startToday;

        $tasksToday  = $this->taskModel->getTasksForUserInRange($userId, $todayStart->toDateTimeString(), $endToday->toDateTimeString());
        $tasksWeek   = $this->taskModel->getTasksForUserInRange($userId, $endToday->toDateTimeString(), $weekEnd->toDateTimeString());
        $tasksLaterRanged = $this->taskModel->getTasksForUserFromDate($userId, $weekEnd->toDateTimeString());
        $tasksLaterNoDate = $this->taskModel->getTasksForUserWithNoLimitDate($userId);

        // Merge "later" task arrays and ensure uniqueness by id_task
        $tasksLater = $this->uniqueByIdTask(array_merge($tasksLaterRanged, $tasksLaterNoDate));

        $tasksExpired = $this->taskModel->getOverdueTasksForUser($userId);

        $data['tasksToday']   = $this->decorateForDisplay($tasksToday);
        $data['tasksWeek']    = $this->decorateForDisplay($tasksWeek);
        $data['tasksLater']   = $this->decorateForDisplay($tasksLater);
        $data['tasksExpired'] = $this->decorateForDisplay($tasksExpired);
        $data['canCreateTask'] = $canCreateTask;
        $currentUser = $this->userModel->find((int) session('id_user'));
        $data['currentUserEmail'] = $currentUser['email'] ?? null;

        //merge all tasks arrays (all columns) into a single 'task' one to load it into the view
        $data['tasks'] = $this->decorateForDisplay(array_merge($tasksToday, $tasksWeek, $tasksLater, $tasksExpired));

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
    public function create() {
        $userId = session()->get('id_user');

        //Check if the user's role allows task creation
        if (! $this->canCreateTasks($userId)) {
            return redirect()->to('/Tasks/MyDay')->with('error', 'Unauthorized');
        }

        //Get projects this user participates in
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
      
        $userId = session()->get('id_user');

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

        $limitDateLocal = $this->request->getPost('limit_date');
        $limitDateUtc   = null;

        if ($limitDateLocal !== null && $limitDateLocal !== '') {
            $tmpDate = str_replace('T', ' ', $limitDateLocal); //Accept both 'YYYY-MM-DDTHH:mm' and 'YYYY-MM-DDTHH:mm:ss'
            if (strlen($tmpDate) === 16) { //no seconds
                $tmpDate .= ':00';
            }
            //Convert from user's tz (session) to UTC string
            $limitDateUtc = fromUserTimezone($tmpDate); //returns 'Y-m-d H:i:s' in UTC
        }

        //Obtain POST fields
        $name        = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $priority    = $this->request->getPost('priority') ?? 'Medium';
        $duration    = $this->request->getPost('duration') ?: null;
        $projectId   = $this->request->getPost('id_project') ?: null;
        $assignedId  = $this->request->getPost('assigned_user');
        $isIndividual = $this->request->getPost('individual') ? true : false;
        $simulated   = $this->request->getPost('simulated') ? 1 : 0;

        //Get origin_of_task and person_of_interest
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

        //Build task data
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

        //Save task (insert)
        $taskId = $this->taskModel->insert($taskData);

        //If individual is checked OR no user selected, assign to self.
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
    public function usersForProject($projectId = null) {
        $userId = session()->get('id_user');

        //Use ID 0 or null to indicate "no project selected"
        $projectId = empty($projectId) ? null : (int) $projectId;

        if ($projectId) {
            $users = $this->tasksUsersModel->getUsersByProjectId($projectId);
        } else {
            //No project selected: return all possible users for current user role
            $users = $this->tasksUsersModel->getUsersForUserRole($userId);
        }

        return $this->response->setJSON($users);
    }

    /**
     * Determine if the current user can create tasks.
     */
    private function canCreateTasks(int $userId): bool {
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

    /**
     * Helper: unique by id_task while preserving order.
     */
    private function uniqueByIdTask(array $tasks): array {
        $seen = [];
        $out  = [];
        foreach ($tasks as $t) {
            $id = $t['id_task'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $out[] = $t;
            }
        }
        return $out;
    }

    public function updateState($id = null) {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $payload = $this->request->getJSON(true) ?? [];
        $state   = $payload['state'] ?? null;

        $allowed = ['To Do','In Progress','Done'];
        if (!in_array($state, $allowed, true)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid state']);
        }

        // TODO: authorize the user owns/can edit this task
        $ok = $this->taskModel->update($id, ['state' => $state]);

        if (!$ok) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'DB update failed']);
        }

        //also return a fresh CSRF token if you rotate per-request
        return $this->response->setJSON([
            'ok'   => true,
            'id'   => (int)$id,
            'state'=> $state,
            'csrf' => ['name' => csrf_token(), 'hash' => csrf_hash()]
        ]);
    }

    public function delete($id = null) {
        if (!$id || !$this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $userId = (int)(session('id_user') ?? 0);
        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Not authenticated']);
        }

        // current user (we have id_user in session)
        $user = model(UserModel::class)->find($userId); // OK to use find(id)
        if (!$user || empty($user['email'])) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'User email not found']);
        }
        $meEmail = (string)$user['email'];

        $tasks = model(TaskModel::class);

        $isAdmin = (session('role_name') === 'Profile_Admin');
        $isOwner = $tasks->isOwnerByEmail((int)$id, $meEmail);

        if (!$isOwner && !$isAdmin) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Forbidden',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $ok = $tasks->deleteWithRelations((int)$id);

        if (!$ok) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Delete failed',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        // success (return new CSRF for your AJAX flow)
        return $this->response->setJSON([
            'ok'   => true,
            'csrf' => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }
}
