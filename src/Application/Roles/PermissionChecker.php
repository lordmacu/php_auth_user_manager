<?php

namespace Application\Roles;

use Domain\Role\IRoleRepository;

/**
 * PermissionChecker - Verificador de permisos
 * 
 * Maneja la lógica de autorización y verificación de permisos
 */
class PermissionChecker
{
    public function __construct(
        private IRoleRepository $role_repository
    ) {}
    /**
     * Verificar si un rol tiene un permiso específico
     */
    public function hasPermission(int $role_id, string $permission_name): bool
    {
        $role = $this->role_repository->findById($role_id);

        if (!$role) {
            return false;
        }

        return $role->hasPermission($permission_name);
    }

    /**
     * Verificar si un rol es SuperAdmin
     */
    public function isSuperAdmin(int $role_id): bool
    {
        $role = $this->role_repository->findById($role_id);

        if (!$role) {
            return false;
        }

        return $role->getRoleName() === 'SuperAdmin';
    }

    /**
     * Verificar si un rol es Admin
     */
    public function isAdmin(int $role_id): bool
    {
        $role = $this->role_repository->findById($role_id);

        if (!$role) {
            return false;
        }

        return $role->getRoleName() === 'Admin';
    }

    /**
     * Verificar si un usuario puede gestionar roles
     */
    public function canManageRoles(int $role_id): bool
    {
        return $this->isSuperAdmin($role_id);
    }

    /**
     * Verificar si un usuario puede crear usuarios
     */
    public function canCreateUsers(int $role_id): bool
    {
        return $this->isSuperAdmin($role_id) ||
            $this->isAdmin($role_id) ||
            $this->hasPermission($role_id, 'create-users');
    }

    /**
     * Verificar si un usuario puede actualizar usuarios
     */
    public function canUpdateUsers(int $role_id): bool
    {
        return $this->isSuperAdmin($role_id) ||
            $this->isAdmin($role_id) ||
            $this->hasPermission($role_id, 'update-users');
    }

    /**
     * Verificar si un usuario puede eliminar usuarios
     */
    public function canDeleteUsers(int $role_id): bool
    {
        return $this->isSuperAdmin($role_id) ||
            $this->isAdmin($role_id) ||
            $this->hasPermission($role_id, 'delete-users');
    }

    /**
     * Verificar si un usuario puede ver todos los usuarios
     */
    public function canViewAllUsers(int $role_id): bool
    {
        return $this->isSuperAdmin($role_id) ||
            $this->isAdmin($role_id) ||
            $this->hasPermission($role_id, 'view-all-users');
    }

    /**
     * Verificar si un usuario puede editar su propio perfil solamente
     */
    public function canOnlyEditOwnProfile(int $role_id, int $target_user_id, int $current_user_id): bool
    {
        if ($this->isSuperAdmin($role_id) || $this->isAdmin($role_id)) {
            return false;
        }

        return $target_user_id === $current_user_id;
    }
}
