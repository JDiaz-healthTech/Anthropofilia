<?php
namespace App\Models;

use PDO;

class Category {
    
    // ... (tus otros métodos existentes aquí)
    
    /**
     * Contar total de categorías
     */
    public static function countAll(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }
    
} // ← Esta llave cierra la clase