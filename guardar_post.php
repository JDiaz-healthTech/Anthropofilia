<?php
// 1. INICIALIZACIÓN Y BARRERA DE SEGURIDAD
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: crear_post.php");
    exit();
}

// ===================================================================
// !! BLOQUE DE VALIDACIÓN CSRF - INICIO !!
// ===================================================================
$token_recibido = $_POST['csrf_token'] ?? '';

if (!isset($_SESSION['csrf_token']) || empty($token_recibido) || !hash_equals($_SESSION['csrf_token'], $token_recibido)) {
    // Usamos `hash_equals` por seguridad, igual que en el paso anterior.
    die("Error de seguridad: Token no válido. La creación del post ha sido cancelada.");
}
// ===================================================================
// !! BLOQUE DE VALIDACIÓN CSRF - FIN !!
// ===================================================================

// 2. RECOGER DATOS Y VALIDAR
$titulo = trim($_POST['titulo']);
$contenido = trim($_POST['contenido']);
$id_categoria = (int)$_POST['id_categoria'];
$id_usuario = $_SESSION['id_usuario'];
$etiquetas_manuales = trim($_POST['etiquetas']); // <-- La variable se guarda aquí

if (empty($titulo) || empty($contenido) || empty($id_categoria)) {
    die("Error: El título, contenido y categoría son obligatorios.");
}

$conn->begin_transaction();

try {
    // 3. PROCESAR LA IMAGEN SUBIDA (VERSIÓN SEGURA)
    $imagen_path = NULL;

    // Comprobamos si se envió un archivo y no hubo errores.
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

        // --- PASO 1: VALIDAR TAMAÑO ---
        $max_size = 2 * 1024 * 1024; // 2 Megabytes
        if ($_FILES['imagen']['size'] > $max_size) {
            throw new Exception("El archivo es demasiado grande. El tamaño máximo es 2MB.");
        }

        // --- PASO 2: VALIDAR TIPO DE ARCHIVO (MIME TYPE) ---
        // Esta es la forma más segura, ya que lee el contenido real del archivo.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);

        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime_type, $allowed_mime_types)) {
            throw new Exception("Tipo de archivo no válido. Solo se permiten JPG, PNG y GIF.");
        }

        // --- PASO 3: GENERAR UN NOMBRE DE ARCHIVO ÚNICO Y SEGURO ---
        // Obtenemos la extensión correcta a partir del tipo MIME validado.
        $extension = match ($mime_type) {
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
        };
        // Creamos un nombre que no tiene ninguna relación con el original.
        $nombre_archivo_seguro = uniqid('post_img_', true) . $extension;

        // --- PASO 4: MOVER EL ARCHIVO ---
        $directorio_subidas = 'uploads/';
        if (!is_dir($directorio_subidas)) {
            mkdir($directorio_subidas, 0755, true);
        }
        $ruta_completa = $directorio_subidas . $nombre_archivo_seguro;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
            $imagen_path = $ruta_completa;
        } else {
            throw new Exception("Error al mover el archivo subido.");
        }
    }
    // 4. INSERTAR EL POST PRINCIPAL
    $sql_post = "INSERT INTO posts (titulo, contenido, id_categoria, imagen_destacada_url, id_usuario) VALUES (?, ?, ?, ?, ?)";
    $stmt_post = $conn->prepare($sql_post);
    $stmt_post->bind_param("ssisi", $titulo, $contenido, $id_categoria, $imagen_path, $id_usuario);
    $stmt_post->execute();
    $id_post_nuevo = $conn->insert_id;
    $stmt_post->close();

    // 5. PROCESAR ETIQUETAS MANUALES
    if (!empty($etiquetas_manuales)) {
        // !! CORRECCIÓN: Convertimos a minúsculas aquí para consistencia !!
        $etiquetas_array = array_map('trim', explode(',', strtolower($etiquetas_manuales)));

        foreach ($etiquetas_array as $nombre_etiqueta) {
            if (empty($nombre_etiqueta)) continue;

            $id_etiqueta_actual = null;
            $sql_find_tag = "SELECT id_etiqueta FROM etiquetas WHERE nombre_etiqueta = ?";
            $stmt_find = $conn->prepare($sql_find_tag);
            $stmt_find->bind_param("s", $nombre_etiqueta);
            $stmt_find->execute();
            $resultado_find = $stmt_find->get_result();
            if ($fila = $resultado_find->fetch_assoc()) {
                $id_etiqueta_actual = $fila['id_etiqueta'];
            } else {
                $sql_insert_tag = "INSERT INTO etiquetas (nombre_etiqueta) VALUES (?)";
                $stmt_insert = $conn->prepare($sql_insert_tag);
                $stmt_insert->bind_param("s", $nombre_etiqueta);
                $stmt_insert->execute();
                $id_etiqueta_actual = $conn->insert_id;
                $stmt_insert->close();
            }
            $stmt_find->close();
            $sql_link = "INSERT INTO post_etiquetas (id_post, id_etiqueta) VALUES (?, ?)";
            $stmt_link = $conn->prepare($sql_link);
            $stmt_link->bind_param("ii", $id_post_nuevo, $id_etiqueta_actual);
            $stmt_link->execute();
            $stmt_link->close();
        }
    }



    // 6. BÚSQUEDA Y ASIGNACIÓN AUTOMÁTICA DE ETIQUETAS
    $contenido_post = strtolower($contenido);
    $sql_todas_etiquetas = "SELECT id_etiqueta, nombre_etiqueta FROM etiquetas";
    $resultado_todas_etiquetas = $conn->query($sql_todas_etiquetas);
    if ($resultado_todas_etiquetas && $resultado_todas_etiquetas->num_rows > 0) {
        while ($etiqueta = $resultado_todas_etiquetas->fetch_assoc()) {
            if (strpos($contenido_post, $etiqueta['nombre_etiqueta']) !== false) {
                $sql_auto_link = "INSERT IGNORE INTO post_etiquetas (id_post, id_etiqueta) VALUES (?, ?)";
                $stmt_auto_link = $conn->prepare($sql_auto_link);
                $stmt_auto_link->bind_param("ii", $id_post_nuevo, $etiqueta['id_etiqueta']);
                $stmt_auto_link->execute();
                $stmt_auto_link->close();
            }
        }
    }

    $conn->commit();
    header("Location: index.php?status=post_created_successfully");
    exit();
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    die("Error crítico al guardar el post y sus etiquetas: " . $exception->getMessage());
}
