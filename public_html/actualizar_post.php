<?php
// actualizar_post.php
declare(strict_types=1);

require_once 'init.php';

// 1) Autenticación
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2) Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header("Location: dashboard.php");
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

    if ($id_post <= 0 || $id_categoria <= 0 || $titulo === '' || $contenido === '') {
        http_response_code(400);
        die("Error: id_post, título, contenido e id_categoria son obligatorios.");
    }

    // 5) Verificar que el post exista y cargar imagen actual
    $stmt = $pdo->prepare("SELECT id_post, id_usuario, imagen_destacada_url FROM posts WHERE id_post = ? LIMIT 1");
    $stmt->execute([$id_post]);
    $postRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$postRow) {
        http_response_code(404);
        die("El post no existe.");
    }

    // Autorización
    $security->requireOwnershipOrRole((int)$postRow['id_usuario'], ['admin']);

    // 6) Verificar que la categoría exista
    $stmt = $pdo->prepare("SELECT 1 FROM categorias WHERE id_categoria = ? LIMIT 1");
    $stmt->execute([$id_categoria]);
    if (!$stmt->fetchColumn()) {
        http_response_code(400);
        die("La categoría seleccionada no existe.");
    }

    // 7) Manejo de imagen
    $imagen_url = $postRow['imagen_destacada_url']; // Mantener la actual por defecto

    // A) Si se subió un nuevo archivo
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        try {
            $security->validateUpload($_FILES['imagen']);

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($_FILES['imagen']['tmp_name']);
            $ext   = $security->extensionFromMime((string)$mime);

            $base_dir_fs = __DIR__ . '/uploads/' . date('Y/m/');
            $base_dir_url = 'uploads/' . date('Y/m/');

            if (!is_dir($base_dir_fs)) {
                mkdir($base_dir_fs, 0755, true);
            }

            $nombre = bin2hex(random_bytes(16)) . $ext;
            $dest_fs  = $base_dir_fs . $nombre;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest_fs)) {
                // Eliminar imagen anterior si existe y no es URL externa
                if (!empty($postRow['imagen_destacada_url']) &&
                    !filter_var($postRow['imagen_destacada_url'], FILTER_VALIDATE_URL)) {
                    $old_image = __DIR__ . '/' . $postRow['imagen_destacada_url'];
                    if (file_exists($old_image)) {
                        @unlink($old_image);
                    }
                }

                $imagen_url = $base_dir_url . $nombre;
            }
        } catch (\Throwable $e) {
            error_log("Error al subir imagen en actualizar_post: " . $e->getMessage());
            // Continuar con la imagen anterior
        }
    }
    // B) Si se proporcionó una URL nueva (y no se subió archivo)
        elseif (isset($_POST['imagen_url'])) {
            $imagen_url_in = trim($_POST['imagen_url']);
            if ($imagen_url_in === '') {
                // Campo vaciado explícitamente → borrar imagen
                $imagen_url = null;
            } elseif (filter_var($imagen_url_in, FILTER_VALIDATE_URL)) {
                $imagen_url = $imagen_url_in;
            }
        }

        // DEBUG TEMPORAL — borrar después
    error_log("DEBUG actualizar_post: imagen_url = " . var_export($imagen_url, true));
    error_log("DEBUG actualizar_post: POST[imagen_url] = " . var_export($_POST['imagen_url'] ?? 'NO EXISTE', true));

    // 8) Update
    $sql = "UPDATE posts
            SET titulo = ?, contenido = ?, id_categoria = ?, imagen_destacada_url = ?, actualizado_en = NOW()
            WHERE id_post = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $contenido, $id_categoria, $imagen_url, $id_post]);

    header("Location: dashboard.php?msg=updated", true, 303);
    exit();

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        $security->logEvent('warn', 'post_update_constraint', [
            'post_id' => $id_post ?? 0,
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
