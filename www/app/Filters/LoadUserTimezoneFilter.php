<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LoadUserTimezoneFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {
        if (! session()->has('user_timezone')) {
            $cookieTz = $_COOKIE['user_timezone'] ?? null;
            if ($cookieTz) {
                try { new \DateTimeZone($cookieTz); session()->set('user_timezone', $cookieTz); }
                catch (\Throwable $e) { /* ignore invalid */ }
            }
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
