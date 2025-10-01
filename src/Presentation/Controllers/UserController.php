<?php

namespace Presentation\Controllers;

use Core\Controller;
use Core\Response;
use Application\Users\UserService;
use Application\Roles\PermissionChecker;
use Infrastructure\Repositories\UserRepository;
use Infrastructure\Repositories\RoleRepository;
use Presentation\Middleware\AuthMiddleware;

/**
 * UserController - Controlador de usuarios
 * 
 * Maneja las peticiones CRUD de usuarios
 */
class UserController extends Controller
{
    private UserService $user_service;
    private PermissionChecker $permission_checker;
    private AuthMiddleware $auth_middleware;
    
    public function __construct()
    {
        parent::__construct();
        
        $user_repository = new UserRepository();
        $role_repository = new RoleRepository();
        
        $this->user_service = new UserService($user_repository, $role_repository);
        $this->permission_checker = new PermissionChecker($role_repository);
        $this->auth_middleware = new AuthMiddleware();
    }
    
    /**
     * Listar todos los usuarios
     * GET /api/users
     */
    public function index(): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canViewAllUsers($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para ver todos los usuarios');
        }
        
        $users = $this->user_service->getAllUsers();
        
        Response::success(['users' => $users]);
    }
    
    /**
     * Obtener un usuario específico
     * GET /api/users/:id
     */
    public function show(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // User solo puede ver su propio perfil
        if (!$this->permission_checker->canViewAllUsers($current_user['role_id'])) {
            if ($current_user['user_id'] != $id) {
                Response::forbidden('Solo puedes ver tu propio perfil');
            }
        }
        
        $user = $this->user_service->getUserById($id);
        
        if (!$user) {
            Response::notFound('Usuario no encontrado');
        }
        
        Response::success(['user' => $user]);
    }
    
    /**
     * Crear un nuevo usuario
     * POST /api/users
     */
    public function create(): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canCreateUsers($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para crear usuarios');
        }
        
        $data = $this->request->all();
        $result = $this->user_service->createUser($data);
        
        if (isset($result['errors'])) {
            Response::badRequest($result['errors']);
        }
        
        Response::created([
            'message' => 'Usuario creado exitosamente',
            'user_id' => $result['user_id']
        ]);
    }
    
    /**
     * Actualizar un usuario
     * PUT /api/users/:id
     */
    public function update(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canUpdateUsers($current_user['role_id'])) {
            // User solo puede actualizar su propio perfil
            if ($current_user['user_id'] != $id) {
                Response::forbidden('Solo puedes actualizar tu propio perfil');
            }
        }
        
        $data = $this->request->all();
        $result = $this->user_service->updateUser($id, $data);
        
        if (isset($result['errors'])) {
            Response::badRequest($result['errors']);
        }
        
        Response::success([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $result['user']
        ]);
    }
    
    /**
     * Eliminar un usuario
     * DELETE /api/users/:id
     */
    public function delete(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canDeleteUsers($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para eliminar usuarios');
        }
        
        // No permitir auto-eliminación
        if ($current_user['user_id'] == $id) {
            Response::badRequest(['No puedes eliminar tu propio usuario']);
        }
        
        $deleted = $this->user_service->deleteUser($id);
        
        if (!$deleted) {
            Response::notFound('Usuario no encontrado');
        }
        
        Response::success(['message' => 'Usuario eliminado exitosamente']);
    }
}