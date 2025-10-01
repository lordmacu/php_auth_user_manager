<?php

/**
 * Punto de entrada de la API
 * 
 * Carga el bootstrap y las rutas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/../src/bootstrap.php';

use Core\Router;

// Crear instancia del router
$router = new Router();

// Cargar rutas
require_once __DIR__ . '/../src/routes.php';

// Ejecutar router
$router->dispatch();