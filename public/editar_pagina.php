<?php
// editar_pagina.php — versión pulida
require_once __DIR__ . '/init.php';

// 1) Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2) Obtener y validar id
$pagina_id = (int)$security->cleanInput($_GET['id'] ?? '', 'int');
if ($pagina_id <= 0) {
    http_response_code(400);
    header("Location: gestionar_paginas.php?error=id_invalido");
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3) Cargar la página (evita SELECT*)
    $stmt = $pdo->prepare("SELECT id_pagina, titulo, slug, contenido, orden FROM paginas WHERE id_pagina = ? LIMIT 1");
    $stmt->execute([$pagina_id]);
    $pagina = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pagina) {
        http_response_code(404);
        header("Location: gestionar_paginas.php?error=not_found");
        exit();
    }

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('error','page_load_failed',['id'=>$pagina_id,'error'=>$e->getMessage()]);
    die('Error al cargar la página.');
}

$page_title = 'Editar Página';
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
 
  <h1>Editar página estática</h1>

  <form action="actualizar_pagina.php" method="post" class="form-container" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($security->csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id_pagina" value="<?= (int)$pagina['id_pagina'] ?>">

    <div>
      <label for="titulo">Título</label>
      <input
        type="text" id="titulo" name="titulo"
        required maxlength="150" autocomplete="off"
        value="<?= htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="slug">Slug (URL amigable)</label>
      <input
        type="text" id="slug" name="slug"
        required maxlength="191" spellcheck="false" autocapitalize="off" autocomplete="off"
        pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
        title="Solo minúsculas, números y guiones (ej: historia-da-filosofia)"
        value="<?= htmlspecialchars($pagina['slug'], ENT_QUOTES, 'UTF-8') ?>">
      <small>Usa minúsculas, sin acentos, separadas por guiones.</small>
    </div>

  <div>
      <label for="contenido">Contenido</label>
      <textarea
        id="contenido" name="contenido" rows="20" required maxlength="50000"><?= htmlspecialchars($pagina['contenido'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label for="orden">Orden en el menú</label>
      <input
        type="number" id="orden" name="orden"
        min="0" max="100" step="1"
        value="<?= (int)($pagina['orden'] ?? 0) ?>">
      <small>Número menor aparece primero. Usa 10, 20, 30... para reorganizar fácilmente después.</small>
    </div>

    <div style="display:flex; gap:.5rem; align-items:center;">
      <button type="submit">Actualizar página</button>
      <a href="gestionar_paginas.php">Cancelar</a>
    </div>
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

  // Solo autogenera si el slug está vacío (en edición normalmente no lo estará)
  t.addEventListener('input', () => {
    if (!userEditedSlug && !s.value) {
      s.value = slugify(t.value);
    }
  });
})();
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>