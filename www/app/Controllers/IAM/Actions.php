<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\ActionModel;

class Actions extends BaseController {
    protected $actionModel;

    public function __construct() {
        $this->actionModel = new ActionModel();
    }

    public function showActions() {
        $actions = $this->actionModel->findAll();
        return view('IAM/Actions', ['actions' => $actions]);
    }

    public function create() {
        return view('IAM/createAction');
    }

    public function store() {
        $rules = [
            'action-name'  => 'required|min_length[3]',
            'action-description' => 'required|min_length[3]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->actionModel->insert([
            'name'        => $this->request->getPost('action-name'),
            'description' => $this->request->getPost('action-description'),
            'simulated'   => $this->request->getPost('simulated') ? 1 : 0,
        ]);

        return redirect()->to('/IAM/Actions')->with('message', 'Action created successfully');
    }
}