<?php

namespace Infrastructure\Security;

/**
 * Hash - Manejo de contraseñas
 * 
 * Wrapper simple para funciones de hashing de PHP
 */
class Hash
{
    /**
     * Hashear una contraseña
     */
    public static function make(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Verificar si una contraseña coincide con su hash
     */
    public static function verify(string $password, string $hash): bool
    {
        
        return password_verify($password, $hash);
    }
}