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
$security->requireValidCsrf();

// 3) Recoger datos
$titulo        = trim((string)($_POST['titulo'] ?? ''));
$contenido_raw = (string)($_POST['contenido'] ?? '');
$id_categoria  = filter_var($_POST['id_categoria'] ?? null, FILTER_VALIDATE_INT) ?: 0;
$etiquetas_raw = trim((string)($_POST['etiquetas'] ?? ''));
$imagen_url_in = trim((string)($_POST['imagen_url'] ?? ''));   // por si permites URL directa
$id_usuario    = (int)$security->userId();

// 4) Validaciones básicas
if ($titulo === '' || $contenido_raw === '' || $id_categoria <= 0) {
    $_SESSION['form_post'] = $_POST;
    header('Location: crear_post.php?status=invalid');
    exit();
}

// 5) Verificar categoría existe
$catStmt = $pdo->prepare('SELECT 1 FROM categorias WHERE id_categoria = ?');
$catStmt->execute([$id_categoria]);
if (!$catStmt->fetchColumn()) {
    $_SESSION['form_post'] = $_POST;
    header('Location: crear_post.php?status=invalid_category');
    exit();
}

// 6) Sanitizar contenido HTML (defensa en profundidad)
$contenido = $security->sanitizeHTML($contenido_raw);

// 7) Subida de imagen (archivo o URL)
$imagen_path = null;

// A) archivo subido
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Verificar si hubo error en la subida
    if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo supera el tamaño máximo permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo supera el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida'
        ];

        $_SESSION['form_post'] = $_POST;
        $_SESSION['upload_error'] = $errorMessages[$_FILES['imagen']['error']] ?? 'Error desconocido al subir la imagen';
        header('Location: crear_post.php?status=upload_error');
        exit();
    }

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
        $_SESSION['form_post'] = $_POST;
        $_SESSION['upload_error'] = $e->getMessage();
        error_log("Error al subir imagen: " . $e->getMessage());
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

    // 1) Generar slug automático desde el título
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo), '-'));

    // Si el slug queda vacío o es muy corto, generar uno único
    if (strlen($slug) < 3) {
        $slug = 'post-' . time();
    }

    // Verificar unicidad del slug (opcional pero recomendado)
    $slugOriginal = $slug;
    $contador = 1;
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");

    while (true) {
        $stmtCheck->execute([$slug]);
        if ($stmtCheck->fetchColumn() == 0) {
            break; // Slug disponible
        }
        // Slug duplicado, añadir contador
        $slug = $slugOriginal . '-' . $contador;
        $contador++;
    }

    // 2) Insertar post principal CON slug
    $sqlPost = 'INSERT INTO posts
        (titulo, slug, contenido, id_categoria, imagen_destacada_url, id_usuario)
        VALUES (?, ?, ?, ?, ?, ?)';

    $stmt = $pdo->prepare($sqlPost);
    $stmt->execute([$titulo, $slug, $contenido, $id_categoria, $imagen_path, $id_usuario]);
    $id_post = (int)$pdo->lastInsertId();

    // 3) Upsert de etiquetas + vinculación
    if ($tags) {
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

        // Limpiar datos del formulario
        unset($_SESSION['form_post']);
        unset($_SESSION['upload_error']);

        header('Location: dashboard.php?msg=created');
        exit();

    } catch (\PDOException $e) {
        $pdo->rollBack();

        // Si hay una imagen subida y falla la BD, eliminarla
        if ($imagen_path && !filter_var($imagen_path, FILTER_VALIDATE_URL)) {
            $imagen_fisica = __DIR__ . '/' . $imagen_path;
            if (file_exists($imagen_fisica)) {
                @unlink($imagen_fisica);
            }
        }

        $security->logEvent('error', 'post_create_failed', ['error' => $e->getMessage()]);
        $_SESSION['form_post'] = $_POST;
        $_SESSION['db_error'] = $e->getMessage();
        error_log("Error al crear post: " . $e->getMessage());
        header('Location: crear_post.php?status=db_error');
        exit();
    }
