<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function index() {
        $userId = session()->get('id_user');
        $user   = $this->userModel->find($userId);
        return view('profile/index', ['user' => $user]);
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