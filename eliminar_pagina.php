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
$stmt = $pdo->prepare('SELECT id_pagina, id_usuario FROM paginas WHERE id_pagina = ?');
$stmt->execute([$pagina_id]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pagina) {
    $security->abort(404, 'Página no encontrada.');
}
$security->requireOwnershipOrRole((int)$pagina['id_usuario'], ['admin']);

// Borrado
try {
    $stmt = $pdo->prepare('DELETE FROM paginas WHERE id_pagina = ?');
    $stmt->execute([$pagina_id]);

    header('Location: gestionar_paginas.php?msg=deleted');
    exit();
} catch (\PDOException $e) {
    $security->logEvent('error', 'page_delete_failed', [
        'page_id' => $pagina_id,
        'error'   => $e->getMessage(),
    ]);
    $security->abort(500, 'Error al eliminar la página.');
}
