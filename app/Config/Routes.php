<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

/* AUTH */
$routes->get('registro', 'AuthController::registro');
$routes->post('guardar-registro', 'AuthController::guardarRegistro');

$routes->get('login', 'AuthController::login');
$routes->post('validar-login', 'AuthController::validarLogin');

$routes->get('logout', 'AuthController::logout');

/* USUARIOS */
$routes->get('usuarios', 'UsuariosController::index');

/* CHAT PRIVADO */
$routes->get('chat/(:num)', 'ChatController::chat/$1');
$routes->post('enviar', 'ChatController::enviar');
$routes->get('mensajes/(:num)', 'ChatController::obtenerMensajes/$1');

/* IA */
$routes->get('ia', 'IAController::index');
$routes->post('ia/chat', 'IAController::chat');

/* RECUPERAR PASSWORD */
$routes->get('forgot', 'AuthController::forgotForm');
$routes->post('forgot', 'AuthController::sendReset');
$routes->get('reset/(:any)', 'AuthController::resetForm/$1');
$routes->post('reset', 'AuthController::resetPassword');

/* GRUPOS */
$routes->get('grupos', 'GrupoController::index');
$routes->get('grupos/crear', 'GrupoController::crear');
$routes->post('grupos/guardar', 'GrupoController::guardar');

$routes->get('grupo_chat/(:num)', 'GrupoController::chat/$1');

$routes->post('grupo/enviar', 'GrupoController::enviarMensaje');
