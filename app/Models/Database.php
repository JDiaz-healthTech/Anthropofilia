<?php
namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // 1. CARGAMOS EL MAPA (Tu archivo de config)
        // Usamos __DIR__ para salir de 'app/Models' (../..) y entrar en 'config'
        $config = require __DIR__ . '/../../config/database.php';

        // 2. Construimos la dirección de conexión
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['database'],
            $config['charset']
        );

        try {
            // 3. Abrimos la línea con MySQL
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            // Si falla, lanzamos error pero sin mostrar la contraseña
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    // Patrón Singleton: Para usar siempre la misma conexión abierta
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
    
    // Evitamos clonar la conexión
    private function __clone() {}
}