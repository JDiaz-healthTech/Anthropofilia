<?php
namespace App\Models;

use PDO;

class Category {
    
    /**
     * Contar total de categorías
     */
    public static function countAll(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
    }
    
    /**
     * Obtener todas las categorías
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();  // ← CORRECCIÓN
        $stmt = $db->query("SELECT id_categoria, nombre_categoria, slug FROM categorias ORDER BY nombre_categoria ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nueva categoría
     */
    public static function create(string $nombre): int
    {
        $db = Database::getConnection();  // ← CORRECCIÓN
        $slug = self::generateSlug($nombre);
        
        $stmt = $db->prepare("INSERT INTO categorias (nombre_categoria, slug) VALUES (?, ?)");
        $stmt->execute([$nombre, $slug]);
        
        return (int)$db->lastInsertId();
    }

    /**
     * Actualizar categoría existente
     */
    public static function update(int $id, string $nombre): bool
    {
        $db = Database::getConnection();  // ← CORRECCIÓN
        $slug = self::generateSlug($nombre);
        
        $stmt = $db->prepare("UPDATE categorias SET nombre_categoria = ?, slug = ? WHERE id_categoria = ?");
        return $stmt->execute([$nombre, $slug, $id]);
    }

    /**
     * Eliminar categoría
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();  // ← CORRECCIÓN
        
        // Desvincular posts de esta categoría (quedan sin categoría)
        $stmt = $db->prepare("UPDATE posts SET id_categoria = NULL WHERE id_categoria = ?");
        $stmt->execute([$id]);
        
        // Eliminar la categoría
        $stmt = $db->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Generar slug desde texto
     */
    private static function generateSlug(string $text): string
    {
        // Convertir a minúsculas
        $text = mb_strtolower($text, 'UTF-8');
        
        // Reemplazar caracteres especiales
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'Ñ' => 'n', 'Ü' => 'u',
        ];
        $text = strtr($text, $replacements);
        
        // Eliminar caracteres no permitidos
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        
        // Reemplazar espacios múltiples por un guion
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Eliminar guiones al inicio/final
        return trim($text, '-');
    }
}