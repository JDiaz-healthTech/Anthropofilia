<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$page_title = 'Política de Privacidad';
require_once __DIR__ . '/header.php';
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
  
  <h1>Política de Privacidad</h1>

  <p>En <strong>Anthropofilia</strong> nos tomamos en serio tu privacidad. Esta página describe cómo recopilamos, usamos y protegemos la información personal que nos proporcionas.</p>

  <h2>Datos que recopilamos</h2>
  <ul>
    <li>Información que envías voluntariamente a través del formulario de contacto (nombre, correo electrónico, mensaje).</li>
    <li>Datos técnicos básicos de navegación (dirección IP anónima, agente de usuario, cookies necesarias).</li>
  </ul>

  <h2>Finalidad</h2>
  <p>La información que recopilamos se utiliza exclusivamente para responder a tus consultas, mejorar la experiencia de navegación y mantener la seguridad del sitio.</p>

  <h2>Conservación</h2>
  <p>Los datos personales se conservan el tiempo estrictamente necesario para atender tu solicitud y se eliminan periódicamente de los registros.</p>

  <h2>Tus derechos</h2>
  <p>Puedes ejercer los derechos de acceso, rectificación y supresión de tus datos enviando un correo a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a>.</p>

  <h2>Responsable</h2>
  <p>Responsable del tratamiento: Anthropofilia – Editora Ana López Sampedro.</p>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
