<?php declare(strict_types=1); http_response_code(404);
$page_title = 'Página no encontrada';
  $categoria = null; // para migas condicionales
  require_once __DIR__.'/header.php';
<main class="container">
  <h1>404</h1>
  <p>Lo sentimos, no encontramos lo que buscas.</p>
  <p><a href="<?= url('index.php') ?>">Volver al inicio</a></p>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
