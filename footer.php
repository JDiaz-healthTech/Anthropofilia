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
    <a href="<?= url('aviso-legal.php') ?>">Aviso legal</a>
    <span aria-hidden="true">·</span>
    <a href="<?= url('privacidad.php') ?>">Privacidad</a>
    <span aria-hidden="true">·</span>
    <a href="<?= url('cookies.php') ?>">Cookies</a>
  </nav>

  <nav class="admin-nav" aria-label="Área de administración">
    <?php if (!$isLogged): ?>
      <a href="<?php echo $baseUrl; ?>/login.php" class="admin-link" rel="nofollow">Admin Login</a>
    <?php else: ?>
      <a href="<?php echo $baseUrl; ?>/admin.php" class="admin-link">Panel</a>
      <form action="<?php echo $baseUrl; ?>/logout.php" method="POST" class="logout-form" style="display:inline">
        <?= $security->csrfField(); ?>
        <button type="submit">Salir</button>
      </form>
    <?php endif; ?>
  </nav>

  <a href="#top" class="back-to-top">Volver arriba</a>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Theme switcher
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.add(savedTheme);
        if (savedTheme === 'dark-mode') {
            themeToggle.checked = true;
        }
    }

    themeToggle.addEventListener('change', function () {
        if (this.checked) {
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark-mode');
        } else {
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light-mode');
        }
    });

    // Mobile navigation
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mainNavLinks = document.getElementById('main-nav-links');

    if (mobileNavToggle && mainNavLinks) {
      mobileNavToggle.addEventListener('click', function () {
        const isOpen = mainNavLinks.classList.toggle('active');
        this.classList.toggle('is-open', isOpen);
        this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        this.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
      });
    }
});
</script>
<!-- el primero no tenia defer y daba problemas de carga -->
<script defer src="<?= url('accessibility.js') ?>"></script>
<script defer src="<?= url('public/js/ui.js') ?>"></script>
<script defer src="<?= url('public/js/lightbox.js') ?>"></script>

