<?php
// actualizar_post.php

// 1. SEGURIDAD E INICIALIZACIÓN
// Requerimos init.php para tener acceso a $security y $pdo.
require_once 'init.php';

// Validar que el usuario esté logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. VALIDACIÓN CSRF Y MÉTODO DE PETICIÓN
// Con una sola línea delegamos la validación del token a nuestro gestor de seguridad.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token_recibido = $_POST['csrf_token'] ?? '';
    // Este método detendrá el script si el token no es válido.
    $security->csrfValidate($token_recibido);

    try {
        // 3. RECOGER Y SANITIZAR DATOS DEL FORMULARIO
        // Usamos nuestro SecurityManager para limpiar los datos.
        $id_post = (int)$security->cleanInput($_POST['id_post'], 'int');
        $titulo = $security->cleanInput($_POST['titulo']);
        $contenido = $_POST['contenido']; // Contenido HTML, no lo sanitizamos aquí para el editor WYSIWYG
        $id_categoria = (int)$security->cleanInput($_POST['id_categoria'], 'int');
        $imagen_url = !empty($_POST['imagen_url']) ? $security->cleanInput($_POST['imagen_url'], 'url') : NULL;

        // 4. PREPARAR Y EJECUTAR LA SENTENCIA UPDATE CON PDO
        $sql = "UPDATE posts SET titulo = ?, contenido = ?, id_categoria = ?, imagen_destacada_url = ? WHERE id_post = ?";
        $stmt = $pdo->prepare($sql);

        // Los parámetros se pasan en un array a execute().
        $stmt->execute([$titulo, $contenido, $id_categoria, $imagen_url, $id_post]);

        // 5. REDIRIGIR TRAS EL ÉXITO
        header("Location: gestionar_posts.php");
        exit();

    } catch (PDOException $e) {
        // 6. MANEJO DE ERRORES CON PDO
        // En caso de un fallo en la base de datos, mostramos un error genérico y registramos el evento.
        // En producción, solo registraríamos el error y redirigiríamos.
        $security->logEvent('error', 'post_update_failed', ['post_id' => $id_post, 'error' => $e->getMessage()]);
        die("Error al actualizar el post. Por favor, inténtelo de nuevo.");
    }
}