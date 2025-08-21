<?php

// 1. Centralizamos todo en init.php
require_once 'init.php';

// 2. Lógica de seguridad con la clase SecurityManager
// Con una sola llamada, validamos el token y terminamos la ejecución si es inválido.
// ¡Adiós al bloque de validación manual!
$token_recibido = $_GET['token'] ?? '';
$security->csrfValidate($token_recibido);

// 3. Obtener y validar el ID del post (no cambia)
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    // Es una buena práctica redirigir en lugar de usar die()
    header("Location: gestionar_posts.php?error=id_invalido");
    exit();
}

// 4. Preparar la sentencia DELETE con PDO
// Usamos PDO::prepare() y execute()
$sql = "DELETE FROM posts WHERE id_post = ?";

// Las excepciones de PDO se encargan del control de errores, por lo que no necesitamos `if ($stmt)`.
$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]); // Pasamos el parámetro en un array a execute().

// 5. Redirigir tras el éxito
header("Location: gestionar_posts.php");
exit();

?>