<?php
// Seguridad e inicialización
session_start();
require_once 'config.php';
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    // Recoger datos del formulario
    $id_post = (int)$_POST['id_post'];
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $id_categoria = (int)$_POST['id_categoria'];
    $imagen_url = !empty($_POST['imagen_url']) ? $_POST['imagen_url'] : NULL;

    // La sentencia UPDATE
    $sql = "UPDATE posts SET titulo = ?, contenido = ?, id_categoria = ?, imagen_destacada_url = ? WHERE id_post = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Vincular parámetros: string, string, integer, string, integer
        $stmt->bind_param("ssisi", $titulo, $contenido, $id_categoria, $imagen_url, $id_post);

        if ($stmt->execute()) {
            // Redirigir a la gestión de posts tras el éxito
            header("Location: gestionar_posts.php");
            exit();
        } else {
            echo "Error al actualizar el post: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
