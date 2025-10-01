<?php

namespace Domain\Role;

/**
 * Role - Entidad de dominio
 * 
 * Representa un rol del sistema
 */
class Role
{
    private ?int $id;
    private string $role_name;
    private ?string $created_at;
    private array $permissions;
    
    public function __construct(
        string $role_name,
        ?int $id = null,
        ?string $created_at = null,
        array $permissions = []
    ) {
        $this->id = $id;
        $this->role_name = $role_name;
        $this->created_at = $created_at;
        $this->permissions = $permissions;
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getRoleName(): string
    {
        return $this->role_name;
    }
    
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
    public function getPermissions(): array
    {
        return $this->permissions;
    }
    
    // Setters
    public function setRoleName(string $role_name): void
    {
        $this->role_name = $role_name;
    }
    
    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }
    
    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permission_name): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission['permission_name'] === $permission_name) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Convertir a array (para respuestas JSON)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'role_name' => $this->role_name,
            'created_at' => $this->created_at,
            'permissions' => $this->permissions
        ];
    }
}