<?php
declare(strict_types=1);

http_response_code(404);

$page_title = 'Página no encontrada - 404';
$meta_description = 'La página que buscas no existe.';
$categoria = null;

require_once __DIR__ . '/../views/partials/header.php';
?>

<main class="container error-page">
    <div class="error-content">
        <div class="error-code">404</div>
        <h1>Página no encontrada</h1>
        <p class="error-message">
            Lo sentimos, la página que buscas no existe o ha sido movida.
        </p>
        
        <div class="error-actions">
            <a href="<?= url('index.php') ?>" class="btn btn-primary">
                ← Volver al inicio
            </a>
            <a href="<?= url('search.php') ?>" class="btn">
                🔍 Buscar en el blog
            </a>
        </div>

        <div class="error-suggestions">
            <h3>Sugerencias:</h3>
            <ul>
                <li>Verifica que la URL esté escrita correctamente</li>
                <li>Usa el buscador para encontrar contenido relacionado</li>
                <li>Explora nuestras <a href="<?= url('index.php') ?>">últimas entradas</a></li>
            </ul>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>