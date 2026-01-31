<?php
// api/pagina_posts_remove.php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../init.php';

use App\Models\PaginaPost;

$security->requireLogin();
$security->requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$security->csrfValidate($_POST['csrf_token'] ?? '');

$idPagina = filter_input(INPUT_POST, 'id_pagina', FILTER_VALIDATE_INT);
$idPost = filter_input(INPUT_POST, 'id_post', FILTER_VALIDATE_INT);

if (!$idPagina || !$idPost) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

try {
    $paginaPost = new PaginaPost($pdo);
    $success = $paginaPost->removePostFromPagina($idPagina, $idPost);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Post eliminado de la página'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al eliminar el post']);
    }

} catch (Exception $e) {
    $security->logEvent('error', 'pagina_post_remove_failed', [
        'id_pagina' => $idPagina,
        'id_post' => $idPost,
        'error' => $e->getMessage()
    ]);

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
