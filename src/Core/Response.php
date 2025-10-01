<?php

namespace Core;

/**
 * Response - Respuestas JSON estandarizadas
 */
class Response
{
    /**
     * Enviar respuesta JSON
     */
    private static function json(array $data, int $code): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Respuesta exitosa 200
     */
    public static function success(array $data, int $code = 200): void
    {
        self::json($data, $code);
    }
    
    /**
     * Recurso creado 201
     */
    public static function created(array $data): void
    {
        self::json($data, 201);
    }
    
    /**
     * Error de validación 400
     */
    public static function badRequest(array $errors): void
    {
        self::json(['errors' => $errors], 400);
    }
    
    /**
     * No autorizado 401
     */
    public static function unauthorized(string $message = 'No autorizado'): void
    {
        self::json(['error' => $message], 401);
    }
    
    /**
     * Prohibido 403
     */
    public static function forbidden(string $message = 'Sin permisos'): void
    {
        self::json(['error' => $message], 403);
    }
    
    /**
     * No encontrado 404
     */
    public static function notFound(string $message = 'No encontrado'): void
    {
        self::json(['error' => $message], 404);
    }
    
    /**
     * Error del servidor 500
     */
    public static function error(string $message = 'Error del servidor'): void
    {
        self::json(['error' => $message], 500);
    }
}