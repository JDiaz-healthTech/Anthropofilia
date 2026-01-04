<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$page_title = 'Política de Cookies';
  $categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
?>
<main class="container">

    <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <?php if (!empty($categoria)): ?>
      <a href="<?= url('categoria.php?slug=' . urlencode($categoria['slug'])) ?>">
        <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
      </a> <span aria-hidden="true">›</span>
    <?php endif; ?>
    <span aria-current="page"><?= htmlspecialchars($page_title ?? 'Actual', ENT_QUOTES, 'UTF-8') ?></span>
  </nav>
 
  <h1>Política de Cookies</h1>

  <p>Este sitio web utiliza cookies propias y de terceros para mejorar tu experiencia de navegación, analizar el tráfico y garantizar la seguridad.</p>

  <h2>¿Qué son las cookies?</h2>
  <p>Las cookies son pequeños archivos de texto que se almacenan en tu navegador al visitar un sitio web. Permiten reconocer tu visita y recordar tus preferencias.</p>

  <h2>Tipos de cookies utilizadas</h2>
  <ul>
    <li><strong>Cookies necesarias:</strong> imprescindibles para el funcionamiento básico del sitio.</li>
    <li><strong>Cookies de análisis:</strong> nos ayudan a entender cómo los usuarios utilizan el sitio de forma anónima.</li>
    <li><strong>Cookies de terceros:</strong> pueden ser establecidas por servicios externos integrados (ej. contenido embebido).</li>
  </ul>

  <h2>Gestión de cookies</h2>
  <p>Puedes configurar tu navegador para aceptar, rechazar o eliminar cookies. Ten en cuenta que algunas funciones del sitio pueden no estar disponibles si deshabilitas las cookies.</p>

  <h2>Más información</h2>
  <p>Para cualquier duda sobre esta política, puedes escribirnos a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a>.</p>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
