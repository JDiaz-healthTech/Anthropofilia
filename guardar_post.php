<?php


// 1) INICIALIZACIÓN Y BARRERA DE SEGURIDAD
require_once 'init.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); } // asegura sesión

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401); //  código HTTP adecuado
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // método no permitido
    header("Location: crear_post.php");
    exit();
}

$security->csrfValidate($_POST['csrf_token'] ?? ''); // CSRF centralizado

// 2) RECOGER DATOS Y VALIDAR
$titulo          = $security->cleanInput($_POST['titulo']);
$contenido       = $_POST['contenido']; // sanitizar al mostrar (p. ej. HTML Purifier)
$id_categoria    = (int)$security->cleanInput($_POST['id_categoria'], 'int');
$id_usuario      = $_SESSION['id_usuario'];
$etiquetas_input = $security->cleanInput($_POST['etiquetas'] ?? '');

// valida presencia de datos
if ($titulo === '' || $contenido === '' || $id_categoria <= 0) {
    http_response_code(400);
    die("Error: El título, contenido y categoría son obligatorios.");
}

// verifica que la categoría exista
$catStmt = $pdo->prepare("SELECT 1 FROM categorias WHERE id_categoria = ?");
$catStmt->execute([$id_categoria]);
if (!$catStmt->fetchColumn()) {
    http_response_code(400);
    die("Error: La categoría seleccionada no existe.");
}

// 3) PROCESAR LA IMAGEN SUBIDA (nombre 100% opaco + extensión por MIME)
$imagen_path = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    try {
        $security->validateUpload($_FILES['imagen']); // tamaños, mimetypes, etc.

        // extensión por MIME y nombre aleatorio que no revela el original
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($_FILES['imagen']['tmp_name']);
        $ext   = $security->extensionFromMime($mime); // p.ej. ".jpg", ".png", ".gif"

        // Nombre totalmente opaco (no usa el original)
        $nombre_archivo_seguro = bin2hex(random_bytes(16)) . $ext;

        // (Opcional) subcarpetas por fecha para evitar miles de ficheros en un dir
        $base_uploads = 'uploads/' . date('Y/m/') ;
        if (!is_dir($base_uploads) && !mkdir($base_uploads, 0755, true) && !is_dir($base_uploads)) {
            throw new Exception("No se pudo crear el directorio de subidas.");
        }

        $ruta_completa = $base_uploads . $nombre_archivo_seguro;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
            throw new Exception("Error al mover el archivo subido.");
        }

        // (Opcional para futuro TODO): re-encode con GD/Imagick para eliminar metadatos
        $imagen_path = $ruta_completa;
    } catch (Exception $e) {
        http_response_code(400);
        die("Error en la subida de imagen: " . $e->getMessage());
    }
}

// Helpers de normalización para autoetiquetado 
function norm_ascii_lower(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    return $t !== false ? $t : $s;
}

// 4) TRANSACCIÓN Y GUARDADO CON PDO
try {
    $pdo->beginTransaction();

    // Asegura modo excepciones (por si init.php no lo hizo)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert del post
    $stmt_post = $pdo->prepare(
        "INSERT INTO posts (titulo, contenido, id_categoria, imagen_destacada_url, id_usuario)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_post->execute([$titulo, $contenido, $id_categoria, $imagen_path, $id_usuario]);
    $id_post = (int)$pdo->lastInsertId();

    //  preparo índices/contraints (ver SQL al final) y usa upsert atómico para etiquetas
    $stmt_upsert_tag = $pdo->prepare(
        "INSERT INTO etiquetas (nombre_etiqueta)
         VALUES (?)
         ON DUPLICATE KEY UPDATE id_etiqueta = LAST_INSERT_ID(id_etiqueta)"
    );
    $stmt_link = $pdo->prepare(
        // evita duplicados con índice único (id_post, id_etiqueta) -> INSERT IGNORE
        "INSERT IGNORE INTO post_etique_
