<?php
// guardar_pagina.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: crear_pagina.php');
    exit();
}

// CSRF
$security->csrfValidate($_POST['csrf_token'] ?? null);

// Helpers locales
function slugify(string $text): string {
    $text = trim($text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text; // quita acentos
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text) ?? $text;
    $text = trim($text, '-');
    // límite razonable
    return substr($text, 0, 120);
}

/**
 * Genera un slug único consultando la BBDD.
 * Asume índice único en paginas.slug. Si no lo tienes, este método evita colisiones igualmente.
 */
function uniqueSlug(PDO $pdo, string $base): string {
    $slug = $base !== '' ? $base : 'pagina';
    // ¿Existe exacto?
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM paginas WHERE slug = ?');
    $stmt->execute([$slug]);
    if ((int)$stmt->fetchColumn() === 0) return $slug;

    // Busca sufijos -2, -3, ...
    $stmt = $pdo->prepare('SELECT slug FROM paginas WHERE slug = ? OR slug LIKE ?');
    $stmt->execute([$slug, $slug.'-%']);
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $max = 1;
    foreach ($existing as $s) {
        if (preg_match('#^'.preg_quote($slug, '#').'-(\d+)$#', (string)$s, $m)) {
            $n = (int)$m[1];
            if ($n > $max) $max = $n;
        }
    }
    return $slug . '-' . ($max + 1);
}

try {
    // 1) Recoger y validar datos
    $titulo    = trim((string)($_POST['titulo'] ?? ''));
    $slugInput = trim((string)($_POST['slug'] ?? ''));
    $contenido = (string)($_POST['contenido'] ?? '');
    $userId    = (int)$security->userId();

    // Longitudes (ajústalas si quieres)
    if (mb_strlen($titulo) > 150)   $titulo = mb_substr($titulo, 0, 150);
    if (mb_strlen($slugInput) > 150) $slugInput = mb_substr($slugInput, 0, 150);
    if (mb_strlen($contenido) > 200000) $contenido = mb_substr($contenido, 0, 200000);

    if ($titulo === '' || $contenido === '') {
        $_SESSION['form_data'] = $_POST;
        header('Location: crear_pagina.php?status=invalid');
        exit();
    }

    // 2) Normalizar slug (si vacío, derivado del título) y asegurar unicidad
    $baseSlug = slugify($slugInput !== '' ? $slugInput : $titulo);
    $slug = uniqueSlug($pdo, $baseSlug);

    // 3) Sanitizar HTML del contenido (por si acaso)
    $contenidoLimpio = $security->sanitizeHTML($contenido);

    // 4) Insert
    // Nota: Asumo columnas: id_usuario, titulo, slug, contenido.
    // Si tienes más (fecha_creacion, etc.), añádelas aquí.
    $sql = 'INSERT INTO paginas (id_usuario, titulo, slug, contenido) VALUES (?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $titulo, $slug, $contenidoLimpio]);

    header('Location: gestionar_paginas.php?msg=created');
    exit();

} catch (PDOException $e) {
    // Duplicado del slug (23000 en MySQL para unique constraint)
    if ((int)$e->getCode() === 23000) {
        $_SESSION['form_data'] = $_POST;
        header('Location: crear_pagina.php?status=duplicate');
        exit();
    }

    $security->logEvent('error', 'page_create_failed', ['error' => $e->getMessage()]);
    $_SESSION['form_data'] = $_POST;
    header('Location: crear_pagina.php?status=db_error');
    exit();
}
