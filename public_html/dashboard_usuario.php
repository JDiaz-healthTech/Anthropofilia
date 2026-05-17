<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

$userName = $_SESSION['nombre_usuario'] ?? '';
$nombre = explode(' ', $userName)[0];

$page_title = 'Mi Cuenta';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container" id="content">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Mi Cuenta</span>
    </nav>

    <div class="admin-header">
        <h1>Mi Cuenta</h1>
        <p>¡Bienvenid@, <?= e($nombre) ?>!</p>
    </div>

    <section class="info-card">
        <p>Tu perfil está activo. Próximamente podrás gestionar tus preferencias desde aquí.</p>
    </section>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
