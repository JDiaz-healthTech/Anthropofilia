<?php
require_once __DIR__ . '/init.php';

// Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// PRG: datos previos si hubo error al guardar
$old = $_SESSION['form_post'] ?? [];
unset($_SESSION['form_post']); // limpia tras mostrar

// CSRF
$csrf = $security->csrfToken();

// Cargar categorías (PDO)
$categorias = [];
try {
    $sql = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // En prod: $security->logEvent('error','load_categories_failed',['error'=>$e->getMessage()]);
    $categorias = [];
}

$page_title = 'Crear Nuevo Post';
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
 
  <h1>Crear nuevo post</h1>

  <form action="<?= url('guardar_post.php') ?>" method="post" enctype="multipart/form-data" class="form-container" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

    <div>
      <label for="titulo">Título</label>
      <input
        type="text" id="titulo" name="titulo"
        required maxlength="150" autocomplete="off"
        value="<?= htmlspecialchars($old['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="id_categoria">Categoría</label>
      <select id="id_categoria" name="id_categoria" required>
        <option value="">— Selecciona una categoría —</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['id_categoria'] ?>"
            <?= isset($old['id_categoria']) && (int)$old['id_categoria'] === (int)$cat['id_categoria'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="etiquetas">Etiquetas</label>
      <input
        type="text" id="etiquetas" name="etiquetas"
        maxlength="500" placeholder="separa con comas: ciencia, evolución"
        value="<?= htmlspecialchars($old['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <small>Se guardan en minúsculas y se desduplican automáticamente.</small>
    </div>

    <div>
      <label for="contenido">Contenido</label>
      <textarea id="editor" name="contenido" rows="15" maxlength="100000"><?= htmlspecialchars($old['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label for="imagen">Imagen destacada (opcional)</label>
      <input
        type="file" id="imagen" name="imagen"
        accept="image/jpeg,image/png,image/gif">
      <!-- (Opcional) <input type="hidden" name="MAX_FILE_SIZE" value="2097152"> -->
    </div>

    <button type="submit">Guardar post</button>
  </form>
</main>

<!-- Configuración de TinyMCE-->
<script>
if (typeof tinymce !== 'undefined') {
  tinymce.init({
    selector: '#editor',
    // Plugins útiles
    plugins: 'code link lists image media table autoresize paste',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code',
    menubar: false,
    height: 540,
    branding: false,
    convert_urls: false, 

    // Bloques y formato
    block_formats: 'Párrafo=p; Encabezado 2=h2; Encabezado 3=h3; Encabezado 4=h4; Cita=blockquote; Preformateado=pre',

    // Enlaces: abre por defecto en pestaña nueva y con rel seguro
    link_target_list: [{ title: 'Nueva pestaña', value: '_blank' }, { title: 'Misma pestaña', value: '' }],
    rel_list: [{ title: 'Ninguno', value: '' }, { title: 'noopener', value: 'noopener' }, { title: 'nofollow', value: 'nofollow' }],
    default_link_target: '_blank',

    // Imágenes: subida al backend y leyendas
    images_upload_url: '<?= url("upload_image.php") ?>',
    images_upload_credentials: true,
    automatic_uploads: true,
    image_caption: true,
    image_dimensions: false,
    // Opcional: clases para responsive en tus posts
    image_class_list: [
      { title: 'Por defecto', value: '' },
      { title: 'Ancho completo', value: 'img-wide' }
    ],

    // Manejador con CSRF en cabecera
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

    // Pegar: no incrustar imágenes base64 (mejor subirlas)
    paste_data_images: false,

    // CSS del contenido dentro del editor (WYSIWYG)
    content_css: [
      '<?= url("assets/css/styles.css") ?>'  // tu hoja pública
    ],
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

    // Accesibilidad y UX
    browser_spellcheck: true,
    contextmenu: false
  });
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
