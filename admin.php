<?php
// 1. Prepara el entorno (siempre primero)
require_once 'init.php'; 

// 2. Ahora, pon al guardia de seguridad en la puerta d e esta página específica.
if (!isset($_SESSION['id_usuario'])) {
    // 3. ...redirigir al login y terminar el script.
    header("Location: login.php");
    exit();
}

// Si el script llega hasta aquí, significa que el entorno está listo Y el usuario tiene permiso.
// A partir de aquí, el resto del código de la página.
$page_title = 'Panel de Administración';
require_once 'header.php';
?>

// En admin.php

<main>
    <h2>Panel de Administración</h2>
    <p>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>!</p>

    <div class="admin-sections">
        <section class="admin-section">
            <h3>Gestionar Entradas</h3>
            <nav>
                <ul>
                    <li><a href="crear_post.php">Crear Nueva Entrada</a></li>
                    <li><a href="gestionar_posts.php">Gestionar Entradas Existentes</a></li>
                </ul>
            </nav>
        </section>

        <section class="admin-section">
            <h3>Gestionar Páginas</h3>
            <nav>
                <ul>
                    <li><a href="crear_pagina.php">Crear Nueva Página</a></li>
                    <li><a href="gestionar_paginas.php">Gestionar Páginas Existentes</a></li>
                </ul>
            </nav>
        </section>
    </div>

    <a href="logout.php">Cerrar Sesión</a>
</main>

<?php require_once 'footer.php'; ?>