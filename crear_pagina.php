<?php
// crear_pagina.php

// 1. INICIALIZACIÓN Y BARRERA DE SEGURIDAD
// Requerimos init.php, que se encarga de session_start() y de inicializar $security.
require_once 'init.php';

// Validar que el usuario esté logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$page_title = 'Crear Nueva Página';
require_once 'header.php';
?>

<main>
    <h2>Crear Nueva Página Estática</h2>
    <form action="guardar_pagina.php" method="POST" class="form-container">
        
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($security->csrfToken()); ?>">
        
        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>
        <div>
            <label for="slug">Slug (URL amigable):</label>
            <input type="text" id="slug" name="slug" placeholder="ejemplo: historia-da-filosofia" required>
        </div>
        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="20" required></textarea>
        </div>
        <button type="submit">Guardar Página</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>