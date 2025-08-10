<?php
// 1. Iniciar la sesión. SIEMPRE lo primero.
session_start();

// 2. Comprobar si el usuario ha iniciado sesión.
// Si la variable de sesión 'id_usuario' NO está definida...
if (!isset($_SESSION['id_usuario'])) {
    // 3. ...redirigir al login y terminar el script.
    header("Location: login.php");
    exit();
}

// Si el script llega hasta aquí, significa que el usuario SÍ ha iniciado sesión.
// A partir de aquí, ponemos todo el contenido exclusivo para el administrador.
?>
<?php
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