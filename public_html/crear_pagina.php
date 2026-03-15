<?php
// crear_pagina.php
require_once __DIR__ . '/init.php';

// Auth
$security->requireLogin();

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
  $categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
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

<form action="<?= url('guardar_pagina.php') ?>" method="post" class="form-container" accept-charset="UTF-8">
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
        id="contenido" name="contenido" rows="20" maxlength="50000"><?= htmlspecialchars($form['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label for="orden">Orden en el menú</label>
      <input
        type="number" id="orden" name="orden"
        min="0" max="100" step="1"
        value="<?= htmlspecialchars($form['orden'] ?? '0', ENT_QUOTES, 'UTF-8') ?>">
      <small>Número menor aparece primero. Usa 10, 20, 30... para reorganizar fácilmente después.</small>
    </div>
    <div class="form-group form-group--checkbox">
      <label class="checkbox-label">
          <input type="checkbox" id="mostrar_indice" name="mostrar_indice" value="1"
                <?= !empty($form['mostrar_indice']) ? 'checked' : '' ?>>
          <span class="checkbox-text">Mostrar índice de contenidos</span>
      </label>
      <small>Genera automáticamente un índice basado en los títulos (Título 1, 2, 3) del contenido.</small>
    </div>

    <button type="submit">Guardar página</button>
  </form>
</main>
<!-- CSS del modal para insertar posts -->
<link rel="stylesheet" href="<?= url('css/components/post-embed-modal.css') ?>">
<?php
// CSP Nonce para scripts inline
$nonceAttr = ($security->cspNonce())
    ? ' nonce="'.htmlspecialchars($security->cspNonce(), ENT_QUOTES, 'UTF-8').'"'
    : '';
?>

<!-- Plugin de inserción de posts -->
<script src="<?= url('js/tinymce-post-embed.js') ?>"<?= $nonceAttr ?>></script>
<script<?= $nonceAttr ?>>
window.PostEmbedConfig = {
    searchUrl: '<?= url("api/post_search.php") ?>',
    csrfToken: '<?= $security->csrfToken() ?>'
};
</script>

<script src="js/tinymce-config.js"<?= $nonceAttr ?>></script>
<script<?= $nonceAttr ?>>
  initTinyMCE({
    csrfToken: '<?= $security->csrfToken() ?>',
    uploadUrl: '<?= url("upload_image.php") ?>',
    contentCss: '<?= url("css/style.css") ?>',
    type: 'page'
  });
</script>

<script src="js/slugify.js"<?= $nonceAttr ?>></script>


<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
