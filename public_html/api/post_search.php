<?php
// api/post_search.php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../init.php';

use App\Models\PaginaPost;

$security->requireLogin();
$security->requireRole(['administrador', 'autor']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$query = trim($_GET['q'] ?? '');
$idPagina = filter_input(INPUT_GET, 'id_pagina', FILTER_VALIDATE_INT);
$idCategoria = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);
$limit = 20;

try {
    // Construir query
    $where = ['1=1'];
    $params = [];

    // Búsqueda por texto
    if ($query !== '') {
        $where[] = '(p.titulo LIKE :query OR p.contenido LIKE :query)';
        $params[':query'] = '%' . $query . '%';
    }

    // Filtro por categoría
    if ($idCategoria) {
        $where[] = 'p.id_categoria = :categoria';
        $params[':categoria'] = $idCategoria;
    }

    $whereSql = implode(' AND ', $where);

    // Query principal
    $sql = "SELECT
                p.id_post,
                p.titulo,
                p.slug,
                p.fecha_publicacion,
                c.nombre_categoria,
                c.id_categoria
            FROM posts p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            WHERE $whereSql
            ORDER BY p.fecha_publicacion DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si se especificó id_pagina, marcar cuáles ya están en la página
    if ($idPagina) {
        $paginaPost = new PaginaPost($pdo);
        foreach ($posts as &$post) {
            $post['en_pagina'] = $paginaPost->postExistsInPagina($idPagina, (int)$post['id_post']);

            // Obtener páginas donde aparece
            $paginas = $paginaPost->getPaginasWithPost((int)$post['id_post']);
            $post['paginas'] = array_map(function($p) {
                return $p['titulo'];
            }, $paginas);
        }
    }

    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'total' => count($posts)
    ]);

} catch (Exception $e) {
    $security->logEvent('error', 'post_search_failed', [
        'query' => $query,
        'error' => $e->getMessage()
    ]);

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error en la búsqueda']);
}
