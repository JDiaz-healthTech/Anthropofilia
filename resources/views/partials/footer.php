<?php
// footer.php
$year = (int)date('Y');
$isLogged = isset($security) ? (bool)$security->userId() : false;

// Incluir sidebar si está habilitado
if (!empty($showSidebar)):
?>
  <aside class="site-sidebar" role="complementary" aria-label="Barra lateral">
    <?php include __DIR__ . '/sidebar.php'; ?>
  </aside>
<?php
endif;
?>
</div> <!-- /.main-content-area -->

<footer class="main-footer" role="contentinfo">
  <div class="footer-content">

    <!-- COPYRIGHT -->
    <div class="footer-section">
      <p>&copy; <?= $year ?> Ana López Sampedro. Todos los derechos reservados.</p>
    </div>

    <!-- LINKS LEGALES -->
    <div class="footer-links">
      <nav aria-label="Enlaces legales">
        <a href="<?= url('aviso-legal.php') ?>">Aviso legal</a>
        <span aria-hidden="true">·</span>
        <a href="<?= url('privacidad.php') ?>">Privacidad</a>
        <span aria-hidden="true">·</span>
        <a href="<?= url('cookies.php') ?>">Cookies</a>
      </nav>
    </div>

    <!-- ADMIN NAV (solo si está logueado) -->
    <?php if ($isLogged): ?>
      <div class="admin-nav" role="navigation" aria-label="Área de administración">
        <a href="<?= url('dashboard.php') ?>" class="btn-admin-link">
          📊 Panel de Control
        </a>

        <span aria-hidden="true">|</span>

        <a href="#top"
           onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
           class="btn-scroll-top">
          ↑ Volver arriba
        </a>

        <span aria-hidden="true">|</span>

        <form method="POST" action="<?= url('logout.php') ?>">
          <?= isset($security) && method_exists($security, 'csrfField')
              ? $security->csrfField()
              : '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') . '">'
          ?>
          <button type="submit" class="btn-logout">
            🚪 Cerrar sesión
          </button>
        </form>
      </div>
    <?php else: ?>
      <!-- SCROLL TO TOP (usuarios no logueados) -->
      <div class="footer-scroll">
        <a href="#top"
           onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
           class="btn-scroll-top">
          ↑ Volver arriba
        </a>
      </div>
    <?php endif; ?>

  </div>
</footer>

</div><!-- /.container -->

<script defer src="<?= url('js/nav.js') ?>"></script>
<script defer src="<?= url('js/ui.js') ?>"></script>
<script defer src="<?= url('js/accessibility.js') ?>"></script>
<script defer src="<?= url('js/lightbox.js') ?>"></script>
<script defer src="<?= url('js/responsive-iframes.js') ?>"></script>

</body>
</html>
