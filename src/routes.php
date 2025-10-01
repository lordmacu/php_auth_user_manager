<?php

/**
 * Definición de rutas de la API
 * 
 * Aquí se definen todas las rutas disponibles
 */

use Presentation\Controllers\AuthController;
use Presentation\Controllers\PageController;
use Presentation\Controllers\UserController;
use Presentation\Controllers\RoleController;

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================


$router->get('/', function() {
    $controller = new PageController();
    $controller->login();
});

$router->get('/dashboard', function() {
    $controller = new PageController();
    $controller->dashboard();
});

// Login
$router->post('/api/login', function() {
    $controller = new AuthController();
    $controller->login();
});

// ============================================
// RUTAS PROTEGIDAS - USUARIOS
// ============================================

// Listar todos los usuarios
$router->get('/api/users', function() {
    $controller = new UserController();
    $controller->index();
});

// Obtener un usuario específico
$router->get('/api/users/:id', function($id) {
    $controller = new UserController();
    $controller->show((int)$id);
});

// Crear un nuevo usuario
$router->post('/api/users', function() {
    $controller = new UserController();
    $controller->create();
});

// Actualizar un usuario
$router->put('/api/users/:id', function($id) {
    $controller = new UserController();
    $controller->update((int)$id);
});

// Eliminar un usuario
$router->delete('/api/users/:id', function($id) {
    $controller = new UserController();
    $controller->delete((int)$id);
});

// ============================================
// RUTAS PROTEGIDAS - ROLES
// ============================================

// Listar todos los roles
$router->get('/api/roles', function() {
    $controller = new RoleController();
    $controller->index();
});

// Obtener un rol específico
$router->get('/api/roles/:id', function($id) {
    $controller = new RoleController();
    $controller->show((int)$id);
});

// Crear un nuevo rol
$router->post('/api/roles', function() {
    $controller = new RoleController();
    $controller->create();
});

// Actualizar un rol
$router->put('/api/roles/:id', function($id) {
    $controller = new RoleController();
    $controller->update((int)$id);
});

// Eliminar un rol
$router->delete('/api/roles/:id', function($id) {
    $controller = new RoleController();
    $controller->delete((int)$id);
});

// ============================================
// RUTAS PROTEGIDAS - PERMISOS
// ============================================

// Listar todos los permisos disponibles
$router->get('/api/permissions', function() {
    $controller = new RoleController();
    $controller->getPermissions();
});