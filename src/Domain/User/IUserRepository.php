<?php

namespace Domain\User;

/**
 * IUserRepository - Contrato para el repositorio de usuarios
 * 
 * Define las operaciones que debe implementar cualquier
 * repositorio de usuarios (Dependency Inversion Principle)
 */
interface IUserRepository
{
    /**
     * Obtener todos los usuarios
     */
    public function findAll(): array;
    
    /**
     * Obtener usuario por ID
     */
    public function findById(int $id): ?User;
    
    /**
     * Obtener usuario por email
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Crear un nuevo usuario
     */
    public function create(User $user): int;
    
    /**
     * Actualizar un usuario
     */
    public function update(User $user): bool;
    
    /**
     * Eliminar un usuario
     */
    public function delete(int $id): bool;
    
    /**
     * Verificar si un email ya existe
     */
    public function emailExists(string $email, ?int $exclude_id = null): bool;
}