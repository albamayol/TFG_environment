<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data['roles'] = $this->roleModel->findAll();
        return view('iam/roles/index', $data);
    }

    public function show($id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('iam/roles/show', ['role' => $role]);
    }

    public function create()
    {
        return view('iam/roles/create');
    }

    public function store()
    {
        if (! $this->validate([
            'name' => 'required|min_length[3]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'skills'      => $this->request->getPost('skills')
        ]);

        return redirect()->to('/IAM/Roles')->with('message', 'Rol creado');
    }
}