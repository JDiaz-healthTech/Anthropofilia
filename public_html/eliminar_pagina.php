<?php
// eliminar_pagina.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $security->abort(405, 'Método no permitido.');
}

// CSRF
$security->requireValidCsrf();

// ID desde POST
$pagina_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$pagina_id || $pagina_id <= 0) {
    $security->abort(400, 'ID de página inválido.');
}

// Verificar que la página existe
$stmt = $pdo->prepare('SELECT id_pagina, titulo FROM paginas WHERE id_pagina = ? LIMIT 1');
$stmt->execute([$pagina_id]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pagina) {
    $security->abort(404, 'Página no encontrada.');
}

// Borrado
try {
    $pdo->beginTransaction();

    // Eliminar relaciones con posts (tabla pagina_posts)
    $stmtRel = $pdo->prepare('DELETE FROM pagina_posts WHERE id_pagina = ?');
    $stmtRel->execute([$pagina_id]);

    // Eliminar la página
    $stmt = $pdo->prepare('DELETE FROM paginas WHERE id_pagina = ?');
    $stmt->execute([$pagina_id]);

    $pdo->commit();

    // Log del evento
    $security->logEvent('info', 'page_deleted', [
        'page_id' => $pagina_id,
        'titulo' => $pagina['titulo']
    ]);

    header('Location: gestionar_paginas.php?msg=deleted');
    exit();

} catch (\PDOException $e) {
    $pdo->rollBack();
    $security->logEvent('error', 'page_delete_failed', [
        'page_id' => $pagina_id,
        'error' => $e->getMessage(),
    ]);
    $security->abort(500, 'Error al eliminar la página.');
}
