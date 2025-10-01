<?php
/**
 * Bootstrap - Inicialización de la aplicación
 * 
 * Este archivo se encarga de:
 * - Configurar el autoloader manual (sin Composer)
 * - Cargar variables de entorno
 * - Establecer configuraciones globales
 * - Configurar headers HTTP necesarios
 */

// Iniciar sesión si es necesario
session_start();

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores (desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Autoloader PSR-4 simplificado
 * Convierte nombres de clases a rutas de archivos
 */
spl_autoload_register(function ($class_name) {
    // Directorio base donde están las clases
    $base_directory = __DIR__ . '/';
    
    // Convertir namespace a ruta de archivo
    // Ejemplo: Domain\User\User -> Domain/User/User.php
    $file_path = $base_directory . str_replace('\\', '/', $class_name) . '.php';
    
    // Si el archivo existe, cargarlo
    if (file_exists($file_path)) {
        require_once $file_path;
    }
});

/**
 * Cargar configuración desde archivo .env
 * Función simple para leer variables de entorno
 */
function loadEnvironment($file_path) {
    if (!file_exists($file_path)) {
        die('Archivo .env no encontrado. Copia .env.example a .env');
    }
    
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Separar clave=valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remover comillas si existen
            $value = trim($value, '"\'');
            
            // Definir como constante y en $_ENV
            if (!defined($key)) {
                define($key, $value);
            }
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Cargar variables de entorno
loadEnvironment(__DIR__ . '/../.env');

$request_uri = $_SERVER['REQUEST_URI'] ?? '';

if (strpos($request_uri, '/api/') !== false) {
    // Headers para API
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 3600');
    
    // Manejar preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Función helper para debugging (solo desarrollo)
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Función helper para obtener variables de entorno
 */
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

/**
 * Función helper para sanitizar strings
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Función helper para validar JSON
 */
function isValidJson($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}