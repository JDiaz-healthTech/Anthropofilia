<?php
// app/Models/UserPage.php
declare(strict_types=1);

namespace App\Models;

use PDO;

class UserPage
{
    /**
     * ¿El usuario sigue esta página?
     */
    public static function isFollowing(int $userId, int $pageId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT 1 FROM usuario_paginas WHERE id_usuario = ? AND id_pagina = ? LIMIT 1"
        );
        $stmt->execute([$userId, $pageId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Seguir una página. Idempotente: si ya la sigue, no falla.
     */
    public static function follow(int $userId, int $pageId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT IGNORE INTO usuario_paginas (id_usuario, id_pagina) VALUES (?, ?)"
        );
        return $stmt->execute([$userId, $pageId]);
    }

    /**
     * Dejar de seguir una página.
     */
    public static function unfollow(int $userId, int $pageId): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "DELETE FROM usuario_paginas WHERE id_usuario = ? AND id_pagina = ?"
        );
        return $stmt->execute([$userId, $pageId]);
    }

    /**
     * Páginas que sigue el usuario, con datos para mostrar en el dashboard.
     * Incluye nº de materiales (posts) vinculados a cada página.
     */
    public static function getFollowedPages(int $userId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT p.id_pagina, p.titulo, p.slug, up.created_at AS seguida_desde
            FROM usuario_paginas up
            JOIN paginas p ON p.id_pagina = up.id_pagina
            WHERE up.id_usuario = ?
            ORDER BY up.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Nº de páginas que sigue (para el tile de stats).
     */
    public static function countFollowed(int $userId): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM usuario_paginas WHERE id_usuario = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
