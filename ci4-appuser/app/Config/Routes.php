<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Users::login');

// Auth routes
$routes->get('login', 'Users::login');
$routes->post('login', 'Users::login');
$routes->get('logout', 'Users::logout');

// Admin routes (require admin filter)
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('dashboard', 'Users::adminDashboard');
    $routes->get('users', 'Users::adminIndex');
    $routes->get('users/create', 'Users::adminCreate');
    $routes->post('users/create', 'Users::adminCreate');
    $routes->get('users/(:segment)/edit', 'Users::adminEdit/$1');
    $routes->post('users/(:segment)/edit', 'Users::adminEdit/$1');
    $routes->post('users/(:segment)/delete', 'Users::adminDelete/$1');
});

// Guest routes (require guest filter)
$routes->group('guest', ['filter' => 'guest'], function($routes) {
    $routes->get('dashboard', 'Users::guestDashboard');
    $routes->get('users', 'Users::guestIndex');
});