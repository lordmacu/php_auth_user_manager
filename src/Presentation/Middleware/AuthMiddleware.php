<?php

namespace Presentation\Middleware;

use Core\Response;
use Application\Auth\AuthService;
use Infrastructure\Repositories\UserRepository;

/**
 * AuthMiddleware - Middleware de autenticación
 * 
 * Verifica que el usuario esté autenticado mediante JWT
 */
class AuthMiddleware
{
    private AuthService $auth_service;
    
    public function __construct()
    {
        $user_repository = new UserRepository();
        $this->auth_service = new AuthService($user_repository);
    }
    
    /**
     * Autenticar usuario mediante token JWT
     * Retorna los datos del usuario del token
     */
    public function authenticate(): array
    {
         $token = $this->getToken();
        
        if (!$token) {
            Response::unauthorized('Token no proporcionado');
        }
        
         $payload = $this->auth_service->validateToken($token);
        
        if (!$payload) {
            Response::unauthorized('Token inválido o expirado');
        }
        
        return $payload;
    }
    
    /**
     * Obtener token del header Authorization
     */
    private function getToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        // Formato: Bearer eyJhbGciOiJIUzI1NiIsIn...
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}