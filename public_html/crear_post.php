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
// PRG: datos previos si hubo error al guardar
$old = $_SESSION['form_post'] ?? [];
$uploadError = $_SESSION['upload_error'] ?? null;
$dbError = $_SESSION['db_error'] ?? null;
unset($_SESSION['form_post'], $_SESSION['upload_error'], $_SESSION['db_error']);
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

// Mensajes de error
$errorMessage = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'invalid':
            $errorMessage = 'Por favor completa todos los campos obligatorios.';
            break;
        case 'invalid_category':
            $errorMessage = 'La categoría seleccionada no es válida.';
            break;
        case 'upload_error':
            $errorMessage = $uploadError ?? 'Error al subir la imagen.';
            break;
        case 'db_error':
            $errorMessage = 'Error al guardar el post. Por favor, inténtalo de nuevo.';
            break;
    }
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

    <?php if ($errorMessage): ?>
        <div class="alert alert-error" style="padding: 1rem; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 1.5rem; color: #721c24;">
            <strong>Error:</strong> <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form action="<?= url('guardar_post.php') ?>" method="post" enctype="multipart/form-data" class="form-container" novalidate id="formCrearPost">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

        <div>
            <label for="titulo">Título <span style="color: red;">*</span></label>
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
            <label for="id_categoria">Categoría <span style="color: red;">*</span></label>
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
            <label for="etiquetas">Etiquetas (opcional)</label>
            <input
                type="text" 
                id="etiquetas" 
                name="etiquetas"
                placeholder="Ej: php, programación, tutoriales (separadas por comas)"
                autocomplete="off"
                value="<?= htmlspecialchars($old['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <small>Separa las etiquetas con comas. Serán convertidas automáticamente a minúsculas.</small>
        </div>
        <div>
            <label for="contenido">Contenido <span style="color: red;">*</span></label>
            <textarea 
                id="contenido" 
                name="contenido" 
                rows="20" 
                required 
                maxlength="200000"><?= htmlspecialchars($old['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
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
        <div>
            <label for="imagen_url">O URL de imagen externa (opcional)</label>
            <input
                type="url" 
                id="imagen_url" 
                name="imagen_url"
                placeholder="https://ejemplo.com/imagen.jpg"
                value="<?= htmlspecialchars($old['imagen_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <small>Si subes un archivo, esta URL será ignorada</small>
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

<script src="js/tinymce/tinymce.min.js"></script>

<script>
document.getElementById('formCrearPost').addEventListener('submit', function(e) {
    // 1. OBLIGATORIO: Volcar datos de TinyMCE al textarea real
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
    }

    // 2. VALIDAR: Comprobar que no esté vacío
    var contenido = document.getElementById('contenido').value.trim();
    
    if (contenido === '') {
        e.preventDefault(); // Frenar el envío
        alert('El contenido no puede estar vacío.');
        return false;
    }
    
    // 3. Validar que se haya seleccionado una categoría
    var categoria = document.getElementById('id_categoria').value;
    if (categoria === '') {
        e.preventDefault();
        alert('Debes seleccionar una categoría.');
        return false;
    }
});
</script>

<script>
// Obtenemos el token CSRF de la sesión de PHP para usarlo en JS
const csrfToken = "<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>"; 

if (typeof tinymce !== 'undefined') {
    tinymce.init({
        selector: 'textarea#contenido',
        
        // --- CORRECCIÓN DE RUTAS DE LOS ICONOS ---
        base_url: 'js/tinymce', // Importante: Sin barra inicial
        suffix: '.min',         // Optimización
        // ----------------------------------------

        plugins: 'code link lists image media table autoresize advlist',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | code',
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

                // --- CONFIGURACIÓN DE VÍDEOS EMBEBIDOS ---
        media_live_embeds: true,  // Preview en tiempo real
        media_alt_source: false,  // Simplifica el diálogo
        media_poster: false,      // Sin póster, más simple
        media_dimensions: false,  // Responsive por defecto

        // Filtros de URL para seguridad
        media_url_resolver: function (data, resolve) {
            // Detectar YouTube
            if (data.url.indexOf('youtube.com/watch') !== -1 || data.url.indexOf('youtu.be/') !== -1) {
                var videoId = '';
                if (data.url.indexOf('youtu.be/') !== -1) {
                    videoId = data.url.split('youtu.be/')[1].split(/[?&]/)[0];
                } else {
                    var match = data.url.match(/[?&]v=([^&]+)/);
                    videoId = match ? match[1] : '';
                }
                if (videoId) {
                    resolve({
                        html: '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                    });
                    return;
                }
            }
            
            // Detectar Vimeo
            if (data.url.indexOf('vimeo.com/') !== -1) {
                var vimeoId = data.url.split('vimeo.com/')[1].split(/[?&]/)[0];
                if (vimeoId) {
                    resolve({
                        html: '<iframe src="https://player.vimeo.com/video/' + vimeoId + '" width="560" height="315" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>'
                    });
                    return;
                }
            }
            
            // Si no es YouTube ni Vimeo, dejar que TinyMCE lo maneje
            resolve({ html: '' });
        },

        // --- GESTIÓN DE SUBIDA DE IMÁGENES ---
        // Nota: Debes crear el archivo 'upload_image.php' para que esto funcione
        images_upload_url: 'upload_image.php', 
        images_upload_credentials: true,
        automatic_uploads: true,
        image_caption: true,
        image_dimensions: false,
        image_class_list: [
            { title: 'Por defecto', value: '' },
            { title: 'Ancho completo', value: 'img-wide' }
        ],

        // Handler personalizado para subidas AJAX con seguridad CSRF
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'upload_image.php'); // Asegúrate de crear este archivo
                xhr.withCredentials = true;
                
                // Inyectamos el token CSRF en la cabecera
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                
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
                            reject('Respuesta inválida: ' + xhr.responseText); 
                            return; 
                        }
                        resolve(json.location);
                    } catch (err) { 
                        reject('JSON inválido: ' + err.message); 
                    }
                };
                
                xhr.onerror = function () { reject('Error de red'); };
                
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                // También enviamos el token por POST por si acaso
                formData.append('csrf_token', csrfToken);
                
                xhr.send(formData);
            });
        },

        paste_data_images: false,

        // --- INSTRUCCIONES PARA IMÁGENES CON ENLACES ---
        // Las imágenes con enlaces ya funcionan nativamente en TinyMCE:
        // 1. Sube o inserta una imagen
        // 2. Selecciona la imagen en el editor
        // 3. Haz clic en el botón "link" (icono de cadena) en la toolbar
        // 4. Pega la URL (ej: https://calameo.com/tu-documento)
        // 5. Elige si abrir en nueva pestaña
        // La imagen quedará clicable automáticamente

        // --- ESTILOS DENTRO DEL EDITOR ---
        // Usamos style.css que ya tienes subido
        content_css: ['style.css'], 
        content_style: `
            body { max-width: 760px; margin: 1rem auto; line-height: 1.7; font-family: Helvetica, Arial, sans-serif; color: #333; }
            figure { margin: 1.2rem 0; }
            figcaption { font-size: .9rem; opacity: .8; text-align: center; }
            img { border-radius: 6px; max-width: 100%; height: auto; }
            blockquote { border-left: 4px solid #8a4; padding:.6rem 1rem; background:rgba(0,0,0,.03); font-style: italic; }
            table { border-collapse: collapse; width: 100%; }
            table, th, td { border: 1px solid #ddd; }
            th, td { padding: .5rem; }
            ul, ol { margin-left: 1.2rem; }
            pre, code { font-family: monospace; background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
                /* Vídeos responsive */
            iframe {
                max-width: 100%;
                height: auto;
                aspect-ratio: 16/9;
                border-radius: 8px;
                margin: 1.5rem 0;
            }
            
            /* Imágenes con enlaces - efecto hover */
            a img {
                transition: opacity 0.2s ease, transform 0.2s ease;
            }
            
            a:hover img {
                opacity: 0.85;
                transform: scale(1.02);
            }
        `,

        browser_spellcheck: true,
        contextmenu: false,
        
        init_instance_callback: function (editor) {
            console.log('TinyMCE iniciado correctamente con configuración completa');
        }
    });
} else {
    console.error('TinyMCE no está cargado. Revisa la ruta src del script.');
}
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>