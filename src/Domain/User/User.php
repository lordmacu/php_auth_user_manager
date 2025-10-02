<?php

namespace Domain\User;

/**
 * User - Entidad de dominio
 * 
 * Representa un usuario del sistema
 */
class User
{
    public function __construct(
        private string $name,
        private string $email,
        private string $password,
        private int $role_id,
        private ?int $id = null,
        private ?string $role_name = null,
        private ?string $created_at = null
    ) {}

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
