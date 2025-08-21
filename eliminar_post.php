<?php
// eliminar_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Requiere sesión
$security->requireLogin();

// 2) Forzar método POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $security->abort(405, 'Método no permitido.');
}

// 3) CSRF (solo desde POST)
$security->csrfValidate($_POST['csrf_token'] ?? null);

// 4) ID robusto (desde POST)
$post_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$post_id || $post_id <= 0) {
    $security->abort(400, 'ID de post inválido.');
}

// 5) Cargar el post para verificar existencia y propietario
$stmt = $pdo->prepare('SELECT id_post, id_usuario FROM posts WHERE id_post = ?');
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    $security->abort(404, 'Post no encontrado.');
}

// 6) Autorización: dueño o admin
$security->requireOwnershipOrRole((int)$post['id_usuario'], ['admin']);

// 7) Borrado
try {
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id_post = ?');
    $stmt->execute([$post_id]);

    // 8) Redirección con feedback
    header('Location: gestionar_post.php?msg=deleted');
    exit();
} catch (\PDOException $e) {
    $security->logEvent('error', 'post_delete_failed', [
        'post_id' => $post_id,
        'error'   => $e->getMessage(),
    ]);
    $security->abort(500, 'Error al eliminar el post.');
}
