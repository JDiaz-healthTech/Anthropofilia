<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$page_title = 'Aviso Legal';
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
 
  <h1>Aviso Legal</h1>

  <h2>Datos identificativos</h2>
  <p>El presente sitio web, <strong>Anthropofilia</strong>, es editado por Ana López Sampedro.</p>

  <h2>Propiedad intelectual</h2>
  <p>Todos los textos, imágenes y contenidos de este sitio, salvo que se indique lo contrario, son propiedad de la editora. Queda prohibida su reproducción, distribución o comunicación pública sin autorización previa.</p>

  <h2>Responsabilidad</h2>
  <p>El contenido de este sitio es de carácter divulgativo y cultural. Aunque se procura la mayor exactitud posible, no se garantiza que la información sea exhaustiva o libre de errores. El uso que el visitante haga de los contenidos es de su exclusiva responsabilidad.</p>

  <h2>Enlaces externos</h2>
  <p>Este sitio puede contener enlaces a páginas de terceros. Anthropofilia no se responsabiliza de los contenidos ni de las políticas de privacidad de dichos sitios.</p>

  <h2>Contacto</h2>
  <p>Para cualquier consulta, puedes escribir a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a>.</p>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
