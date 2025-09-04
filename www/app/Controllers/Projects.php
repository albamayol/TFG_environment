<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\UserProjectRoleModel;
use App\Models\TaskModel;

class Projects extends BaseController {
    protected $projectModel;
    protected $userProjectRoleModel;
    protected $taskModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
        $this->userProjectRoleModel = new UserProjectRoleModel();
        $this->taskModel = new TaskModel();
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
            'canCreateProject' => in_array(session('role_name'), ['Profile_Admin', 'Manager'])
        ];
        return view('projects/myProjects', $projects);
    }

    public function show($id) {
        $project = $this->projectModel->find($id);
        if (! $project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('projects/show', ['project' => $project]);
    }

    public function create() {
        return view('projects/create');
    }

    public function save() {
        if (! $this->validate([
            'name' => 'required|min_length[3]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->projectModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'state'       => $this->request->getPost('state'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date')
        ]);

        return redirect()->to('projects/myProjects')->with('message', 'Proyecto creado');
    }
}