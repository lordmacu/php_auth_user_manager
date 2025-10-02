<?php

namespace Infrastructure\Repositories;

use Config\Database;
use Domain\Role\Role;
use Domain\Role\IRoleRepository;
use PDO;

/**
 * RoleRepository - Implementación del repositorio de roles
 * 
 * Maneja todas las operaciones de base de datos para roles
 */
class RoleRepository implements IRoleRepository
{
    private PDO $connection;
    
    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los roles con sus permisos
     */
    public function findAll(): array
    {
        $query = "SELECT * FROM roles ORDER BY id ASC";
        
        $stmt = $this->connection->query($query);
        $rows = $stmt->fetchAll();
        
        $roles = [];
        foreach ($rows as $row) {
            $role = $this->mapToEntity($row);
            $role->setPermissions($this->getPermissions($role->getId()));
            $roles[] = $role;
        }
        
        return $roles;
    }
    
    /**
     * Obtener rol por ID con sus permisos
     */
    public function findById(int $id): ?Role
    {
        $query = "SELECT * FROM roles WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        $role = $this->mapToEntity($row);
        $role->setPermissions($this->getPermissions($id));
        
        return $role;
    }
    
    /**
     * Obtener rol por nombre
     */
    public function findByName(string $role_name): ?Role
    {
        $query = "SELECT * FROM roles WHERE role_name = ?";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$role_name]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        $role = $this->mapToEntity($row);
        $role->setPermissions($this->getPermissions($role->getId()));
        
        return $role;
    }
    
    /**
     * Crear un nuevo rol
     */
    public function create(Role $role): int
    {
        $query = "
            INSERT INTO roles (role_name, created_at) 
            VALUES (?, NOW())
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$role->getRoleName()]);
        
        return (int) $this->connection->lastInsertId();
    }
    
    /**
     * Actualizar un rol
     */
    public function update(Role $role): bool
    {
        $query = "UPDATE roles SET role_name = ? WHERE id = ?";
        
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([
            $role->getRoleName(),
            $role->getId()
        ]);
    }
    
    /**
     * Eliminar un rol
     */
    public function delete(int $id): bool
    {
        $query_permissions = "DELETE FROM role_has_permissions WHERE role_id = ?";
        $stmt = $this->connection->prepare($query_permissions);
        $stmt->execute([$id]);
        
        $query = "DELETE FROM roles WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$id]);
    }
    
    /**
     * Verificar si un nombre de rol ya existe
     */
    public function roleNameExists(string $role_name, ?int $exclude_id = null): bool
    {
        $query = "SELECT COUNT(*) FROM roles WHERE role_name = ?";
        $params = [$role_name];
        
        if ($exclude_id !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Asignar permisos a un rol
     */
    public function assignPermissions(int $role_id, array $permission_ids): bool
    {
        $query_delete = "DELETE FROM role_has_permissions WHERE role_id = ?";
        $stmt = $this->connection->prepare($query_delete);
        $stmt->execute([$role_id]);
        
        if (empty($permission_ids)) {
            return true;
        }
        
        $query_insert = "INSERT INTO role_has_permissions (role_id, permission_id) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query_insert);
        
        foreach ($permission_ids as $permission_id) {
            $stmt->execute([$role_id, $permission_id]);
        }
        
        return true;
    }
    
    /**
     * Obtener permisos de un rol
     */
    public function getPermissions(int $role_id): array
    {
        $query = "
            SELECT p.id, p.permission_name 
            FROM permissions p
            INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id
            WHERE rhp.role_id = ?
        ";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$role_id]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Mapear fila de BD a entidad Role
     */
    private function mapToEntity(array $row): Role
    {
        return new Role(
            role_name: $row['role_name'],
            id: $row['id'],
            created_at: $row['created_at'] ?? null
        );
    }
}