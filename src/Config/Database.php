<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Database - Singleton para gestión de conexión PDO
 * 
 * Implementa el patrón Singleton para garantizar una única
 * instancia de conexión a la base de datos.
 */
class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    
    /**
     * Constructor privado (patrón Singleton)
     */
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Obtener la instancia única de Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Establecer conexión con la base de datos
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                env('DB_HOST', 'localhost'),
                env('DB_PORT', '3306'),
                env('DB_NAME', 'auth_system')
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO(
                $dsn,
                env('DB_USER', 'root'),
                env('DB_PASSWORD', ''),
                $options
            );
            
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener la conexión PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }
}