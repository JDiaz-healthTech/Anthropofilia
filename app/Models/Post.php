<?php
namespace App\Models;

use PDO;

class Post {

    /**
     * Obtener todos los posts ordenados por fecha (Para el Index)
     */
    public static function getAll(): array {
        $db = Database::getConnection();
        // Seleccionamos explícitamente las columnas necesarias
        $stmt = $db->query(
            "SELECT id_post, slug, titulo, contenido, imagen_destacada_url,
                    fecha_publicacion, id_usuario, id_categoria, actualizado_en
             FROM posts
             ORDER BY fecha_publicacion DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Buscar un post por su ID (Para ver el post individual)
     */
    public static function find(int $id) {
        $db = Database::getConnection();
        // Seleccionamos explícitamente las columnas necesarias
        $stmt = $db->prepare(
            "SELECT id_post, slug, titulo, contenido, imagen_destacada_url,
                    fecha_publicacion, id_usuario, id_categoria, actualizado_en
             FROM posts
             WHERE id_post = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Buscar posts por término de búsqueda
     *
     * @param string $query Término a buscar
     * @return array Lista de posts que coinciden
     */
    public static function search(string $query): array {
        $db = Database::getConnection();

        // Escapar comodines SQL para prevenir búsquedas incorrectas
        $escapedQuery = strtr($query, [
            '\\' => '\\\\',
            '%'  => '\\%',
            '_'  => '\\_',
        ]);

        $searchTerm = "%{$escapedQuery}%";

        $stmt = $db->prepare(
            "SELECT id_post, slug, titulo, contenido, imagen_destacada_url, fecha_publicacion, id_categoria
            FROM posts
            WHERE (titulo LIKE :q ESCAPE '\\' OR contenido LIKE :q ESCAPE '\\')
            ORDER BY fecha_publicacion DESC, id_post DESC"
        );

        $stmt->execute([':q' => $searchTerm]);
        return $stmt->fetchAll();
    }

    /**
     * Contar total de posts (Para calcular la paginación)
     */
    public static function countAll(): int {
        $db = Database::getConnection();
        return (int) $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    }

    /**
     * Obtener posts paginados (sustituye al LIMIT/OFFSET manual)
     */
public static function getPaginated(int $limit, int $offset): array {
    $db = Database::getConnection();
    $stmt = $db->prepare(
        "SELECT p.id_post, p.slug, p.titulo, p.contenido, p.imagen_destacada_url,
                p.fecha_publicacion, c.nombre_categoria, c.slug AS categoria_slug
         FROM posts p
         LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
         ORDER BY p.fecha_publicacion DESC, p.id_post DESC
         LIMIT :limit OFFSET :offset"
    );

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Obtener un post con detalles adicionales (categoría y etiquetas)
     */
    public static function getWithDetails(int|null $id, string|null $slug): ?array {
        $db = Database::getConnection();

        // 1. Determinar si buscamos por Slug o por ID
        $useSlug = ($slug !== null && $slug !== '');

        // 2. Consulta principal (JOIN con Categorías)
        $sql = "SELECT p.id_post, p.slug, p.titulo, p.contenido, p.imagen_destacada_url,
                       p.fecha_publicacion, p.id_categoria,
                       c.nombre_categoria, c.slug as categoria_slug
                FROM posts p
                LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
                WHERE " . ($useSlug ? "p.slug = ?" : "p.id_post = ?") . "
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute([$useSlug ? $slug : $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no existe, devolvemos null inmediatamente
        if (!$post) {
            return null;
        }

        // 3. Consulta secundaria (Etiquetas)
        // Obtenemos las etiquetas asociadas en una segunda consulta eficiente
        $sqlTags = "SELECT e.nombre_etiqueta
                    FROM post_etiquetas pe
                    INNER JOIN etiquetas e ON e.id_etiqueta = pe.id_etiqueta
                    WHERE pe.id_post = ?
                    ORDER BY e.nombre_etiqueta ASC";

        $stmtTags = $db->prepare($sqlTags);
        $stmtTags->execute([$post['id_post']]);

        // Añadimos las tags al array del post
        $post['tags'] = $stmtTags->fetchAll(PDO::FETCH_COLUMN) ?: [];

        return $post;
    }

        /**
     * Buscar posts con paginación
     */
    public static function searchPaginated(string $query, int $limit = 10, int $offset = 0): array {
        $db = Database::getConnection();
        $searchTerm = '%' . addcslashes($query, '%_\\') . '%';

        $stmt = $db->prepare(
            "SELECT id_post, slug, titulo, contenido, fecha_publicacion
            FROM posts
            WHERE titulo LIKE :q1 OR contenido LIKE :q2
            ORDER BY fecha_publicacion DESC, id_post DESC
            LIMIT :limit OFFSET :offset"
        );

        $stmt->bindValue(':q1', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':q2', $searchTerm, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Contar resultados de búsqueda
     */
    public static function countSearch(string $query): int {
        $db = Database::getConnection();
        $searchTerm = '%' . addcslashes($query, '%_\\') . '%';

        $stmt = $db->prepare(
            "SELECT COUNT(*)
            FROM posts
            WHERE titulo LIKE :q1 OR contenido LIKE :q2"
        );

        $stmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm]);
        return (int) $stmt->fetchColumn();
    }


}
