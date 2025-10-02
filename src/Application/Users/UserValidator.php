<?php

namespace Application\Users;

use Domain\User\IUserRepository;
use Domain\Role\IRoleRepository;

/**
 * UserValidator - Validador de datos de usuario
 * 
 * Maneja todas las validaciones relacionadas con usuarios
 * Separado del service para cumplir Single Responsibility
 */
class UserValidator
{
    public function __construct(
        private IUserRepository $user_repository,
        private IRoleRepository $role_repository
    ) {}

    /**
     * Validar datos para crear usuario
     */
    public function validateCreate(array $data): array
    {
        $errors = [];

        $errors = array_merge($errors, $this->validateName($data));
        $errors = array_merge($errors, $this->validateEmail($data));
        $errors = array_merge($errors, $this->validatePassword($data));
        $errors = array_merge($errors, $this->validateRole($data));

        return $errors;
    }

    /**
     * Validar datos para actualizar usuario
     */
    public function validateUpdate(array $data, int $user_id): array
    {
        $errors = [];

        $errors = array_merge($errors, $this->validateName($data));
        $errors = array_merge($errors, $this->validateEmail($data, $user_id));
        $errors = array_merge($errors, $this->validateRole($data));

        // Password es opcional en update
        if (!empty($data['password'])) {
            $errors = array_merge($errors, $this->validatePassword($data));
        }

        return $errors;
    }

    /**
     * Validar nombre
     */
    private function validateName(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres';
        }

        return $errors;
    }

    /**
     * Validar email
     */
    private function validateEmail(array $data, ?int $exclude_id = null): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors[] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email no es válido';
        } elseif ($this->user_repository->emailExists($data['email'], $exclude_id)) {
            $errors[] = 'El email ya está registrado';
        }

        return $errors;
    }

    /**
     * Validar password
     */
    private function validatePassword(array $data): array
    {
        $errors = [];

        if (empty($data['password'])) {
            $errors[] = 'La contraseña es requerida';
        } elseif (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'La contraseña debe tener al menos ' . PASSWORD_MIN_LENGTH . ' caracteres';
        }

        return $errors;
    }

    /**
     * Validar rol
     */
    private function validateRole(array $data): array
    {
        $errors = [];

        if (empty($data['role_id'])) {
            $errors[] = 'El rol es requerido';
        } else {
            $role = $this->role_repository->findById($data['role_id']);
            if (!$role) {
                $errors[] = 'El rol seleccionado no existe';
            }
        }

        return $errors;
    }
}
