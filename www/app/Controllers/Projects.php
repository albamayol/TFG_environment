<?php

namespace App\Controllers;

use App\Models\ProjectModel;

class Projects extends BaseController {
    protected $projectModel;

    public function __construct() {
        $this->projectModel = new ProjectModel();
    }

    public function index() {
        $data['projects'] = $this->projectModel->findAll();
        return view('projects/myProjects', $data);
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