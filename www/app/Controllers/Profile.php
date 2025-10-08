<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function show() {  

        $userId = (int)(session('id_user') ?? 0);
        if ($userId <= 0) {
            return $this->response->setStatusCode(401)->setJSON([
                'ok' => false,
                'error' => 'Not authenticated',
                'csrf'  => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }
        $user   = $this->userModel->find($userId);
        $user['role_name'] = session('role_name');
        
        return view('Profile/userProfile', ['user' => $user]);
    }

    public function update() {
        $userId = session()->get('id_user');

        if (! $this->validate([
            'name'     => 'required',
            'surnames' => 'required',
            'email'    => 'required|valid_email'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($userId, [
            'name'     => $this->request->getPost('name'),
            'surnames' => $this->request->getPost('surnames'),
            'email'    => $this->request->getPost('email'),
        ]);

        return redirect()->back()->with('message', 'Perfil actualizado');
    }
}