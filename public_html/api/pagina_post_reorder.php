<?php
// api/pagina_posts_reorder.php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../init.php';

use App\Models\PaginaPost;

$security->requireLogin();
$security->requireRole(['administrador', 'autor']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$security->requireValidCsrf();

$idPagina = filter_input(INPUT_POST, 'id_pagina', FILTER_VALIDATE_INT);
$ordenJson = $_POST['orden'] ?? '';

if (!$idPagina || !$ordenJson) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

// Decodificar JSON con el nuevo orden
// Formato esperado: [{"id_post":1,"orden":10}, {"id_post":2,"orden":20}, ...]
$ordenNuevo = json_decode($ordenJson, true);

if (!is_array($ordenNuevo)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de orden inválido']);
    exit;
}

try {
    $paginaPost = new PaginaPost($pdo);
    $success = $paginaPost->reorderPosts($idPagina, $ordenNuevo);

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Orden actualizado correctamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al reordenar']);
    }

} catch (Exception $e) {
    $security->logEvent('error', 'pagina_posts_reorder_failed', [
        'id_pagina' => $idPagina,
        'error' => $e->getMessage()
    ]);

    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
