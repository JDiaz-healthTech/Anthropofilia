<?php
// api/pagina_posts_add.php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../init.php';

use App\Models\PaginaPost;

// Solo admins pueden gestionar páginas
$security->requireLogin();
$security->requireRole(['administrador', 'autor']);

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// CSRF
$security->csrfValidate($_POST['csrf_token'] ?? '');

// Validar datos
$idPagina = filter_input(INPUT_POST, 'id_pagina', FILTER_VALIDATE_INT);
$idPost = filter_input(INPUT_POST, 'id_post', FILTER_VALIDATE_INT);
$orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT) ?: 0;
$tipo = $_POST['tipo'] ?? 'card';

if (!$idPagina || !$idPost) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

if (!in_array($tipo, ['embebido', 'card'])) {
    $tipo = 'card';
}

try {
    $paginaPost = new PaginaPost($pdo);

    // Verificar si ya existe
    if ($paginaPost->postExistsInPagina($idPagina, $idPost)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Este post ya está en la página']);
        exit;
    }

    // Añadir
    $success = $paginaPost->addPostToPagina($idPagina, $idPost, $orden, $tipo);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Post añadido correctamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al añadir el post']);
    }

} catch (Exception $e) {
    $security->logEvent('error', 'pagina_post_add_failed', [
        'id_pagina' => $idPagina,
        'id_post' => $idPost,
        'error' => $e->getMessage()
    ]);

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
