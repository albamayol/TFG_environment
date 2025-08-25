<?php

namespace App\Controllers;

use App\Models\HeadOfTeamModel;
use App\Models\ManagerModel;
use App\Models\ProfileAdminModel;
use App\Models\UserModel;
use App\Models\WorkerModel;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class Auth extends BaseController {
    public function login() {
        if (session()->get('id_user')) {
            return redirect()->to('/Tasks/MyDay');
        }
        return view('auth/login', ['Title' => 'Pasmo']);
    }

    public function signup() {
        helper(['form']);
        return view('auth/signup');
    }

    //REGISTER FUNCTIONALITY WILL ALLOW TO HAVE AN ACCOUNT FOR THE APP TO BE USED FOR PERSONAL MOTIVES.
    public function register() {
        helper(['form']);
        $userModel = new UserModel();
        $managerModel = new ManagerModel(); //Default role for new users

        //normalize BEFORE setting up $data
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
            'simulated' => 0, // Default value for simulated
        ];
        
        // insert will run the model’s validationRules and validationMessages
        if ($userModel->insert($data)) {
            
            // Grab the auto‑generated id_user
            $newUserId = $userModel->insertID();
            $managerModel->insert(['id_manager' => $newUserId]);

            // success: redirect to login with flash message
            return redirect()->to('/')->with('message', 'User registered successfully');
        } else { // failure: redirect back with the errors array from the model
            return redirect()->back()->withInput()->with('errors', $userModel->errors()); 
        }
    }

    public function attempt() {
        // Detect AJAX/JSON
        $isAjax = $this->request->isAJAX()
                  || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;

        // Accept either form-encoded (non-AJAX) or JSON (AJAX)
        $email = trim((string) ($this->request->getPost('email') ?? ($this->request->getJSON(true)['email'] ?? '')));
        $pass  = (string) ($this->request->getPost('password') ?? ($this->request->getJSON(true)['password'] ?? ''));

        // First Basic validation
        if ($email === '' || $pass === '') {
            return $isAjax
                ? $this->jsonLoginError('Email and password are required')
                : redirect()->back()->with('error', 'Email and password are required');
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || empty($user['password'])) {
            return $isAjax
            ? $this->jsonLoginError('Invalid Credentials')
            : redirect()->back()->with('error', 'Invalid Credentials');
        }

        if (!$user || !password_verify($pass, $user['password'])) {
            return $isAjax
                ? $this->jsonLoginError('Invalid Credentials')
                : redirect()->back()->with('error', 'Invalid Credentials');
        }

        // Get the user's role
        $role = $userModel->getRole($user['id_user']);
        $user['role_name'] = $role;

        $session = session();
        session()->set([
            'id_user'   => $user['id_user'],
            'role_name' => $user['role_name'],
            'logged_in' => true
        ]);

        $session->regenerate(true);

        if ($isAjax) {
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => '/Tasks/MyDay',
                'csrf'     => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        return redirect()->to('/Tasks/MyDay');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('msg', 'Logged out.');
    }

    private function jsonLoginError(string $message)
    {
        $csrf = [
            'name' => csrf_token(),
            'hash' => csrf_hash(),
        ];
        return $this->response
            ->setStatusCode(401)
            ->setJSON(['success' => false, 'error' => $message, 'csrf' => $csrf]);
    }

    /**
     * Normaliza un teléfono “amigable” a E.164 (+XXXXXXXXXXX).
     * Devuelve null si no es válido.
     */
    function normalize_phone_to_e164(string $input, string $defaultRegion = 'ES'): ?string
    {
        $util = PhoneNumberUtil::getInstance();
        try {
            $proto = $util->parse($input, $defaultRegion); // acepta +, espacios, guiones, paréntesis...
            if (!$util->isValidNumber($proto)) {
                return null;
            }
            return $util->format($proto, PhoneNumberFormat::E164); // p.ej. +34600123456
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
