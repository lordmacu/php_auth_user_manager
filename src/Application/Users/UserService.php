<?php

namespace Application\Users;

use Domain\User\User;
use Domain\User\IUserRepository;
use Domain\Role\IRoleRepository;
use Infrastructure\Security\Hash;

/**
 * UserService - Servicio de gestión de usuarios
 * 
 * Maneja la lógica de negocio para operaciones CRUD de usuarios
 */
class UserService
{
    private IUserRepository $user_repository;
    private IRoleRepository $role_repository;
    private UserValidator $validator;
    
    public function __construct(
        IUserRepository $user_repository,
        IRoleRepository $role_repository
    ) {
        $this->user_repository = $user_repository;
        $this->role_repository = $role_repository;
        $this->validator = new UserValidator($user_repository, $role_repository);
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function getAllUsers(): array
    {
        $users = $this->user_repository->findAll();
        
        $result = [];
        foreach ($users as $user) {
            $result[] = $user->toArray();
        }
        
        return $result;
    }
    
    /**
     * Obtener un usuario por ID
     */
    public function getUserById(int $id): array|false
    {
        $user = $this->user_repository->findById($id);
        
        if (!$user) {
            return false;
        }
        
        return $user->toArray();
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function createUser(array $data): array
    {
        $errors = $this->validator->validateCreate($data);
        
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        $user = new User(
            name: $data['name'],
            email: $data['email'],
            password: Hash::make($data['password']),
            role_id: $data['role_id']
        );
        
        $user_id = $this->user_repository->create($user);
        
        return [
            'success' => true,
            'user_id' => $user_id
        ];
    }
    
    /**
     * Actualizar un usuario
     */
    public function updateUser(int $id, array $data): array
    {
        $user = $this->user_repository->findById($id);
        
        if (!$user) {
            return ['errors' => ['Usuario no encontrado']];
        }
        
        $errors = $this->validator->validateUpdate($data, $id);
        
        if (!empty($errors)) {
            return ['errors' => $errors];
        }
        
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRoleId($data['role_id']);
        
        if (!empty($data['password'])) {
            $user->setPassword(Hash::make($data['password']));
        }
        
        $this->user_repository->update($user);
        
        return [
            'success' => true,
            'user' => $user->toArray()
        ];
    }
    
    /**
     * Eliminar un usuario
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->user_repository->findById($id);
        
        if (!$user) {
            return false;
        }
        
        return $this->user_repository->delete($id);
    }
}