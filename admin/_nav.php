<?php
$routes = require __DIR__ . '/routes.php';
function admin_url(string $key): string {
  global $routes;
  return isset($routes[$key]) ? ('/admin/' . $routes[$key]) : '#';
}
function admin_active(string $key): bool {
  global $routes;
  $cur = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
  return isset($routes[$key]) && $routes[$key] === $cur;
}
?>
<nav class="admin-nav">
  <a href="<?= admin_url('dashboard') ?>" aria-current="<?= admin_active('dashboard') ? 'page' : 'false' ?>">Inicio</a>
  <a href="<?= admin_url('posts') ?>">Entradas</a>
  <a href="<?= admin_url('post_new') ?>">Nueva</a>
  <a href="<?= admin_url('media') ?>">Medios</a>
  <a href="<?= admin_url('backgrounds') ?>">Fondos</a>
  <a href="<?= admin_url('personalizar') ?>">Diseño</a>
  <a href="<?= admin_url('settings') ?>">Ajustes</a>
</nav>
