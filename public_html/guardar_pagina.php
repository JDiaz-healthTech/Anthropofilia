<?php
// guardar_pagina.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Page;

$security->requireLogin();

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: crear_pagina.php');
    exit();
}

// CSRF
$security->requireValidCsrf();
try {
    // 1) Recoger y validar datos
    $titulo    = trim((string)($_POST['titulo'] ?? ''));
    $slugInput = trim((string)($_POST['slug'] ?? ''));
    $contenido = (string)($_POST['contenido'] ?? '');
    $orden     = max(0, min(100, (int)($_POST['orden'] ?? 0)));
    $mostrar_indice = isset($_POST['mostrar_indice']) ? 1 : 0;
    $userId    = (int)$security->userId();

    // Longitudes
    if (mb_strlen($titulo) > 150)   $titulo = mb_substr($titulo, 0, 150);
    if (mb_strlen($slugInput) > 150) $slugInput = mb_substr($slugInput, 0, 150);
    if (mb_strlen($contenido) > 200000) $contenido = mb_substr($contenido, 0, 200000);

    if ($titulo === '' || $contenido === '') {
        $_SESSION['form_pagina'] = $_POST;
        header('Location: crear_pagina.php?status=invalid');
        exit();
    }

    // 2) Normalizar slug y asegurar unicidad via Page model
    $baseSlug = Page::slugify($slugInput !== '' ? $slugInput : $titulo);
    $slug = Page::uniqueSlug($baseSlug);

// 3) Sanitizar HTML del contenido (por si acaso)
    $contenidoLimpio = $security->sanitizeHTML($contenido);

    // 4) Insert via modelo
    Page::create([
        'titulo'          => $titulo,
        'slug'            => $slug,
        'contenido'       => $contenidoLimpio,
        'orden'           => $orden,
        'mostrar_indice'  => $mostrar_indice,
    ]);

    header('Location: gestionar_paginas.php?msg=created');
    exit();

} catch (PDOException $e) {
    // Duplicado del slug (23000 en MySQL para unique constraint)
    if ((int)$e->getCode() === 23000) {
        $_SESSION['form_pagina'] = $_POST;
        header('Location: crear_pagina.php?status=duplicate');
        exit();
    }

    $security->logEvent('error', 'page_create_failed', ['error' => $e->getMessage()]);
    $_SESSION['form_pagina'] = $_POST;
    header('Location: crear_pagina.php?status=db_error');
    exit();
}
