<?php

namespace Domain\Role;

/**
 * IRoleRepository - Contrato para el repositorio de roles
 * 
 * Define las operaciones que debe implementar cualquier
 * repositorio de roles (Dependency Inversion Principle)
 */
interface IRoleRepository
{
    /**
     * Obtener todos los roles
     */
    public function findAll(): array;
    
    /**
     * Obtener rol por ID
     */
    public function findById(int $id): ?Role;
    
    /**
     * Obtener rol por nombre
     */
    public function findByName(string $role_name): ?Role;
    
    /**
     * Crear un nuevo rol
     */
    public function create(Role $role): int;
    
    /**
     * Actualizar un rol
     */
    public function update(Role $role): bool;
    
    /**
     * Eliminar un rol
     */
    public function delete(int $id): bool;
    
    /**
     * Verificar si un nombre de rol ya existe
     */
    public function roleNameExists(string $role_name, ?int $exclude_id = null): bool;
    
    /**
     * Asignar permisos a un rol
     */
    public function assignPermissions(int $role_id, array $permission_ids): bool;
    
    /**
     * Obtener permisos de un rol
     */
    public function getPermissions(int $role_id): array;
}