<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('registro', 'AuthController::registro');
$routes->post('guardar-registro', 'AuthController::guardarRegistro');

$routes->get('login', 'AuthController::login');
$routes->post('validar-login', 'AuthController::validarLogin');

$routes->get('logout', 'AuthController::logout');
