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

    <div>
      <label for="orden">Orden en el menú</label>
      <input
        type="number" id="orden" name="orden"
        min="0" max="100" step="1"
        value="<?= htmlspecialchars($form['orden'] ?? '0', ENT_QUOTES, 'UTF-8') ?>">
      <small>Número menor aparece primero. Usa 10, 20, 30... para reorganizar fácilmente después.</small>
    </div>

    <button type="submit">Guardar página</button>
  </form>
</main>

<?php
// CSP Nonce para scripts inline
$nonceAttr = ($security->cspNonce())
    ? ' nonce="'.htmlspecialchars($security->cspNonce(), ENT_QUOTES, 'UTF-8').'"'
    : '';
?>

<!-- Configuración de TinyMCE -->
<script<?= $nonceAttr ?>>
if (typeof tinymce !== 'undefined') {
  tinymce.init({
    selector: '#contenido',
    plugins: 'code link lists image media table autoresize paste',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media table | code',
    menubar: false,
    height: 540,

    block_formats: 'Párrafo=p; Encabezado 2=h2; Encabezado 3=h3; Encabezado 4=h4; Cita=blockquote; Preformateado=pre',

    link_target_list: [{ title: 'Nueva pestaña', value: '_blank' }, { title: 'Misma pestaña', value: '' }],
    rel_list: [{ title: 'Ninguno', value: '' }, { title: 'noopener', value: 'noopener' }, { title: 'nofollow', value: 'nofollow' }],
    default_link_target: '_blank',

    images_upload_url: '<?= url("upload_image.php") ?>',
    images_upload_credentials: true,
    automatic_uploads: true,
    image_caption: true,
    image_dimensions: false,
    image_class_list: [
      { title: 'Por defecto', value: '' },
      { title: 'Ancho completo', value: 'img-wide' }
    ],

    images_upload_handler: function (blobInfo, progress) {
      return new Promise(function(resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= url("upload_image.php") ?>');
        xhr.withCredentials = true;
        xhr.setRequestHeader('X-CSRF-Token', '<?= $security->csrfToken() ?>');
        xhr.upload.onprogress = function (e) { if (e.lengthComputable) progress(e.loaded / e.total * 100); };
        xhr.onload = function () {
          if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP ' + xhr.status); return; }
          try {
            var json = JSON.parse(xhr.responseText);
            if (!json || typeof json.location !== 'string') { reject('Respuesta inválida'); return; }
            resolve(json.location);
          } catch (err) { reject('JSON inválido'); }
        };
        xhr.onerror = function () { reject('Error de red'); };
        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
      });
    },

    paste_data_images: false,

    content_css: ['<?= url("css/style.css") ?>'],
    content_style: `
      body { max-width: 760px; margin: 1rem auto; line-height: 1.7; }
      figure { margin: 1.2rem 0; }
      figcaption { font-size: .9rem; opacity: .8; text-align: center; }
      img { border-radius: 6px; }
      blockquote { border-left: 4px solid var(--theme-primary, #8a4); padding:.6rem 1rem; background:rgba(0,0,0,.03); }
      table { border-collapse: collapse; width: 100%; }
      table, th, td { border: 1px solid #ddd; }
      th, td { padding: .5rem; }
      ul, ol { margin-left: 1.2rem; }
      pre, code { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace; }
    `,

    browser_spellcheck: true,
    contextmenu: false
  });
}
</script>

<!-- Auto-slug -->
<script<?= $nonceAttr ?>>
(function () {
  const t = document.getElementById('titulo');
  const s = document.getElementById('slug');
  let userEditedSlug = false;

  s.addEventListener('input', () => { userEditedSlug = true; });

  function slugify(str) {
    return str
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
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

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>