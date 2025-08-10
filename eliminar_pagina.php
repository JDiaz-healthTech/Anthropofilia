<?php
// Seguridad e inicialización
session_start();
require_once 'config.php';
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener y validar el ID
$pagina_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pagina_id <= 0) {
    die("ID de página no válido.");
}

// Preparar y ejecutar la sentencia DELETE
$sql = "DELETE FROM paginas WHERE id_pagina = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pagina_id);

if ($stmt->execute()) {
    header("Location: gestionar_paginas.php");
    exit();
} else {
    echo "Error al eliminar la página: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>