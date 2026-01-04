<?php
// actualizar_pagina.php (versión mejorada y consistente con init + SecurityManager + PDO)
require_once 'init.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Location: gestionar_paginas.php");
    exit();
}

// CSRF
$security->csrfValidate($_POST['csrf_token'] ?? '');

/** Normaliza slug: minúsculas, sin acentos, guiones simples */
function normalize_slug(string $s): string {
    $s = trim($s);
    $s = mb_strtolower($s, 'UTF-8');
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    if ($t === false) { $t = $s; }
    $t = preg_replace('~[^a-z0-9]+~', '-', $t);  // todo lo no-alfa-num → -
    $t = trim($t, '-');
    $t = preg_replace('~-+~', '-', $t);         // colapsa guiones
    return substr($t, 0, 191);                  // margen para índices
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1) Recoger + validar
    $id_pagina = (int)$security->cleanInput($_POST['id_pagina'] ?? '', 'int');
    $titulo    = trim($security->cleanInput($_POST['titulo'] ?? ''));
    $slug_in   = trim($security->cleanInput($_POST['slug'] ?? ''));
    $contenido = $_POST['contenido'] ?? ''; // puede tener HTML; sanitiza al render
    $orden     = max(0, min(100, (int)($_POST['orden'] ?? 0))); // Entre 0-100

    if ($id_pagina <= 0 || $titulo === '' || $slug_in === '' || $contenido === '') {
        http_response_code(400);
        die("Error: Título, slug, contenido e id_pagina son obligatorios.");
    }

    // 2) Normalizar slug y validar patrón
    $slug = normalize_slug($slug_in);
    if ($slug === '') {
        http_response_code(400);
        die("Error: El slug proporcionado no es válido.");
    }

    // 3) Comprobar que la página exista (404 si no)
    $stmt = $pdo->prepare("SELECT id_pagina FROM paginas WHERE id_pagina = ? LIMIT 1");
    $stmt->execute([$id_pagina]);
    if (!$stmt->fetchColumn()) {
        http_response_code(404);
        die("La página no existe.");
    }

    // (Opcional) Verificación de permisos/propiedad aquí si aplica.

    // 4) Evitar slug duplicado (409 si otro registro lo usa)
    // Asegúrate de tener UNIQUE KEY en paginas(slug)
    $stmt = $pdo->prepare("SELECT 1 FROM paginas WHERE slug = ? AND id_pagina <> ? LIMIT 1");
    $stmt->execute([$slug, $id_pagina]);
    if ($stmt->fetchColumn()) {
        http_response_code(409);
        die("El slug ya está en uso por otra página.");
    }
    // 5) Update (marca timestamp si tienes columna)
    $sql = "UPDATE paginas 
            SET titulo = ?, slug = ?, contenido = ?, orden = ?, actualizado_en = NOW()
            WHERE id_pagina = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $slug, $contenido, $orden, $id_pagina]);

    // Si no cambió nada, rowCount puede ser 0; no es error.
    header("Location: gestionar_paginas.php?status=updated&id={$id_pagina}", true, 303); // PRG
    exit();

} catch (PDOException $e) {
    // Unique constraint (por si solo confías en el índice)
    if ($e->getCode() === '23000') { // violación de restricción
        http_response_code(409);
        $security->logEvent('warn', 'page_update_slug_conflict', ['page_id' => $id_pagina, 'slug' => $slug]);
        die("El slug ya existe. Elige otro diferente.");
    }
    http_response_code(500);
    $security->logEvent('error', 'page_update_failed', ['page_id' => $id_pagina, 'error' => $e->getMessage()]);
    die("Error al actualizar la página. Por favor, inténtelo de nuevo.");
}
