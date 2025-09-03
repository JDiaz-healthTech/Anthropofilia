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

// --- handler Apariencia (⬅ AÑADIDO: va aquí, antes de cualquier HTML) ---
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'save_theme') {
    try {
        if (isset($security) && method_exists($security, 'requireValidCsrf')) {
            $security->requireValidCsrf();
        } else {
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
                throw new RuntimeException('CSRF inválido');
            }
        }

        // 1) Colores
        $primary = trim((string)($_POST['theme_primary_color'] ?? ''));
        $bg      = trim((string)($_POST['theme_bg_color'] ?? ''));

        if ($primary !== '' && is_hex_color($primary)) set_setting($pdo, 'theme_primary_color', $primary);
        if ($bg      !== '' && is_hex_color($bg))      set_setting($pdo, 'theme_bg_color', $bg);

        // 2) Imagen de cabecera
        if (!empty($_FILES['header_bg']['name']) && $_FILES['header_bg']['error'] === UPLOAD_ERR_OK) {
            $tmp  = $_FILES['header_bg']['tmp_name'];
            $size = (int)($_FILES['header_bg']['size'] ?? 0);
            if ($size <= 0 || $size > 2*1024*1024) throw new RuntimeException('La imagen supera 2MB o es inválida.');

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($tmp) ?: 'application/octet-stream';
            $ext   = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                default      => null
            };
            if ($ext === null) throw new RuntimeException('Formato no permitido. Usa JPG/PNG/WEBP.');

            $uploadDir = __DIR__ . '/public/uploads/theme';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }

            $basename = 'header-bg-' . date('Ymd-His') . '.' . $ext;
            $destFs   = $uploadDir . '/' . $basename;
            if (!move_uploaded_file($tmp, $destFs)) throw new RuntimeException('No se pudo guardar el archivo.');

            $relUrl = 'public/uploads/theme/' . $basename;
            set_setting($pdo, 'header_bg_url', $relUrl);
        }

        $msg_ok = 'Apariencia actualizada correctamente.';
    } catch (Throwable $e) {
        $msg_err = 'Error al guardar: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

// valores actuales
$current_primary = get_setting($pdo, 'theme_primary_color', '#0645ad');
$current_bg      = get_setting($pdo, 'theme_bg_color', '#ffffff');
$current_header  = get_setting($pdo, 'header_bg_url', '');

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
  $categoria = null; // para migas condicionales
  require_once __DIR__.'/header.php';
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
 
  <h1>Panel de Administración</h1>
  <p>¡Bienvenido, <?= htmlspecialchars($_SESSION['nombre_usuario'] ?? 'usuario', ENT_QUOTES, 'UTF-8') ?>!</p>

  <div class="admin-sections">
    <section class="admin-section" aria-labelledby="gestion-entradas">
      <h2 id="gestion-entradas">Gestionar Entradas</h2>
      <nav aria-label="Acciones de entradas">
        <ul>
          <li><a href="<?= url('crear_post.php') ?>">Crear nueva entrada</a></li>
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
<form action="<?= url('logout.php') ?>" method="post" style="margin-top:1rem">
  <?= $security->csrfField(); ?>
  <button type="submit">Cerrar sesión</button>
</form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
