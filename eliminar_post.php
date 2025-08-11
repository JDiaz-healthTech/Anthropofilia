<?php
// 1. Seguridad e Inicialización
session_start();
require_once 'config.php';

// Comprobar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// ===================================================================
// !! BLOQUE DE VALIDACIÓN CSRF - INICIO !!
// ===================================================================
$token_recibido = $_GET['token'] ?? '';

// Comprobamos tres cosas:
// 1. Que el token exista en la sesión.
// 2. Que hayamos recibido un token en la URL.
// 3. Que ambos tokens sean idénticos.
if (!isset($_SESSION['csrf_token']) || empty($token_recibido) || !hash_equals($_SESSION['csrf_token'], $token_recibido)) {
    // Si algo falla, detenemos la ejecución.
    // hash_equals() es una función segura para comparar cadenas de texto y evitar ataques de temporización.
    die("Error de seguridad: La acción ha sido cancelada por un posible ataque CSRF.");
}
// ===================================================================
// !! BLOQUE DE VALIDACIÓN CSRF - FIN !!
// ===================================================================

// 2. Obtener y validar el ID del post desde la URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    die("ID de post no válido.");
}

// 3. Preparar la sentencia DELETE
// Es una sentencia simple pero debe ser preparada para seguridad.
$sql = "DELETE FROM posts WHERE id_post = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 4. Vincular el parámetro y ejecutar
    $stmt->bind_param("i", $post_id);
    
    if ($stmt->execute()) {
        // 5. Redirigir a la gestión de posts tras el éxito
        header("Location: gestionar_posts.php");
        exit();
    } else {
        echo "Error al eliminar el post: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $conn->error;
}

$conn->close();
?>