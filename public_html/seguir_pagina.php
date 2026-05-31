<?php
// seguir_pagina.php — alternar seguimiento de una página (seguir / dejar de seguir)
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Page;
use App\Models\UserPage;

$security->requireLogin();

if (!$security->hasRole('usuario')) {
    $security->abort(403, 'Esta función es solo para usuarios registrados.');
}

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . url('index.php'));
    exit();
}

// CSRF
$security->requireValidCsrf();

// Validar página
$idPagina = filter_input(INPUT_POST, 'id_pagina', FILTER_VALIDATE_INT);
if (!$idPagina) {
    header('Location: ' . url('index.php'));
    exit();
}

$pagina = Page::findById($idPagina);
if (!$pagina) {
    header('Location: ' . url('index.php'));
    exit();
}

$userId = (int) $security->userId();

// Toggle: si la sigue, dejar de seguir; si no, seguir
if (UserPage::isFollowing($userId, $idPagina)) {
    UserPage::unfollow($userId, $idPagina);
} else {
    UserPage::follow($userId, $idPagina);
}

// Volver a la página (redirect seguro construido desde el slug, no desde el referer)
header('Location: ' . url('pagina.php?slug=' . urlencode($pagina['slug'])));
exit();
