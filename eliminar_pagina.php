<?php
// eliminar_pagina.php

// 1. SEGURIDAD E INICIALIZACIÓN
// Requerimos init.php para tener acceso a $security y $pdo.
require_once 'init.php';

// Validar que el usuario esté logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. VALIDACIÓN CSRF y OBTENER ID
// El token CSRF viene por la URL (método GET).
$token_recibido = $_GET['token'] ?? '';
$security->csrfValidate($token_recibido);

// Obtener y validar el ID de la página desde la URL.
$pagina_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pagina_id <= 0) {
    header("Location: gestionar_paginas.php?error=id_invalido");
    exit();
}

try {
    // 3. PREPARAR Y EJECUTAR LA SENTENCIA DELETE CON PDO
    // Usamos un bloque try/catch para manejar cualquier error de la base de datos.
    $sql = "DELETE FROM paginas WHERE id_pagina = ?";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([$pagina_id]);
    
    // 4. REDIRIGIR TRAS EL ÉXITO
    header("Location: gestionar_paginas.php");
    exit();
    
} catch (PDOException $e) {
    // 5. MANEJO DE ERRORES CON PDO
    $security->logEvent('error', 'page_delete_failed', ['page_id' => $pagina_id, 'error' => $e->getMessage()]);
    die("Error al eliminar la página. Por favor, inténtelo de nuevo.");
}