<?php
// actualizar_pagina.php

// 1. SEGURIDAD E INICIALIZACIÓN
// Requerimos init.php para tener acceso a $security y $pdo.
require_once 'init.php';

// Validar que el usuario esté logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2. VALIDACIÓN CSRF Y MÉTODO DE PETICIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token_recibido = $_POST['csrf_token'] ?? '';
    // Este método detendrá el script si el token no es válido.
    $security->csrfValidate($token_recibido);

    try {
        // 3. RECOGER Y SANITIZAR DATOS DEL FORMULARIO
        $id_pagina = (int)$security->cleanInput($_POST['id_pagina'], 'int');
        $titulo = $security->cleanInput($_POST['titulo']);
        $slug = $security->cleanInput($_POST['slug']);
        $contenido = $_POST['contenido']; // El contenido puede tener HTML

        if (empty($titulo) || empty($slug) || empty($contenido)) {
            die("Error: Título, slug y contenido son obligatorios.");
        }
        
        // 4. PREPARAR Y EJECUTAR LA SENTENCIA UPDATE CON PDO
        $sql = "UPDATE paginas SET titulo = ?, slug = ?, contenido = ? WHERE id_pagina = ?";
        $stmt = $pdo->prepare($sql);
        
        // PDO::execute() recibe los parámetros en un array.
        $stmt->execute([$titulo, $slug, $contenido, $id_pagina]);
        
        // 5. REDIRIGIR TRAS EL ÉXITO
        header("Location: gestionar_paginas.php");
        exit();
        
    } catch (PDOException $e) {
        // 6. MANEJO DE ERRORES CON PDO
        $security->logEvent('error', 'page_update_failed', ['page_id' => $id_pagina, 'error' => $e->getMessage()]);
        die("Error al actualizar la página. Por favor, inténtelo de nuevo.");
    }
}