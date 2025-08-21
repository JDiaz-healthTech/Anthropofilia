<?php
// guardar_pagina.php

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
    // Validamos el token CSRF que viene del formulario.
    $token_recibido = $_POST['csrf_token'] ?? '';
    $security->csrfValidate($token_recibido);

    try {
        // 3. RECOGER Y SANITIZAR DATOS DEL FORMULARIO
        // Usamos nuestro SecurityManager para limpiar los datos.
        $titulo = $security->cleanInput($_POST['titulo']);
        $slug = $security->cleanInput($_POST['slug']);
        $contenido = $_POST['contenido']; // El contenido puede tener HTML

        if (empty($titulo) || empty($slug) || empty($contenido)) {
            die("Error: Título, slug y contenido son obligatorios.");
        }
        
        // 4. PREPARAR Y EJECUTAR LA SENTENCIA INSERT CON PDO
        $sql = "INSERT INTO paginas (titulo, slug, contenido) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // PDO::execute() recibe los parámetros en un array.
        $stmt->execute([$titulo, $slug, $contenido]);
        
        // 5. REDIRIGIR TRAS EL ÉXITO
        header("Location: admin.php?status=page_created");
        exit();
        
    } catch (PDOException $e) {
        // 6. MANEJO DE ERRORES CON PDO
        // En caso de un fallo en la base de datos, mostramos un error genérico y registramos el evento.
        $security->logEvent('error', 'page_create_failed', ['error' => $e->getMessage()]);
        die("Error al guardar la página. Por favor, inténtelo de nuevo.");
    }
}