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
$routes->get('usuarios', 'UsuariosController::index');

$routes->get('chat/(:num)', 'ChatController::chat/$1');
$routes->post('enviar', 'ChatController::enviar');

$routes->get('/forgot', 'AuthController::forgotForm');
$routes->post('/forgot', 'AuthController::sendReset');

$routes->get('/reset/(:any)', 'AuthController::resetForm/$1');
$routes->post('/reset', 'AuthController::resetPassword');
