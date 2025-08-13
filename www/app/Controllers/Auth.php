<?php

namespace App\Controllers;

use App\Models\HeadOfTeamModel;
use App\Models\ManagerModel;
use App\Models\ProfileAdminModel;
use App\Models\UserModel;
use App\Models\WorkerModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('id_user')) {
            return redirect()->to('/Tasks/MyDay');
        }
        return view('auth/login');
    }

    public function signup()
    {
        return view('auth/signup');
    }

    public function register()
    {
        $userModel = new UserModel();

        if (! $this->validate([
            'name'     => 'required',
            'surnames' => 'required',
            'email'    => 'required|valid_email|is_unique[User.email]',
            'password' => 'required|min_length[8]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel->save([
            'name'     => $this->request->getPost('name'),
            'surnames' => $this->request->getPost('surnames'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/')->with('message', 'Usuario registrado');
    }


    public function attempt()
    {
        $userModel = new UserModel();
        $user = $userModel->findByEmail($this->request->getPost('email'));

        if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->with('error', 'Credenciales inválidas.');
        }

        session()->set([
            'id_user'   => $user['id_user'],
            'role_name' => $userModel->getRoleName($user['id_user']) ?? 'Worker', // función auxiliar
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
        //TODO QUE SEA UNA SOLA CONSULTA, QUE NO SE HAGAN 4 NEWS
        // Consulta a la tabla Profile_Admin, Manager, etc. para determinar el rol.
        // Devuelve 'Profile_Admin', 'Manager', 'Head_of_Team' o 'Worker'.
        $profileAdmin = new ProfileAdminModel();
        if ($profileAdmin->isAdmin($userId)) {
            return 'Profile_Admin';
        }

        $manager = new ManagerModel();
        if ($manager->isManager($userId)) {
            return 'Manager';
        }

        $headOfTeam = new HeadOfTeamModel();
        if ($headOfTeam->isHeadOfTeam($userId)) {
            return 'Head_of_Team';
        }

        $worker = new WorkerModel();
        if ($worker->isWorker($userId)) {   
            return 'Worker';
        }
    }
}
