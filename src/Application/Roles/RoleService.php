<?php

namespace Application\Roles;

use Domain\Role\Role;
use Domain\Role\IRoleRepository;

/**
 * RoleService - Servicio de gestión de roles
 * 
 * Maneja la lógica de negocio para operaciones CRUD de roles
 */
class RoleService
{
    private IRoleRepository $role_repository;
    private RoleValidator $validator;
    
    public function __construct(IRoleRepository $role_repository)
    {
        $this->role_repository = $role_repository;
        $this->validator = new RoleValidator($role_repository);
    }
    
    /**
     * Obtener todos los roles con sus permisos
     */
    public function getAllRoles(): array
    {
        $roles = $this->role_repository->findAll();
        
        $result = [];
        foreach ($roles as $role) {
            $result[] = $role->toArray();
        }
        
        return $result;
    }
    
    /**
     * Obtener un rol por ID
     */
    public function getRoleById(int $id): array|false
    {
        $role = $this->role_repository->findById($id);
        
        if (!$role) {
            return false;
        }
        
        return $role->toArray();
    }
    
    /**
     * Crear un nuevo rol
     */
    public function createRole(array $data): array
    {
        $errors = $this->validator->validateCreate($data);
        
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        $role = new Role(
            role_name: $data['role_name']
        );
        
        $role_id = $this->role_repository->create($role);
        
        if (!empty($data['permissions'])) {
            $this->role_repository->assignPermissions($role_id, $data['permissions']);
        }
        
        return [
            'success' => true,
            'role_id' => $role_id
        ];
    }
    
    /**
     * Actualizar un rol
     */
    public function updateRole(int $id, array $data): array
    {
        $role = $this->role_repository->findById($id);
        
        if (!$role) {
            return ['errors' => ['Rol no encontrado']];
        }
        
        $errors = $this->validator->validateUpdate($data, $id);
        
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        $role->setRoleName($data['role_name']);
        $this->role_repository->update($role);
        
        if (isset($data['permissions'])) {
            $this->role_repository->assignPermissions($id, $data['permissions']);
        }
        
        return [
            'success' => true,
            'role' => $this->role_repository->findById($id)->toArray()
        ];
    }
    
    /**
     * Eliminar un rol
     */
    public function deleteRole(int $id): bool
    {
        $role = $this->role_repository->findById($id);
        
        if (!$role) {
            return false;
        }
        
        return $this->role_repository->delete($id);
    }
    
    /**
     * Obtener todos los permisos disponibles
     */
    public function getAllPermissions(): array
    {
        $db = \Config\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM permissions ORDER BY id ASC");
        
        return $stmt->fetchAll();
    }
}