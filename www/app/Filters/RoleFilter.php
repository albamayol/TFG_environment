<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        //$requiredRole = $arguments[0] ?? '';

        $session = session();

        // 1) Must be logged in first.
        //    If not, redirect them to login (and set a flash message).
        if (!$session->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión primero.'); // Redirigir a la página de inicio de sesión si no hay sesión
        }
        
        // 2) Read the current user's single role from the session.
        //    Ex: 'admin' | 'manager' | 'head_of_team' | 'worker'
        $userRole = $session->get('role_name') ?? '';

        // 3) The $arguments come from routes (role:admin,manager,head_of_team).
        //    CI4 passes them as an array already, but we’ll be defensive.
        $allowed = is_array($arguments) ? $arguments : [];

        // 4) If no arguments were provided, "role" filter just means "must be logged in".
        if ($allowed === []) {
            return; // allowed
        }
        // Allow if user's role is in the allowed list
        if (in_array($userRole, $allowed, true)) {
            return; // allow
        }

        // Deny: tailor response for AJAX vs normal
        $isAjax = $request->isAJAX() ||
                  stripos($request->getHeaderLine('Accept'), 'application/json') !== false;

        if ($isAjax) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Acceso denegado']);
        }

        // HTML 403 page (make sure errors/html/error_403 exists)
        return service('response')
            ->setStatusCode(403)
            ->setBody(view('errors/html/error_403'));
            
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        //
    }
}
