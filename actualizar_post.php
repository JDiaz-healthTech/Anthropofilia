<?php
// actualizar_post.php (versión reforzada)
require_once 'init.php';

// 1) Autenticación
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2) Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Location: gestionar_posts.php");
    exit();
}

// 3) CSRF
$security->csrfValidate($_POST['csrf_token'] ?? '');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4) Datos + validación mínima
    $id_post      = (int)$security->cleanInput($_POST['id_post'] ?? '', 'int');
    $titulo       = trim($security->cleanInput($_POST['titulo'] ?? ''));
    $contenido    = $_POST['contenido'] ?? ''; // HTML permitido; sanitiza al render
    $id_categoria = (int)$security->cleanInput($_POST['id_categoria'] ?? '', 'int');

    // URL opcional (admite NULL si viene vacía)
    $imagen_url_in = $security->cleanInput($_POST['imagen_url'] ?? '');
    $imagen_url    = $imagen_url_in !== '' ? $security->cleanInput($imagen_url_in, 'url') : null;

    if ($id_post <= 0 || $id_categoria <= 0 || $titulo === '' || $contenido === '') {
        http_response_code(400);
        die("Error: id_post, título, contenido e id_categoria son obligatorios.");
    }

    // (Opcional) Comprobar propiedad/permisos si aplica:
    // $isAdmin = $_SESSION['rol'] === 'admin' ?? false;

    // 5) Verificar que el post exista (404 si no)
    $stmt = $pdo->prepare("SELECT id_post, id_usuario FROM posts WHERE id_post = ? LIMIT 1");
    $stmt->execute([$id_post]);
    $postRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$postRow) {
        http_response_code(404);
        die("El post no existe.");
    }

    // (Opcional) si no es admin, verifica autoría:
    // if (!$isAdmin && (int)$postRow['id_usuario'] !== (int)$_SESSION['id_usuario']) {
    //     http_response_code(403);
    //     die("No tienes permiso para editar este post.");
    // }

    // 6) Verificar que la categoría exista (mensaje amable; además usa FK en BD)
    $stmt = $pdo->prepare("SELECT 1 FROM categorias WHERE id_categoria = ? LIMIT 1");
    $stmt->execute([$id_categoria]);
    if (!$stmt->fetchColumn()) {
        http_response_code(400);
        die("La categoría seleccionada no existe.");
    }

    // 7) Update (si tienes columna de timestamp, puedes añadir: , actualizado_en = NOW())
    $sql = "UPDATE posts
            SET titulo = ?, contenido = ?, id_categoria = ?, imagen_destacada_url = ?
            WHERE id_post = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $contenido, $id_categoria, $imagen_url, $id_post]);

    // rowCount puede ser 0 si no hubo cambios; no es error.
    header("Location: gestionar_posts.php?status=updated&id={$id_post}", true, 303); // PRG
    exit();

} catch (PDOException $e) {
    // FK/UNIQUE, etc.
    if ($e->getCode() === '23000') {
        http_response_code(409);
        $security->logEvent('warn', 'post_update_constraint', [
            'post_id' => $id_post,
            'error'   => $e->getMessage()
        ]);
        die("No se pudo actualizar por una restricción de la base de datos.");
    }
    http_response_code(500);
    $security->logEvent('error', 'post_update_failed', [
        'post_id' => $id_post ?? null,
        'error'   => $e->getMessage()
    ]);
    die("Error al actualizar el post. Por favor, inténtalo de nuevo.");
}
