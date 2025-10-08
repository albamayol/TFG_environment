<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        //$requiredRole = $arguments[0] ?? '';

        $session = session();

        //Must be logged in first
        if (!$session->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión primero.'); // Redirigir a la página de inicio de sesión si no hay sesión
        }
        
        //Read the current user's role from the session
        $userRole = $session->get('role_name') ?? '';

        //The $arguments come from routes (role:admin,manager,head_of_team)
        $allowed = is_array($arguments) ? $arguments : [];

        //If no arguments provided, "role" filter just means "must be logged in"
        if ($allowed === []) {
            return; //allowed
        }
        //Allow if user's role is in the allowed list
        if (in_array($userRole, $allowed, true)) {
            return; //allow
        }

        //Deny
        $isAjax = $request->isAJAX() ||
                  stripos($request->getHeaderLine('Accept'), 'application/json') !== false;

        if ($isAjax) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Acceso denegado']);
        }

        //HTML 403 page
        return service('response')
            ->setStatusCode(403)
            ->setBody(view('errors/html/error_403'));
            
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        //
    }
}
