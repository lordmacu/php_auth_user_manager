<?php

namespace Application\Auth;

use Domain\User\IUserRepository;
use Infrastructure\Security\JWT;
use Infrastructure\Security\Hash;

/**
 * AuthService - Servicio de autenticación
 * 
 * Maneja la lógica de login y validación de tokens
 */
class AuthService
{
    private IUserRepository $user_repository;
    private JWT $jwt;
    
    public function __construct(IUserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
        $this->jwt = new JWT();
    }
    
    /**
     * Autenticar usuario y generar token
     */
    public function login(string $email, string $password): array|false
    {
        // Buscar usuario por email
        $user = $this->user_repository->findByEmail($email);

        
        if (!$user) {
            return false;
        }
        
        // Verificar contraseña
        if (!Hash::verify($password, $user->getPassword())) {
            return false;
        }
        
        // Generar token
        $token = $this->jwt->generate([
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'role_id' => $user->getRoleId()
        ]);
        
        return [
            'token' => $token,
            'user' => $user->toArray()
        ];
    }
    
    /**
     * Validar token y obtener datos del usuario
     */
    public function validateToken(string $token): array|false
    {
        $payload = $this->jwt->validate($token);
        
        if (!$payload) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Obtener usuario actual desde el token
     */
    public function getCurrentUser(string $token): array|false
    {
        $payload = $this->validateToken($token);
        
        if (!$payload) {
            return false;
        }
        
        $user = $this->user_repository->findById($payload['user_id']);
        
        if (!$user) {
            return false;
        }
        
        return $user->toArray();
    }
}