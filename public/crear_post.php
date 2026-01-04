<?php
// public/crear_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// PRG: datos previos si hubo error al guardar
$old = $_SESSION['form_post'] ?? [];
unset($_SESSION['form_post']);

// CSRF
$csrf = $security->csrfToken();

// Cargar categorías
$categorias = [];
try {
    $sql = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Error cargando categorías: " . $e->getMessage());
    $categorias = [];
}

$page_title = 'Crear Nuevo Post';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a> 
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Dashboard</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Crear Post</span>
    </nav>

    <h1>Crear nuevo post</h1>

    <form action="<?= url('guardar_post.php') ?>" method="post" enctype="multipart/form-data" class="form-container">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

        <div>
            <label for="titulo">Título</label>
            <input
                type="text" 
                id="titulo" 
                name="titulo"
                required 
                maxlength="150" 
                autocomplete="off"
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
            <label for="contenido">Contenido</label>
            <textarea 
                id="contenido" 
                name="contenido" 
                rows="20" 
                required 
                maxlength="100000"><?= htmlspecialchars($old['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div>
            <label for="imagen">Imagen destacada (opcional)</label>
            <input
                type="file" 
                id="imagen" 
                name="imagen"
                accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Máximo 2MB. Formatos: JPG, PNG, GIF, WebP</small>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit">Guardar post</button>
            <a href="<?= url('dashboard.php') ?>">Cancelar</a>
        </div>
    </form>
</main>

<?php
// CSP Nonce
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
        branding: false,

        block_formats: 'Párrafo=p; Encabezado 2=h2; Encabezado 3=h3; Encabezado 4=h4; Cita=blockquote; Preformateado=pre',

        link_target_list: [
            { title: 'Nueva pestaña', value: '_blank' }, 
            { title: 'Misma pestaña', value: '' }
        ],
        rel_list: [
            { title: 'Ninguno', value: '' }, 
            { title: 'noopener', value: 'noopener' }, 
            { title: 'nofollow', value: 'nofollow' }
        ],
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
                xhr.setRequestHeader('X-CSRF-Token', '<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>');
                
                xhr.upload.onprogress = function (e) { 
                    if (e.lengthComputable) progress(e.loaded / e.total * 100); 
                };
                
                xhr.onload = function () {
                    if (xhr.status < 200 || xhr.status >= 300) { 
                        reject('HTTP ' + xhr.status); 
                        return; 
                    }
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location !== 'string') { 
                            reject('Respuesta inválida'); 
                            return; 
                        }
                        resolve(json.location);
                    } catch (err) { 
                        reject('JSON inválido'); 
                    }
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
        contextmenu: false,
        
        init_instance_callback: function (editor) {
            console.log('TinyMCE iniciado correctamente');
        }
    });
} else {
    console.error('TinyMCE no está cargado');
}
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>