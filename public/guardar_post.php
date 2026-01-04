<?php
// guardar_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// 1) Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: crear_post.php');
    exit();
}

// 2) CSRF
$security->csrfValidate($_POST['csrf_token'] ?? null);

// 3) Recoger datos
$titulo        = trim((string)($_POST['titulo'] ?? ''));
$contenido_raw = (string)($_POST['contenido'] ?? '');
$id_categoria  = filter_var($_POST['id_categoria'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$etiquetas_raw = trim((string)($_POST['etiquetas'] ?? ''));
$imagen_url_in = trim((string)($_POST['imagen_url'] ?? ''));   // por si permites URL directa
$id_usuario    = (int)$security->userId();

// 4) Validaciones básicas
if ($titulo === '' || $contenido_raw === '' || $id_categoria <= 0) {
    $_SESSION['form_data'] = $_POST;
    header('Location: crear_post.php?status=invalid');
    exit();
}

// 5) Verificar categoría existe
$catStmt = $pdo->prepare('SELECT 1 FROM categorias WHERE id_categoria = ?');
$catStmt->execute([$id_categoria]);
if (!$catStmt->fetchColumn()) {
    $_SESSION['form_data'] = $_POST;
    header('Location: crear_post.php?status=invalid_category');
    exit();
}

// 6) Sanitizar contenido HTML (defensa en profundidad)
$contenido = $security->sanitizeHTML($contenido_raw);

// 7) Subida de imagen (archivo o URL)
$imagen_path = null;

// A) archivo subido
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    try {
        $security->validateUpload($_FILES['imagen']);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($_FILES['imagen']['tmp_name']);
        $ext   = $security->extensionFromMime((string)$mime);

        $base_dir_fs = __DIR__ . '/uploads/' . date('Y/m/') ;
        $base_dir_url = 'uploads/' . date('Y/m/') ;
        if (!is_dir($base_dir_fs) && !mkdir($base_dir_fs, 0755, true) && !is_dir($base_dir_fs)) {
            throw new \RuntimeException('No se pudo crear el directorio de subidas.');
        }

        $nombre = bin2hex(random_bytes(16)) . $ext;
        $dest_fs  = $base_dir_fs . $nombre;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest_fs)) {
            throw new \RuntimeException('Error al mover el archivo subido.');
        }

        $imagen_path = $base_dir_url . $nombre; // ruta relativa servible
    } catch (\Throwable $e) {
        $_SESSION['form_data'] = $_POST;
        header('Location: crear_post.php?status=upload_error');
        exit();
    }
}
// B) URL manual
elseif ($imagen_url_in !== '' && filter_var($imagen_url_in, FILTER_VALIDATE_URL)) {
    $imagen_path = $imagen_url_in;
}

// 8) Etiquetas (normalizar a minúsculas, únicas)
$tags = array_values(array_unique(array_filter(array_map(
    fn($t) => trim(mb_strtolower($t, 'UTF-8')),
    preg_split('/,/', $etiquetas_raw) ?: []
), fn($t) => $t !== '')));
$tags_for_column = implode(', ', $tags); // compat con columna posts.etiquetas

try {
    $pdo->beginTransaction();

    // 9) Insertar post principal
    $sqlPost = 'INSERT INTO posts
        (titulo, contenido, id_categoria, imagen_destacada_url, id_usuario, etiquetas)
        VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $pdo->prepare($sqlPost);
    $stmt->execute([$titulo, $contenido, $id_categoria, $imagen_path, $id_usuario, $tags_for_column]);
    $id_post = (int)$pdo->lastInsertId();

    // 10) Upsert de etiquetas + vinculación
    if ($tags) {
        // upsert: si existe, devuelve su id via LAST_INSERT_ID()
        $stmtUpsert = $pdo->prepare(
            "INSERT INTO etiquetas (nombre_etiqueta)
             VALUES (?)
             ON DUPLICATE KEY UPDATE id_etiqueta = LAST_INSERT_ID(id_etiqueta)"
        );
        $stmtLink = $pdo->prepare(
            "INSERT IGNORE INTO post_etiquetas (id_post, id_etiqueta) VALUES (?, ?)"
        );

        foreach ($tags as $tag) {
            $stmtUpsert->execute([$tag]);
            $id_tag = (int)$pdo->lastInsertId();
            if ($id_tag > 0) {
                $stmtLink->execute([$id_post, $id_tag]);
            }
        }
    }

    $pdo->commit();
    header('Location: dashboard.php?msg=created');
    exit();

} catch (\PDOException $e) {
    $pdo->rollBack();
    $security->logEvent('error', 'post_create_failed', ['error' => $e->getMessage()]);
    $_SESSION['form_data'] = $_POST;
    header('Location: crear_post.php?status=db_error');
    exit();
}
