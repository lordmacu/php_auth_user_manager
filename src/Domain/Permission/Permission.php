<?php

namespace Domain\Permission;

/**
 * Permission - Entidad de dominio
 * 
 * Representa un permiso del sistema
 */
class Permission
{
    private ?int $id;
    private string $permission_name;
    private ?string $created_at;
    
    public function __construct(
        string $permission_name,
        ?int $id = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->permission_name = $permission_name;
        $this->created_at = $created_at;
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getPermissionName(): string
    {
        return $this->permission_name;
    }
    
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
    // Setter
    public function setPermissionName(string $permission_name): void
    {
        $this->permission_name = $permission_name;
    }
    
    /**
     * Convertir a array (para respuestas JSON)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'permission_name' => $this->permission_name,
            'created_at' => $this->created_at
        ];
    }
}