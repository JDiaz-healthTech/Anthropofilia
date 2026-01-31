<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';

$security->requireLogin();

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $security->abort(405, 'Método no permitido.');
}

// CSRF sólo desde POST
$security->csrfValidate($_POST['csrf_token'] ?? null);

// ID desde POST
$pagina_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$pagina_id || $pagina_id <= 0) {
    $security->abort(400, 'ID de página inválido.');
}

// Carga + autorización
$stmt = $pdo->prepare('SELECT id_pagina FROM paginas WHERE id_pagina = ?');
$stmt->execute([$pagina_id]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pagina) {
    $security->abort(404, 'Página no encontrada.');
}
$security->requireOwnershipOrRole((int)$pagina['id_usuario'], ['admin']);

// 7) Borrado
try {
    $pdo->beginTransaction();

    // Eliminar las relaciones con etiquetas primero (si existen)
    $stmtTags = $pdo->prepare('DELETE FROM post_etiquetas WHERE id_post = ?');
    $stmtTags->execute([$post_id]);

    // Eliminar el post
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id_post = ?');
    $stmt->execute([$post_id]);

    // Eliminar la imagen física si existe y no es una URL externa
    if (!empty($post['imagen_destacada_url']) &&
        !filter_var($post['imagen_destacada_url'], FILTER_VALIDATE_URL)) {
        $imagen_path = __DIR__ . '/' . $post['imagen_destacada_url'];
        if (file_exists($imagen_path) && is_file($imagen_path)) {
            @unlink($imagen_path);
        }
    }

    $pdo->commit();

    // 8) Redirección con feedback (corregido el nombre del archivo)
    header('Location: dashboard.php?msg=deleted');
    exit();
} catch (\PDOException $e) {
    $pdo->rollBack();
    $security->logEvent('error', 'post_delete_failed', [
        'post_id' => $post_id,
        'error'   => $e->getMessage(),
    ]);
    $security->abort(500, 'Error al eliminar el post.');
}
