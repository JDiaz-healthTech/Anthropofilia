<?php
// header.php
declare(strict_types=1);

// Se asume que init.php ya fue cargado por la página que incluye este header
// y que existen (opcionalmente) $page_title y $meta_description.

$current_page = basename($_SERVER['SCRIPT_NAME']); // más robusto que PHP_SELF
$nonce        = (isset($security) && method_exists($security, 'cspNonce')) ? $security->cspNonce() : null;
$isLogged     = isset($security) && method_exists($security, 'userId') ? (bool)$security->userId() : false;

// Canonical absoluto
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path     = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');


//propuesta de cambio
//$baseUrl  = $_ENV['APP_URL'] ?? ($scheme . '://' . $host);
$canonical = url(ltrim($path, '/'));


// Páginas que no deben indexarse
$noindexPages = [
  'login.php','admin.php','gestionar_posts.php','gestionar_paginas.php',
  'crear_post.php','editar_post.php','eliminar_post.php',
  'crear_pagina.php','editar_pagina.php','eliminar_pagina.php'
];

// ¿Cargar TinyMCE?
$needsTinymce = in_array($current_page, ['crear_post.php','editar_post.php'], true);

// Helper para clase "active" en nav
function nav_active(string $file, ?string $slug = null): string {
    $currFile = basename($_SERVER['SCRIPT_NAME']);
    if ($slug !== null) {
        return ($currFile === 'pagina.php' && (($_GET['slug'] ?? null) === $slug)) ? ' class="active"' : '';
    }
    return ($currFile === $file) ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="color-scheme" content="light dark">
    <meta name="format-detection" content="telephone=no">
    <title><?php echo htmlspecialchars($page_title ?? 'Anthropofilia Blog', ENT_QUOTES, 'UTF-8'); ?></title>

    <?php if (!empty($meta_description)): ?>
      <meta name="description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <!-- <link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>"> -->

    <?php if (in_array($current_page, $noindexPages, true)): ?>
      <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <link rel="stylesheet" href="./style.css">


<?php
// Puedes traerte aquí $settings o leer las 3 claves: color_site_bg, color_content_bg, bg_current
$bgSite    = $settings['color_site_bg']    ?? '#ffffff';
$bgContent = $settings['color_content_bg'] ?? '#ffffff';
$bgImage   = $settings['bg_current']       ?? null;
?>
<style>
  :root {
    --bg: <?= htmlspecialchars($bgSite, ENT_QUOTES, 'UTF-8') ?>;
    --bg-content: <?= htmlspecialchars($bgContent, ENT_QUOTES, 'UTF-8') ?>;
  }
  body {
    background: var(--bg) <?= $bgImage ? 'url(/uploads/backgrounds/'.htmlspecialchars($bgImage, ENT_QUOTES, 'UTF-8').') center/cover no-repeat fixed' : '' ?>;
  }
  .main, .content, article { background: var(--bg-content); }
</style>


    <?php if ($needsTinymce): ?>
      <!-- Performance hint para TinyMCE -->
      <link rel="preconnect" href="https://cdn.tiny.cloud" crossorigin>
      <script
         src="https://cdn.tiny.cloud/1/55d1aaf8txhz0grsfs3s9dqm214nb4tk0p06h6ydeby0vta1/tinymce/6/tinymce.min.js"
         referrerpolicy="origin"
         <?php echo $nonce ? ' nonce="'.htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8').'"' : ''; ?>>
      </script>
    <?php endif; ?>
    <script src="./accessibility.js" defer></script>
</head>
<body id="top">
  <div class="container">
    <header class="main-header" role="banner">
      <h1>ANTHROPOFILIA</h1>
      <p>El blog de Ana López Sampedro</p>
    </header>

    <nav class="main-nav" role="navigation" aria-label="Principal">
      <button class="mobile-nav-toggle" aria-controls="main-nav-links" aria-expanded="false">
        <span class="visually-hidden">Menú</span>
        <span class="hamburger-icon"><span></span></span>
      </button>
        <div class="main-nav-links" id="main-nav-links">
          <a href="<?= url('index.php') ?>"             <?= nav_active('index.php'); ?>>Inicio</a>
          <a href="<?= url('pagina.php?slug=historia-da-filosofia') ?>" <?= nav_active('pagina.php','historia-da-filosofia'); ?>>Historia da Filosofía</a>
          <a href="<?= url('categoria.php?slug=lecturas-e-peliculas') ?>" <?= nav_active('categoria.php'); ?>>Lecturas e Películas</a>
          <a href="<?= url('pagina.php?slug=etica') ?>" <?= nav_active('pagina.php','etica'); ?>>Ética</a>
          <a href="<?= url('acerca_de_mi.php') ?>"      <?= nav_active('acerca_de_mi.php'); ?>>Acerca de mí</a>
          <a href="<?= url('contacto.php') ?>"          <?= nav_active('contacto.php'); ?>>Contacto</a>

          <span class="spacer"></span>
          <?php if ($isLogged): ?>
            <a href="<?= url('gestionar_posts.php') ?>" <?= nav_active('gestionar_posts.php'); ?>>Panel</a>
          <?php else: ?>
            <a href="<?= url('login.php') ?>"           <?= nav_active('login.php'); ?> rel="nofollow">Admin</a>
          <?php endif; ?>
        </div>
<div class="accessibility-controls">
  <button id="toggle-dark"           title="Alternar claro/oscuro"       class="accessibility-button" aria-pressed="false">🌗</button>
  <button id="toggle-high-contrast"  title="Alternar alto contraste"     class="accessibility-button" aria-pressed="false">HC</button>
  <button id="increase-font-size"    title="Aumentar tamaño de fuente"   class="accessibility-button">A+</button>
  <button id="decrease-font-size"    title="Disminuir tamaño de fuente"  class="accessibility-button">A−</button>
</div>
    </nav>

    <div class="main-content-area">
