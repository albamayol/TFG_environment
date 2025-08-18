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
    public function register() { //TODO: IF USER REGISTERS IT WILL BE BY DEFAULT GET MANAGER ROLE. Otherwise, it will be the profile admin that will be creating the profile for the users within their organization
        helper(['form']);
        $userModel = new UserModel();

        // 1) normaliza ANTES de armar $data
        $rawTel = $this->request->getPost('telephone');
        $tel = $this->normalize_phone_to_e164($rawTel, 'ES');
        if ($tel === null) {
            return redirect()->back()->withInput()
                ->with('errors', ['telephone' => 'Teléfono no válido. Usa formato internacional (+...).']);
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
            // success: redirect to login with flash message
            return redirect()->to('/')->with('message', 'Usuario registrado correctamente');
        } else { // failure: redirect back with the errors array from the model
            return redirect()->back()->withInput()->with('errors', $userModel->errors()); 
        }
    }

    public function success()
    {
        return 'User registered successfully!';
    }

    public function attempt() {
        echo "Attempting login..."; // Debugging line, can be removed later
        // Detect AJAX/JSON
        $isAjax = $this->request->isAJAX()
                  || stripos($this->request->getHeaderLine('Content-Type'), 'application/json') !== false;

        // Accept either form-encoded (non-AJAX) or JSON (AJAX)
        $email = (string) ($this->request->getPost('email') ?? ($this->request->getJSON(true)['email'] ?? ''));
        $pass  = (string) ($this->request->getPost('password') ?? ($this->request->getJSON(true)['password'] ?? ''));
        echo "Email: $email, Password: $pass"; // Debugging line, can be removed later
        // First Basic validation
        if ($email === '' || $pass === '') {
            return $isAjax
                ? $this->jsonLoginError('Email y contraseña son obligatorios')
                : redirect()->back()->with('error', 'Email y contraseña son obligatorios');
        }
        echo "Validación básica OK"; // Debugging line, can be removed later

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($pass, $user['password'])) {
            return $isAjax
                ? $this->jsonLoginError('Credenciales inválidas')
                : redirect()->back()->with('error', 'Credenciales inválidas');
        }

        // Check if the user is already logged in -->already done in filter 'before'
        /*if (session()->get('id_user')) {
            return redirect()->to('/Tasks/MyDay');
        }*/

        // (Optional) rehash if needed
        /*if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            model(UserModel::class)->update($user['id_user'], [
                'password' => password_hash($pass, PASSWORD_DEFAULT)
            ]);
        }*/

        // Get the user's role
        $role = $userModel->getRole($user['id_user']);
        echo "Role: $role"; // Debugging line, can be removed later
        $user['role_name'] = $role;

        // Store minimal session (keep your existing key names to avoid breaking nav)
        $session = session();
        session()->set([
            'id_user'   => $user['id_user'],
            'role_name' => $user['role_name'],
            'logged_in' => true
        ]);

        $session->regenerate(true);

        if ($isAjax) {
            // Optionally return a fresh CSRF for next requests
            $csrf = [
                'name' => csrf_token(),
                'hash' => csrf_hash(),
            ];
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => '/Tasks/MyDay',
                'csrf'     => $csrf,
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
