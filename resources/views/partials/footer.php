<?php
// footer.php
$year      = (int)date('Y');
$isLogged  = isset($security) ? (bool)$security->userId() : false;

if (empty($noSidebar)):
?>
  <aside class="site-sidebar" role="complementary" aria-label="Barra lateral">
    <?php include __DIR__ . '/sidebar.php'; ?>
  </aside>
<?php
endif;
?>
</div> <!-- /.main-content-area -->

<footer class="main-footer" role="contentinfo">
  <p>&copy; <?= $year ?> Ana López Sampedro. Todos los derechos reservados.</p>

  <nav class="footer-nav" aria-label="Enlaces legales">
    <a href="<?= url('aviso-legal.php') ?>">Aviso legal</a>
    <span aria-hidden="true">·</span>
    <a href="<?= url('privacidad.php') ?>">Privacidad</a>
    <span aria-hidden="true">·</span>
    <a href="<?= url('cookies.php') ?>">Cookies</a>
  </nav>

  <?php if ($isLogged): ?>
  <nav class="admin-nav" aria-label="Área de administración">
    <a href="<?= url('dashboard.php') ?>" class="admin-link">Dashboard</a>
    <form action="<?= url('logout.php') ?>" method="POST" >
      <?= isset($security) && method_exists($security, 'csrfField') ? $security->csrfField() : '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') . '">' ?>
      <button type="submit" class="btn btn-logout">Cerrar sesión</button>
    </form>
  </nav>
  <?php endif; ?>

  <a href="#top" class="back-to-top">↑ Volver arriba</a>
</footer>

</div><!-- /.container -->

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mainNavLinks = document.getElementById('main-nav-links');

    if (mobileNavToggle && mainNavLinks) {
      mobileNavToggle.addEventListener('click', function() {
        const isOpen = mainNavLinks.classList.toggle('active');
        this.classList.toggle('is-open', isOpen);
        this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        this.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
      });
    }
  });
</script>

<script defer src="<?= url('js/ui.js') ?>"></script>
<script defer src="<?= url('js/accessibility.js') ?>"></script>
<script defer src="<?= url('js/lightbox.js') ?>"></script>

</body>
</html>