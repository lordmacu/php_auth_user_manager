<?php

namespace Core;

/**
 * Router - Enrutador simple para la API
 */
class Router
{
    private array $routes = [];
    
    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }
    
    public function put(string $path, callable $callback): void
    {
        $this->routes['PUT'][$path] = $callback;
    }
    
    public function delete(string $path, callable $callback): void
    {
        $this->routes['DELETE'][$path] = $callback;
    }
    
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Buscar ruta exacta
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
            return;
        }
        
        // Buscar ruta con parámetros (:id)
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = preg_replace('/:\w+/', '([^/]+)', $route);
            
            if (preg_match("#^{$pattern}$#", $path, $matches)) {
                array_shift($matches);
                call_user_func_array($callback, $matches);
                return;
            }
        }
        
        // 404
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
    }
}