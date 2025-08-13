<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\ActionModel;

class Actions extends BaseController
{
    protected $actionModel;

    public function __construct()
    {
        $this->actionModel = new ActionModel();
    }

    public function index()
    {
        $data['actions'] = $this->actionModel->findAll();
        return view('iam/actions/index', $data);
    }

    public function create()
    {
        return view('iam/actions/create');
    }

    public function store()
    {
        if (! $this->validate([
            'name' => 'required|min_length[3]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->actionModel->save([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        return redirect()->to('/IAM/Actions')->with('message', 'Acción creada');
    }
}