<?php

namespace Application\Roles;

use Domain\Role\IRoleRepository;

/**
 * RoleValidator - Validador de datos de rol
 * 
 * Maneja todas las validaciones relacionadas con roles
 * Separado del service para cumplir Single Responsibility
 */
class RoleValidator
{
    public function __construct(
        private IRoleRepository $role_repository
    ) {}

    /**
     * Validar datos para crear rol
     */
    public function validateCreate(array $data): array
    {
        return $this->validateRole($data);
    }

    /**
     * Validar datos para actualizar rol
     */
    public function validateUpdate(array $data, int $role_id): array
    {
        return $this->validateRole($data, $role_id);
    }

    /**
     * Validar datos del rol
     */
    private function validateRole(array $data, ?int $role_id = null): array
    {
        $errors = [];

        // Validar nombre del rol
        if (empty($data['role_name'])) {
            $errors[] = 'El nombre del rol es requerido';
        } elseif (strlen($data['role_name']) < 3) {
            $errors[] = 'El nombre del rol debe tener al menos 3 caracteres';
        } elseif ($this->role_repository->roleNameExists($data['role_name'], $role_id)) {
            $errors[] = 'El nombre del rol ya existe';
        }

        // Validar permisos (opcional, pueden ser vacíos)
        if (isset($data['permissions']) && !is_array($data['permissions'])) {
            $errors[] = 'Los permisos deben ser un array';
        }

        return $errors;
    }
}
