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

$routes->get('/Profile', 'Profile::show', ['filter' => 'auth']);

$routes->group('/Tasks', ['filter' => 'auth'], function($routes) {
    $routes->get('MyDay', 'Tasks::myDay');      //any logged-in user
    $routes->get('MyTasks', 'Tasks::myTasks');  // any logged-in user
    $routes->get('createTask', 'Tasks::create', ['filter' => 'role:Profile_Admin,Manager,Head_Of_Team']);
    $routes->post('store', 'Tasks::save', ['filter' => 'role:Profile_Admin,Manager,Head_Of_Team']);
    $routes->post('updateState/(:num)', 'Tasks::updateState/$1'); // any logged-in user (ownership check in controller)
    $routes->post('delete/(:num)', 'Tasks::delete/$1');  
});

$routes->group('/Projects', ['filter' => 'auth'], function($routes) {
    $routes->get('MyProjects', 'Projects::showMyProjects');
    $routes->get('createProject', 'Projects::create', ['filter' => 'role:Profile_Admin,Manager']);
    $routes->post('store', 'Projects::save', ['filter' => 'role:Profile_Admin,Manager']);
    $routes->post('updateState/(:num)', 'Projects::updateState/$1');
});

$routes->group('/IAM', ['filter' => 'auth', 'role:Profile_Admin,Manager'], function($routes) {
    $routes->get('Users', 'IAM\Users::showUsers');
    $routes->get('Users/createUser', 'IAM\Users::create', ['filter' => 'role:Profile_Admin']);
    $routes->post('Users/store', 'IAM\Users::store', ['filter' => 'role:Profile_Admin']);
    $routes->post('Users/delete/(:num)', 'IAM\Users::deleteUser/$1', ['filter' => 'role:Profile_Admin']);

    $routes->get('Roles', 'IAM\Roles::showRoles');
    $routes->get('Roles/createRole', 'IAM\Roles::create');
    $routes->post('Roles/store', 'IAM\Roles::store');

    $routes->get('Actions', 'IAM\Actions::showActions');
    $routes->get('Actions/createAction', 'IAM\Actions::create');
    $routes->post('Actions/store', 'IAM\Actions::store');
});

