<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\UserProjectRoleModel;
use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\RoleModel;

class Projects extends BaseController {
    protected $projectModel;
    protected $userModel;
    protected $roleModel;
    protected $userProjectRoleModel;
    protected $taskModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
        $this->userProjectRoleModel = new UserProjectRoleModel();
        $this->taskModel = new TaskModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function showMyProjects() {
        $userId = session('id_user');
        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'error' => 'Not authenticated',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $projects = [
            'projects' => $this->projectModel->getProjectsForUser($userId),
            'canCreateProject' => in_array(session('role_name'), ['Profile_Admin', 'Manager']),
            'canChangeState' => in_array(session('role_name'), ['Manager', 'Head_Of_Team'])
        ];
        return view('projects/myProjects', $projects);
    }

    public function create() {
        //HEAD OF TEAMS in the ddbb
        $hots = $this->userModel->getAllHeadsOfTeam();

        //Workers in the ddbb
        $workers = $this->userModel->getAllWorkers();

        //Roles in the ddbb
        $roles = $this->roleModel->getRolesForWorkers();

        $data =[
            'hots' => $hots,
            'workers' => $workers,
            'roles' => $roles
        ];
        return view('Projects/createProject', $data);
    }

    public function save() {    //for the POST when creating a project
        helper('datetime');
      
        $headOfTeamId = $this->request->getPost('head-of-team');
        $selectedWorkers = (array) $this->request->getPost('workers'); // ['12','18',...]
        $rolesMap        = (array) $this->request->getPost('roles');   // ['12'=>'3','18'=>'2',...]

        // Fetch valid role IDs to guard 'in_list'
        $roleRows      = $this->roleModel->select('id_role')->findAll();
        $validRoleIds  = array_map('strval', array_column($roleRows, 'id_role'));
        $validRoleList = implode(',', $validRoleIds);

        // Validate input
        $rules = [
            'name'  => 'required|min_length[2]',
            'description' => 'required|min_length[3]',
            'start_date' => 'required|valid_date[Y-m-d\TH:i]',
            'end_date' => 'required|valid_date[Y-m-d\TH:i]|after_date[start_date]',
            'head-of-team' => 'required|is_natural_no_zero',
            'workers' => 'required',
            //'roles' => 'required',
            
        ];
        $messages = [
            'name' => [
                'required'   => 'Project name is required.',
                'min_length' => 'Project name must be at least 2 characters long.',
            ],
            'description' => [
                'required'   => 'Project description is required.',
                'min_length' => 'Project description must be at least 3 characters long.',
            ],
            'start_date' => [
                'required'   => 'Project start date is required.',
                'valid_date' => 'Project start date must be a valid date.',
            ],
            'end_date' => [
                'required'   => 'Project end date is required.',
                'valid_date' => 'Project end date must be a valid date.',
                'after_date' => 'The Ending date must be after the Starting date.',
            ],
            'head-of-team' => [
                'required' => 'Please select a Head of Team.',
            ],
            'workers' => [
                'required' => 'Please select at least one worker.',
            ],
        ];

        // Add a per-worker rule: roles.<workerId> is required + must be a valid role
        foreach ($selectedWorkers as $wid) {
            $widStr = (string) $wid;
            $rules["roles.$widStr"] = "required|in_list[$validRoleList]";
            $messages["roles.$widStr"] = [
                'required' => "Please select a role for worker #$widStr.",
                'in_list'  => "Invalid role selected for worker #$widStr.",
            ];
        }
    

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->projectModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
            'state' => 'To Begin'
        ]);

        // Validate each selected worker has a role:
        foreach ($selectedWorkers as $uid) {
            $roleId = $rolesMap[$uid] ?? null;
            $this->userProjectRoleModel->assignRole($uid, $roleId, $this->projectModel->getInsertID());
        }
        //save HoT
        $this->userProjectRoleModel->assignRole($headOfTeamId, 3, $this->projectModel->getInsertID()); //HoT assigned will always have Head of Project Role in a Project (ROLE ID RESERVED = 3)
        //save Creator of the Project
        $this->userProjectRoleModel->assignRole(session('id_user'), 4, $this->projectModel->getInsertID()); //Creator will always have Project Creator Role in a Project (ROLE ID RESERVED = 4)

        return redirect()->to('Projects/MyProjects')->with('message', 'Project successfully created');
    }

    public function updateState($id = null) {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405);
        }

        $payload = $this->request->getJSON(true) ?? [];
        $state   = $payload['state'] ?? null;

        $allowed = ['To Begin','Active','On Pause', 'Finished'];
        if (!in_array($state, $allowed, true)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid state']);
        }

        $ok = $this->projectModel->update($id, ['state' => $state]);

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
}