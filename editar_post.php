<?php
require_once __DIR__ . '/init.php';

// 1) Requiere login
$security->requireLogin();

// 2) Entrada robusta
$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$post_id || $post_id <= 0) {
    $security->abort(400, 'ID de post no válido.');
}

// 3) Cargar post
$sql_post = "SELECT id_post, id_usuario, titulo, contenido, imagen_destacada_url, id_categoria, etiquetas
             FROM posts WHERE id_post = ?";
$stmt = $pdo->prepare($sql_post);
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    $security->abort(404, 'Post no encontrado.');
}

// 4) Autorización
$security->requireOwnershipOrRole((int)$post['id_usuario'], ['admin']);

// 5) Categorías
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute();
$resultado_categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Editar Post';
require_once __DIR__ . '/header.php';
?>

<main>
    <h2>Editar Post</h2>
    <form action="actualizar_post.php" method="POST" class="form-container">
        <?php echo $security->csrfField(); ?>
        <input type="hidden" name="id_post" value="<?php echo (int)$post['id_post']; ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required
                   value="<?php echo htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div>
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <?php foreach ($resultado_categorias as $cat): ?>
                    <option value="<?php echo (int)$cat['id_categoria']; ?>"
                        <?php echo ((int)$cat['id_categoria'] === (int)$post['id_categoria']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="etiquetas">Etiquetas:</label>
            <input type="text" id="etiquetas" name="etiquetas"
                   placeholder="introduce etiquetas separadas por comas"
                   value="<?php echo htmlspecialchars($post['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="15" required><?php
                echo htmlspecialchars($post['contenido'] ?? '', ENT_QUOTES, 'UTF-8');
            ?></textarea>
        </div>

        <div>
            <label for="imagen_url">URL de la Imagen Destacada:</label>
            <input type="url" id="imagen_url" name="imagen_url"
                   value="<?php echo htmlspecialchars($post['imagen_destacada_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <button type="submit">Actualizar Post</button>
    </form>
</main>

<?php
// Si NO usas 'unsafe-inline' en CSP:
$nonceAttr = ($security->cspNonce())
    ? ' nonce="'.htmlspecialchars($security->cspNonce(), ENT_QUOTES, 'UTF-8').'"'
    : '';
?>

<!-- Configuración de TinyMCE-->
<script>
if (typeof tinymce !== 'undefined') {
  tinymce.init({
    selector: '#contenido',
    // Plugins útiles
    plugins: 'code link lists image media table autoresize paste',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code',
    menubar: false,
    height: 540,

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
