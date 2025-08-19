<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->get('/', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->get('/logout', 'Auth::logout');
$routes->get('/signup', 'Auth::signup');
$routes->post('/signup', 'Auth::register');

$routes->group('/Tasks', ['filter' => 'auth'], function($routes) {
    $routes->get('MyDay', 'Tasks::myDay');
    $routes->get('MyTasks', 'Tasks::myTasks');
    $routes->get('createTask', 'Tasks::create');
    $routes->post('store', 'Tasks::store');
    $routes->get('(:num)', 'Tasks::show/$1');
});

$routes->group('/Projects', ['filter' => 'auth'], function($routes) {
    $routes->get('/MyProjects', 'Projects::index');
    $routes->get('/createProject', 'Projects::create');
    $routes->post('/store', 'Projects::store');
    $routes->get('/MyProjects/(:num)', 'Projects::show/$1');
});

$routes->group('/IAM', ['filter' => 'auth,role:Profile_Admin,Manager,Head_Of_Team'], function($routes) {
    $routes->get('/Users', 'IAM/Users::index');
    $routes->get('/Users/createUser', 'IAM/Users::create');
    $routes->post('/Users/store', 'IAM/Users::store');

    $routes->get('/Roles', 'IAM/Roles::index');
    $routes->get('/Roles/createRole', 'IAM/Roles::create');
    $routes->post('/Roles/store', 'IAM/Roles::store');

    $routes->get('/Actions', 'IAM/Actions::index');
    $routes->get('/Actions/createAction', 'IAM/Actions::create');
    $routes->post('/Actions/store', 'IAM/Actions::store');
});

