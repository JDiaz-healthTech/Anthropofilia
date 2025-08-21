<?php
// admin.php — versión pulida
require_once __DIR__ . '/init.php';

// 1) Autenticación
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2) Evita cachear el panel en el navegador
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// (Opcional) roles:
// if (($_SESSION['rol'] ?? '') !== 'admin') { http_response_code(403); die('Acceso denegado'); }

// 3) CSRF para logout por POST
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// 4) (Opcional) contadores rápidos
$postsCount = $paginasCount = 0;
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $postsCount   = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $paginasCount = (int)$pdo->query("SELECT COUNT(*) FROM paginas")->fetchColumn();
} catch (Throwable $e) {
    // silencia o loguea si quieres: $security->logEvent('warn','admin_counts_failed',[...] );
}

$page_title = 'Panel de Administración';
require_once __DIR__ . '/header.php';
?>
<main class="container">
  <h1>Panel de Administración</h1>
  <p>¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'usuario', ENT_QUOTES, 'UTF-8') ?>!</p>

  <div class="admin-sections">
    <section class="admin-section" aria-labelledby="gestion-entradas">
      <h2 id="gestion-entradas">Gestionar Entradas</h2>
      <nav aria-label="Acciones de entradas">
        <ul>
          <li><a href="/crear_post.php">Crear nueva entrada</a></li>
          <li>
            <a href="/gestionar_posts.php">Gestionar entradas</a>
            <?php if ($postsCount): ?>
              <span class="badge" aria-label="Total de entradas"><?= $postsCount ?></span>
            <?php endif; ?>
          </li>
        </ul>
      </nav>
    </section>

    <section class="admin-section" aria-labelledby="gestion-paginas">
      <h2 id="gestion-paginas">Gestionar Páginas</h2>
      <nav aria-label="Acciones de páginas">
        <ul>
          <li><a href="/crear_pagina.php">Crear nueva página</a></li>
          <li>
            <a href="/gestionar_paginas.php">Gestionar páginas</a>
            <?php if ($paginasCount): ?>
              <span class="badge" aria-label="Total de páginas"><?= $paginasCount ?></span>
            <?php endif; ?>
          </li>
        </ul>
      </nav>
    </section>
  </div>

  <!-- Logout por POST con CSRF -->
  <form action="/logout.php" method="post" style="margin-top:1rem">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Cerrar sesión</button>
  </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
