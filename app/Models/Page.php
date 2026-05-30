<?php
// app/Models/Page.php
declare(strict_types=1);

namespace App\Models;

use PDO;

class Page
{
    // ──────────────────────────────────────────────
    // LECTURA
    // ──────────────────────────────────────────────

    /**
     * Contar total de páginas
     */
    public static function countAll(): int
    {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM paginas")->fetchColumn();
    }

    /**
     * Buscar página por ID (para editar_pagina.php, eliminar_pagina.php)
     */
    public static function findById(int $id): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT id_pagina, titulo, slug, contenido, orden, mostrar_indice,
                    fecha_creacion, actualizado_en
             FROM paginas
             WHERE id_pagina = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Buscar página por slug (para pagina.php — vista pública)
     */
    public static function findBySlug(string $slug): ?array
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT id_pagina, titulo, slug, contenido, orden, mostrar_indice,
                    fecha_creacion, actualizado_en
             FROM paginas
             WHERE slug = ?
             LIMIT 1"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Obtener todas las páginas ordenadas (para gestionar_paginas.php o listados completos)
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query(
            "SELECT id_pagina, titulo, slug, orden, fecha_creacion, actualizado_en
             FROM paginas
             ORDER BY orden ASC, titulo ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Páginas para el menú de navegación (solo título, slug y orden)
     * Ordenadas por campo `orden` para que el admin controle la posición.
     */
    public static function getForMenu(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query(
            "SELECT id_pagina, titulo, slug
             FROM paginas
             ORDER BY orden ASC, titulo ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ──────────────────────────────────────────────
    // SLUG
    // ──────────────────────────────────────────────

    /**
     * Genera un slug limpio a partir de texto libre.
     * Centraliza la lógica que estaba duplicada en guardar_pagina.php y actualizar_pagina.php.
     *
     * - Transliterar acentos (á → a, ñ → n)
     * - Minúsculas
     * - Solo a-z, 0-9 y guiones
     * - Máximo 150 caracteres
     */
    public static function slugify(string $text): string
    {
    return \slugify($text);
    }

    /**
     * Genera un slug único verificando contra la BBDD.
     * Si 'mi-pagina' existe, prueba 'mi-pagina-2', 'mi-pagina-3', etc.
     *
     * @param string   $base       Slug base ya slugificado
     * @param int|null $excludeId  ID de página a excluir (para updates — no colisionar consigo misma)
     */
    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $db   = Database::getConnection();
        $slug = $base !== '' ? $base : 'pagina';

        // ¿Existe exacto? (excluyendo la propia página si es update)
        $sql    = "SELECT COUNT(*) FROM paginas WHERE slug = ?" . ($excludeId ? " AND id_pagina <> ?" : "");
        $stmt   = $db->prepare($sql);
        $params = $excludeId ? [$slug, $excludeId] : [$slug];
        $stmt->execute($params);

        if ((int) $stmt->fetchColumn() === 0) {
            return $slug;
        }

        // Buscar todos los slugs que empiecen igual para calcular siguiente sufijo
        $sql  = "SELECT slug FROM paginas WHERE (slug = ? OR slug LIKE ?)"
              . ($excludeId ? " AND id_pagina <> ?" : "");
        $stmt = $db->prepare($sql);
        $params = $excludeId
            ? [$slug, $slug . '-%', $excludeId]
            : [$slug, $slug . '-%'];
        $stmt->execute($params);
        $existing = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $max = 1;
        foreach ($existing as $s) {
            if (preg_match('#^' . preg_quote($slug, '#') . '-(\d+)$#', (string) $s, $m)) {
                $n = (int) $m[1];
                if ($n > $max) {
                    $max = $n;
                }
            }
        }

        return $slug . '-' . ($max + 1);
    }

    // ──────────────────────────────────────────────
    // ESCRITURA
    // ──────────────────────────────────────────────

    /**
     * Crear página nueva. Devuelve el ID insertado.
     *
     * @param array $data Claves: titulo, slug, contenido, orden, mostrar_indice
     */
    public static function create(array $data): int
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT INTO paginas (titulo, slug, contenido, orden, mostrar_indice, fecha_creacion)
             VALUES (?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['contenido'],
            $data['orden'] ?? 0,
            $data['mostrar_indice'] ?? 0,
        ]);
        return (int) $db->lastInsertId();
    }

    /**
     * Actualizar página existente.
     *
     * @param int   $id   ID de la página
     * @param array $data Claves: titulo, slug, contenido, orden, mostrar_indice
     */
    public static function update(int $id, array $data): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare(
            "UPDATE paginas
             SET titulo = ?, slug = ?, contenido = ?, orden = ?, mostrar_indice = ?, actualizado_en = NOW()
             WHERE id_pagina = ?"
        );
        return $stmt->execute([
            $data['titulo'],
            $data['slug'],
            $data['contenido'],
            $data['orden'] ?? 0,
            $data['mostrar_indice'] ?? 0,
            $id,
        ]);
    }

    /**
     * Eliminar página por ID.
     * La FK CASCADE en pagina_posts limpia las relaciones automáticamente.
     */
    public static function delete(int $id): bool
    {
        $db   = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM paginas WHERE id_pagina = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Verificar si un slug ya está en uso por otra página.
     *
     * @param string   $slug      Slug a comprobar
     * @param int|null $excludeId ID a excluir (para updates)
     */
    public static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $db  = Database::getConnection();
        $sql = "SELECT COUNT(*) FROM paginas WHERE slug = ?"
             . ($excludeId ? " AND id_pagina <> ?" : "");
        $stmt = $db->prepare($sql);
        $params = $excludeId ? [$slug, $excludeId] : [$slug];
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
