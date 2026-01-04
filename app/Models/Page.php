<?php
namespace App\Models;

use PDO;

class Page {
    
    // ... (tus otros métodos existentes aquí)
    
    /**
     * Contar total de páginas
     */
    public static function countAll(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM paginas")->fetchColumn();
    }
    
} // ← Esta llave cierra la clase