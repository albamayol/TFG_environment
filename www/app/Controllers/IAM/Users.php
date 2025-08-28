<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showUsers() {
        $users = $this->userModel->getUsersForIAM();
        $canDeleteUsers = (session('role_name') === 'Profile_Admin');
        return view('IAM/Users', ['users' => $users, 'canDeleteUsers' => $canDeleteUsers]);
    }

    public function create() {
        return view('IAM/create');
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

    public function deleteUser($id = null) {
        if (!$id || !$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'ok' => false,
                'error' => 'Method Not Allowed',
                'csrf' => ['name' => csrf_token(), 'hash' => csrf_hash()]
            ]);
        }
        $userId = (int)(session('id_user') ?? 0);
        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'error' => 'Not authenticated',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }
        $canDeleteUsers = (session('role_name') === 'Profile_Admin');
        if (!$canDeleteUsers) {
            return $this->response->setStatusCode(403)->setJSON([
                'ok'    => false,
                'error' => 'Forbidden Action',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $ok = $this->userModel->deleteUser($id);
        
        if(!$ok) {
            return $this->response->setStatusCode(500)->setJSON([
                'ok'    => false,
                'error' => 'Error deleting user',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }
        return $this->response->setStatusCode(200)->setJSON([
            'ok'      => true,
            'message' => 'User deleted successfully',
            'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }
}