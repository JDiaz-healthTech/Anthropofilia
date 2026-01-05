<?php
declare(strict_types=1);

http_response_code(500);

$page_title = 'Error interno del servidor - 500';
$meta_description = 'Ha ocurrido un error inesperado.';
$categoria = null;

require_once __DIR__ . '/../views/partials/header.php';
?>

<main class="container error-page">
    <div class="error-content">
        <div class="error-code">500</div>
        <h1>Error interno del servidor</h1>
        <p class="error-message">
            Lo sentimos, ha ocurrido un error inesperado. Nuestro equipo ha sido notificado.
        </p>
        
        <div class="error-actions">
            <a href="<?= url('index.php') ?>" class="btn btn-primary">
                ← Volver al inicio
            </a>
            <a href="javascript:history.back()" class="btn">
                ← Página anterior
            </a>
        </div>

        <div class="error-suggestions">
            <h3>¿Qué puedes hacer?</h3>
            <ul>
                <li>Intenta recargar la página en unos minutos</li>
                <li>Si el problema persiste, <a href="<?= url('contacto.php') ?>">contáctanos</a></li>
                <li>Vuelve al <a href="<?= url('index.php') ?>">inicio</a> y navega desde allí</li>
            </ul>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>