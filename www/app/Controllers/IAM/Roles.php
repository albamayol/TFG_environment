<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\ActionModel;
use App\Models\RoleActionsModel;

class Roles extends BaseController {
    protected $roleModel;
    protected $actionModel;
    protected $roleActionsModel;

    public function __construct() {
        $this->roleModel = new RoleModel();
        $this->actionModel = new ActionModel();
        $this->roleActionsModel = new RoleActionsModel();
    }

    public function showRoles() {
        $roles = $this->roleModel->findAll();
        return view('IAM/Roles', ['roles' => $roles]);
    }

    public function show($id) {
        $role = $this->roleModel->find($id);
        if (! $role) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('IAM/showRoles', ['role' => $role]);
    }

    public function create() {
        return view('IAM/createRole', [
            'actions' => $this->actionModel->findAll()
        ]);
    }

    public function store() {

        $rules = [
            'role-name'  => 'required|min_length[3]',
            'role-description' => 'required|min_length[3]',
            'role-skills' => 'required|min_length[3]',
            'role-actions' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->insert([
            'name'        => $this->request->getPost('role-name'),
            'description' => $this->request->getPost('role-description'),
            'skills'      => $this->request->getPost('role-skills'),
            'simulated'  => $this->request->getPost('simulated') ? 1 : 0,
        ]);

        $roleId = (int) $this->roleModel->getInsertID();
        $idActions = $this->request->getPost('role-actions'); //array of actions id's

        if (is_array($idActions)) { //if multiple actions selected
            foreach ($idActions as $actionId) {
                $this->roleActionsModel->addActionToRole((int) $actionId, $roleId);
            }
        } else { //If only one action is selected, it won't be an array
            $this->roleActionsModel->addActionToRole((int) $idActions, $roleId);
        }

        return redirect()->to('/IAM/Roles')->with('message', 'Role created successfully');
    }
}