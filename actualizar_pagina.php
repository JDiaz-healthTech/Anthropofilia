<?php
// Seguridad e inicialización
session_start();
require_once 'config.php';
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $id_pagina = (int)$_POST['id_pagina'];
    $titulo = trim($_POST['titulo']);
    $slug = trim($_POST['slug']);
    $contenido = trim($_POST['contenido']);

    // Actualizar la base de datos
    $sql = "UPDATE paginas SET titulo = ?, slug = ?, contenido = ? WHERE id_pagina = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $slug, $contenido, $id_pagina);
    
    if ($stmt->execute()) {
        header("Location: gestionar_paginas.php");
        exit();
    } else {
        echo "Error al actualizar la página: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>