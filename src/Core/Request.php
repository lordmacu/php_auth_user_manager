<?php

namespace Core;

/**
 * Request - Manejo de datos de la petición HTTP
 */
class Request
{
    private array $data;
    
    public function __construct()
    {
        $this->data = $this->parseData();
    }
    
    /**
     * Obtener todos los datos de la petición
     */
    public function all(): array
    {
        return $this->data;
    }
    
    /**
     * Obtener un valor específico
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
    
    /**
     * Obtener el token del header Authorization
     */
    public function getToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Parsear datos según el método HTTP
     */
    private function parseData(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET, DELETE
        if ($method === 'GET' || $method === 'DELETE') {
            return $_GET;
        }
        
        // POST, PUT - JSON
        $input = file_get_contents('php://input');
        $json_data = json_decode($input, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json_data ?? [];
        }
        
        // POST - Form data
        return $_POST;
    }
}