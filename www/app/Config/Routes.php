<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->post('timezone/set-timezone', 'Timezone::setTimezone');

$routes->get('/', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->get('/logout', 'Auth::logout');
$routes->get('/signup', 'Auth::signup');
$routes->post('/signup', 'Auth::register');

$routes->group('/Tasks', ['filter' => 'auth'], function($routes) {
    $routes->get('MyDay', 'Tasks::myDay');      //any logged-in user
    $routes->get('MyTasks', 'Tasks::myTasks');  // any logged-in user
    $routes->get('createTask', 'Tasks::create', ['filter' => 'role:Profile_Admin,Manager,Head_Of_Team']);
    $routes->post('store', 'Tasks::save', ['filter' => 'role:Profile_Admin,Manager,Head_Of_Team']);
    $routes->get('(:num)', 'Tasks::show/$1');   // any logged-in user (ownership check in controller)
});

$routes->group('/Projects', ['filter' => 'auth'], function($routes) {
    $routes->get('/MyProjects', 'Projects::index');
    $routes->get('/createProject', 'Projects::create');
    $routes->post('/store', 'Projects::save');
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

