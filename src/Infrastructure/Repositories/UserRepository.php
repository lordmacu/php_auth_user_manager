<?php

namespace Infrastructure\Repositories;

use Config\Database;
use Domain\User\User;
use Domain\User\IUserRepository;
use PDO;

/**
 * UserRepository - Implementación del repositorio de usuarios
 * 
 * Maneja todas las operaciones de base de datos para usuarios
 */
class UserRepository implements IUserRepository
{
    private PDO $connection;
    
    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los usuarios con su rol
     */
    public function findAll(): array
    {
        $query = "
            SELECT u.*, r.role_name 
            FROM users u
            INNER JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC
        ";
        
        $stmt = $this->connection->query($query);
        $rows = $stmt->fetchAll();
        
        $users = [];
        foreach ($rows as $row) {
            $users[] = $this->mapToEntity($row);
        }
        
        return $users;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function findById(int $id): ?User
    {
        $query = "
            SELECT u.*, r.role_name 
            FROM users u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        return $row ? $this->mapToEntity($row) : null;
    }
    
    /**
     * Obtener usuario por email
     */
    public function findByEmail(string $email): ?User
    {
        $query = "
            SELECT u.*, r.role_name 
            FROM users u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE u.email = ?
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        
        return $row ? $this->mapToEntity($row) : null;
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function create(User $user): int
    {
        $query = "
            INSERT INTO users (name, email, password, role_id, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRoleId()
        ]);
        
        return (int) $this->connection->lastInsertId();
    }
    
    /**
     * Actualizar un usuario
     */
    public function update(User $user): bool
    {
        $query = "
            UPDATE users 
            SET name = ?, email = ?, role_id = ? 
            WHERE id = ?
        ";
        
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getRoleId(),
            $user->getId()
        ]);
    }
    
    /**
     * Eliminar un usuario
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM users WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$id]);
    }
    
    /**
     * Verificar si un email ya existe
     */
    public function emailExists(string $email, ?int $exclude_id = null): bool
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = ?";
        $params = [$email];
        
        if ($exclude_id !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Mapear fila de BD a entidad User
     */
    private function mapToEntity(array $row): User
    {
        return new User(
            name: $row['name'],
            email: $row['email'],
            password: $row['password'],
            role_id: $row['role_id'],
            id: $row['id'],
            role_name: $row['role_name'] ?? null,
            created_at: $row['created_at'] ?? null
        );
    }
}