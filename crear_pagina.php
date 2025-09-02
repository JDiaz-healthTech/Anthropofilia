<?php
// crear_pagina.php
require_once __DIR__ . '/init.php';

// Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// PRG: datos previos del formulario (si hubo error en guardar_pagina.php)
$form = $_SESSION['form_pagina'] ?? [];
unset($_SESSION['form_pagina']); // limpia tras mostrar

// Mensajes de estado (whitelist)
$status = $_GET['status'] ?? '';
$ok  = ($status === 'success');
$err = ($status === 'error' || $status === 'invalid');

// CSRF
$token = $security->csrfToken();

$page_title = 'Crear Nueva Página';
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
 
  <h1>Crear nueva página estática</h1>

  <div aria-live="polite">
    <?php if ($ok): ?>
      <p class="status-success">Página creada correctamente.</p>
    <?php elseif ($err): ?>
      <p class="status-error">No se pudo crear la página. Revisa los datos e inténtalo de nuevo.</p>
    <?php endif; ?>
  </div>

  <form action="/guardar_pagina.php" method="post" class="form-container" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

    <div>
      <label for="titulo">Título</label>
      <input
        type="text" id="titulo" name="titulo"
        required maxlength="150" autocomplete="off"
        value="<?= htmlspecialchars($form['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="slug">Slug (URL amigable)</label>
      <input
        type="text" id="slug" name="slug"
        required maxlength="191" spellcheck="false" autocapitalize="off" autocomplete="off"
        pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
        title="Solo minúsculas, números y guiones (ej: historia-da-filosofia)"
        placeholder="ejemplo: historia-da-filosofia"
        value="<?= htmlspecialchars($form['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <small>Usa minúsculas, sin acentos, separadas por guiones.</small>
    </div>

    <div>
      <label for="contenido">Contenido</label>
      <textarea
        id="contenido" name="contenido" rows="20" required maxlength="50000"><?= htmlspecialchars($form['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <button type="submit">Guardar página</button>
  </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>

<!-- Auto-slug opcional (no sustituye la normalización server-side) -->
<script>
(function () {
  const t = document.getElementById('titulo');
  const s = document.getElementById('slug');
  let userEditedSlug = false;

  s.addEventListener('input', () => { userEditedSlug = true; });

  function slugify(str) {
    return str
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita acentos
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 191);
  }

  t.addEventListener('input', () => {
    if (!userEditedSlug && t.value) {
      s.value = slugify(t.value);
    }
  });
})();
</script>
<?php