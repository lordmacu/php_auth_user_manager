<?php

namespace Domain\User;

/**
 * User - Entidad de dominio
 * 
 * Representa un usuario del sistema
 */
class User
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;
    private int $role_id;
    private ?string $role_name;
    private ?string $created_at;
    
    public function __construct(
        string $name,
        string $email,
        string $password,
        int $role_id,
        ?int $id = null,
        ?string $role_name = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->role_name = $role_name;
        $this->created_at = $created_at;
    }
    
    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function getRoleId(): int
    {
        return $this->role_id;
    }
    
    public function getRoleName(): ?string
    {
        return $this->role_name;
    }
    
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    
    // Setters
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }
    
    /**
     * Convertir a array (para respuestas JSON)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'role_name' => $this->role_name,
            'created_at' => $this->created_at
        ];
    }
}