<?php
namespace App\Models;

use PDO;

class Sites {

    /**
     * Contar total de sitios
     */
    public static function countAll(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM sitios_interes")->fetchColumn();
    }

    /**
     * Obtener todos los sitios (ordenados)
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nombre, url, orden, activo, created_at FROM sitios_interes ORDER BY orden ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener solo los sitios activos (para la sidebar)
     */
    public static function getActivos(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, nombre, url FROM sitios_interes WHERE activo = 1 ORDER BY orden ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo sitio
     */
    public static function create(string $nombre, string $url, int $orden = 0): int
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO sitios_interes (nombre, url, orden, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nombre, $url, $orden]);

        return (int)$db->lastInsertId();
    }

    /**
     * Actualizar sitio existente
     */
    public static function update(int $id, string $nombre, string $url, int $orden, int $activo): bool
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("UPDATE sitios_interes SET nombre = ?, url = ?, orden = ?, activo = ? WHERE id = ?");
        return $stmt->execute([$nombre, $url, $orden, $activo, $id]);
    }

    /**
     * Eliminar sitio
     */
    public static function delete(int $id): bool
    {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM sitios_interes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Obtener favicon automático de Google
     */
    public static function getFaviconUrl(string $url): string
    {
        $domain = parse_url($url, PHP_URL_HOST);
        return "https://www.google.com/s2/favicons?domain={$domain}&sz=32";
    }
}
