<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('id_user')) {
            return redirect()->to('/Tasks/MyDay');
        }
        return view('auth/login');
    }

    public function attempt()
    {
        $userModel = new UserModel();
        $user = $userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->with('error', 'Credenciales inválidas.');
        }

        session()->set([
            'id_user'   => $user['id_user'],
            'role_name' => $this->getRoleName($user['id_user']), // función auxiliar
            'logged_in' => true
        ]);

        return redirect()->to('/Tasks/MyDay');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    private function getRoleName($userId)
    {
        // Consulta a la tabla Profile_Admin, Manager, etc. para determinar el rol.
        // Devuelve 'Profile_Admin', 'Manager', 'Head_of_Team' o 'Worker'.
    }
}
