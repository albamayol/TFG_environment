<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index() {
        $data['users'] = $this->userModel->findAll();
        return view('iam/users/index', $data);
    }

    public function create() {
        return view('iam/users/create');
    }

    public function store() {
        if (! $this->validate([
            'name'     => 'required',
            'surnames' => 'required',
            'email'    => 'required|valid_email|is_unique[Usuario.email]',
            'password' => 'required|min_length[8]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save([
            'name'     => $this->request->getPost('name'),
            'surnames' => $this->request->getPost('surnames'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/IAM/Users')->with('message', 'Usuario creado');
    }
}