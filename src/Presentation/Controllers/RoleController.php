<?php

namespace Presentation\Controllers;

use Core\Controller;
use Core\Response;
use Application\Roles\RoleService;
use Application\Roles\PermissionChecker;
use Infrastructure\Repositories\RoleRepository;
use Presentation\Middleware\AuthMiddleware;

/**
 * RoleController - Controlador de roles
 * 
 * Maneja las peticiones CRUD de roles
 */
class RoleController extends Controller
{
    private RoleService $role_service;
    private PermissionChecker $permission_checker;
    private AuthMiddleware $auth_middleware;
    
    public function __construct()
    {
        parent::__construct();
        
        $role_repository = new RoleRepository();
        
        $this->role_service = new RoleService($role_repository);
        $this->permission_checker = new PermissionChecker($role_repository);
        $this->auth_middleware = new AuthMiddleware();
    }
    
    /**
     * Listar todos los roles
     * GET /api/roles
     */
    public function index(): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para ver roles');
        }
        
        $roles = $this->role_service->getAllRoles();
        
        Response::success(['roles' => $roles]);
    }
    
    /**
     * Obtener un rol específico
     * GET /api/roles/:id
     */
    public function show(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Verificar permisos
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para ver roles');
        }
        
        $role = $this->role_service->getRoleById($id);
        
        if (!$role) {
            Response::notFound('Rol no encontrado');
        }
        
        Response::success(['role' => $role]);
    }
    
    /**
     * Crear un nuevo rol
     * POST /api/roles
     */
    public function create(): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Solo SuperAdmin puede crear roles
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('Solo SuperAdmin puede crear roles');
        }
        
        $data = $this->request->all();
        $result = $this->role_service->createRole($data);
        
        if (isset($result['errors'])) {
            Response::badRequest($result['errors']);
        }
        
        Response::created([
            'message' => 'Rol creado exitosamente',
            'role_id' => $result['role_id']
        ]);
    }
    
    /**
     * Actualizar un rol
     * PUT /api/roles/:id
     */
    public function update(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Solo SuperAdmin puede actualizar roles
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('Solo SuperAdmin puede actualizar roles');
        }
        
        $data = $this->request->all();
        $result = $this->role_service->updateRole($id, $data);
        
        if (isset($result['errors'])) {
            Response::badRequest($result['errors']);
        }
        
        Response::success([
            'message' => 'Rol actualizado exitosamente',
            'role' => $result['role']
        ]);
    }
    
    /**
     * Eliminar un rol
     * DELETE /api/roles/:id
     */
    public function delete(int $id): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Solo SuperAdmin puede eliminar roles
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('Solo SuperAdmin puede eliminar roles');
        }
        
        $deleted = $this->role_service->deleteRole($id);
        
        if (!$deleted) {
            Response::notFound('Rol no encontrado');
        }
        
        Response::success(['message' => 'Rol eliminado exitosamente']);
    }
    
    /**
     * Obtener todos los permisos disponibles
     * GET /api/permissions
     */
    public function getPermissions(): void
    {
        $current_user = $this->auth_middleware->authenticate();
        
        // Solo SuperAdmin puede ver permisos
        if (!$this->permission_checker->canManageRoles($current_user['role_id'])) {
            Response::forbidden('No tienes permiso para ver permisos');
        }
        
        $permissions = $this->role_service->getAllPermissions();
        
        Response::success(['permissions' => $permissions]);
    }
}