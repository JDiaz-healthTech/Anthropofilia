<?php
// footer.php
// Si no tienes $security disponible, descomenta la siguiente línea:
// require_once __DIR__ . '/init.php';

$year      = (int)date('Y');
$isLogged  = isset($security) ? (bool)$security->userId() : false;
?>
<footer class="main-footer" role="contentinfo">
  <p>&copy; <?= $year ?> Ana López Sampedro. Todos los derechos reservados.</p>

  <nav class="footer-nav" aria-label="Enlaces legales">
    <a href="/aviso-legal.php">Aviso legal</a>
    <span aria-hidden="true">·</span>
    <a href="/privacidad.php">Privacidad</a>
    <span aria-hidden="true">·</span>
    <a href="/cookies.php">Cookies</a>
  </nav>

  <nav class="admin-nav" aria-label="Área de administración">
    <?php if (!$isLogged): ?>
      <a href="/login.php" class="admin-link" rel="nofollow">Admin Login</a>
    <?php else: ?>
      <a href="/admin.php" class="admin-link">Panel</a>
      <form action="/logout.php" method="POST" class="logout-form" style="display:inline">
        <?= $security->csrfField(); ?>
        <button type="submit">Salir</button>
      </form>
    <?php endif; ?>
  </nav>

  <a href="#top" class="back-to-top">Volver arriba</a>
</footer>
