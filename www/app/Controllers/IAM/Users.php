<?php

namespace App\Controllers\IAM;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ProfileAdminModel;
use App\Models\ManagerModel;
use App\Models\HeadOfTeamModel;
use App\Models\WorkerModel;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class Users extends BaseController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showUsers() {
        $users = $this->userModel->getUsersForIAM();
        $canDeleteUsers = (session('role_name') === 'Profile_Admin');
        $canCreateUsers = (session('role_name') === 'Profile_Admin');
        return view('IAM/Users', ['users' => $users, 'canDeleteUsers' => $canDeleteUsers, 'canCreateUsers' => $canCreateUsers]);
    }

    public function create() {
        return view('IAM/createUser');
    }

    //store FUNCTIONALITY ALLOWS Profile Admins TO CREATE ACCOUNTS FOR OTHER USERS.
    public function store() {
        helper(['form']);

        $rawTel = $this->request->getPost('telephone');
        $tel = $this->normalize_phone_to_e164($rawTel, 'ES');
        if ($tel === null) {
            return redirect()->back()->withInput()
                ->with('errors', ['telephone' => 'Invalid phone number. Use international format (+...).']);
        }
        
        $data = [
            'name'     => $this->request->getPost('name'),
            'surnames' => $this->request->getPost('surnames'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'repeated_password' => $this->request->getPost('repeated_password'),
            'birthdate' => $this->request->getPost('birthdate'),
            'address'  => $this->request->getPost('address'),
            'dni_nie'  => $this->request->getPost('dni_nie'),
            'telephone' => $tel,
            'soft_skills' => $this->request->getPost('soft_skills'),
            'technical_skills' => $this->request->getPost('technical_skills'),
            'simulated' => $this->request->getPost('simulated') ? 1 : 0, // Default value for simulated
            'Role' => $this->request->getPost('role') ?? 'Manager', // Default role is Manager
        ];

        $validRoles = ['Profile_Admin', 'Manager', 'Head_Of_Team', 'Worker'];
        $role = $this->request->getPost('role');

        if (!in_array($role, $validRoles, true)) {
            return redirect()->back()->withInput()->with('errors', ['role' => 'Invalid role selected.']);
        }

        //Validate all fields
        if (! $this->userModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }
        
        if ($this->userModel->insert($data)) {
            
            switch($data['Role']) {
                case 'Profile_Admin':
                    $profileAdminModel = new ProfileAdminModel();
                    $profileAdminModel->insert(['id_prof_admin' => $this->userModel->insertID()]);
                    break;
                case 'Manager':
                    $managerModel = new ManagerModel();
                    $managerModel->insert(['id_manager' => $this->userModel->insertID()]);
                    break;
                case 'Head_Of_Team':
                    $headOfTeamModel = new HeadOfTeamModel();
                    $headOfTeamModel->insert(['id_head_of_team' => $this->userModel->insertID()]);
                    break;
                case 'Worker':
                    $workerModel = new WorkerModel();
                    $workerModel->insert(['id_worker' => $this->userModel->insertID()]);
                    break;
                default:
                    //If unknown role provided, delete the created user and return an error
                    $this->userModel->delete($this->userModel->insertID());
                    return redirect()->back()->withInput()
                        ->with('errors', ['role' => 'Invalid role specified.']);
            }
            return redirect()->to('IAM/Users')->with('message', 'User registered successfully');
        } else { 
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors()); 
        }
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

    /**
     * Normaliza un teléfono a E.164 (+XXXXXXXXXXX).
     * Devuelve null si no válido.
     */
    private function normalize_phone_to_e164(string $input, string $defaultRegion = 'ES'): ?string {
        $util = PhoneNumberUtil::getInstance();
        try {
            $proto = $util->parse($input, $defaultRegion); // acepta +, espacios, guiones, paréntesis...
            if (!$util->isValidNumber($proto)) {
                return null;
            }
            return $util->format($proto, PhoneNumberFormat::E164); //ej: +34600123456
        } catch (NumberParseException $e) {
            return null;
        }
    }
}