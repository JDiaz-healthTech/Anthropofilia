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
    $titulo = trim($_POST['titulo']);
    $slug = trim($_POST['slug']);
    $contenido = trim($_POST['contenido']);

    if (empty($titulo) || empty($slug) || empty($contenido)) {
        die("Error: Título, slug y contenido son obligatorios.");
    }

    $sql = "INSERT INTO paginas (titulo, slug, contenido) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $titulo, $slug, $contenido);
    
    if ($stmt->execute()) {
        header("Location: admin.php?status=page_created");
        exit();
    } else {
        echo "Error al guardar la página: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>