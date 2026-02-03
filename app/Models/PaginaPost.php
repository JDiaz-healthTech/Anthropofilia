<?php
// app/Models/PaginaPost.php
declare(strict_types=1);

namespace App\Models;

use PDO;

class PaginaPost
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Añadir un post a una página
     */
    public function addPostToPagina(int $idPagina, int $idPost, int $orden = 0, string $tipo = 'card'): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO pagina_posts (id_pagina, id_post, orden, tipo_visualizacion)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE orden = ?, tipo_visualizacion = ?'
            );
            return $stmt->execute([$idPagina, $idPost, $orden, $tipo, $orden, $tipo]);
        } catch (\PDOException $e) {
            error_log("Error añadiendo post a página: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Quitar un post de una página
     */
    public function removePostFromPagina(int $idPagina, int $idPost): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM pagina_posts WHERE id_pagina = ? AND id_post = ?');
            return $stmt->execute([$idPagina, $idPost]);
        } catch (\PDOException $e) {
            error_log("Error quitando post de página: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los posts de una página (ordenados)
     */
    public function getPostsInPagina(int $idPagina): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                pp.id,
                pp.id_post,
                pp.orden,
                pp.tipo_visualizacion,
                p.titulo,
                p.slug,
                p.contenido,
                p.imagen_destacada_url,
                p.fecha_publicacion,
                c.nombre_categoria
             FROM pagina_posts pp
             INNER JOIN posts p ON pp.id_post = p.id_post
             LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
             WHERE pp.id_pagina = ?
             ORDER BY pp.orden ASC, pp.created_at ASC'
        );
        $stmt->execute([$idPagina]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todas las páginas donde aparece un post
     */
    public function getPaginasWithPost(int $idPost): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                pg.id_pagina,
                pg.titulo,
                pg.slug,
                pp.tipo_visualizacion
             FROM pagina_posts pp
             INNER JOIN paginas pg ON pp.id_pagina = pg.id_pagina
             WHERE pp.id_post = ?
             ORDER BY pg.titulo ASC'
        );
        $stmt->execute([$idPost]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reordenar posts en una página
     * @param array $ordenNuevo [['id_post' => 1, 'orden' => 10], ['id_post' => 2, 'orden' => 20], ...]
     */
    public function reorderPosts(int $idPagina, array $ordenNuevo): bool
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare('UPDATE pagina_posts SET orden = ? WHERE id_pagina = ? AND id_post = ?');

            foreach ($ordenNuevo as $item) {
                $stmt->execute([
                    $item['orden'],
                    $idPagina,
                    $item['id_post']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error reordenando posts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar tipo de visualización de un post en una página
     */
    public function cambiarTipoVisualizacion(int $idPagina, int $idPost, string $tipo): bool
    {
        if (!in_array($tipo, ['embebido', 'card'])) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE pagina_posts SET tipo_visualizacion = ? WHERE id_pagina = ? AND id_post = ?'
            );
            return $stmt->execute([$tipo, $idPagina, $idPost]);
        } catch (\PDOException $e) {
            error_log("Error cambiando tipo de visualización: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Contar cuántas páginas contienen un post
     */
    public function countPaginasWithPost(int $idPost): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM pagina_posts WHERE id_post = ?');
        $stmt->execute([$idPost]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Verificar si un post ya está en una página
     */
    public function postExistsInPagina(int $idPagina, int $idPost): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM pagina_posts WHERE id_pagina = ? AND id_post = ? LIMIT 1');
        $stmt->execute([$idPagina, $idPost]);
        return (bool)$stmt->fetchColumn();
    }
}
