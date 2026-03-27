<?php
// public/crear_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// Auth
$security->requireLogin();

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
        $errorMessage = $dbError ?? 'Error al guardar el post. Por favor, inténtalo de nuevo.';
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
        <a href="<?= url('dashboard.php') ?>">Panel de Control</a>
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

<!-- CSS y plugin de contenido externo -->
<link rel="stylesheet" href="<?= url('css/components/external-embed-modal.css') ?>">
<script src="<?= url('js/tinymce-external-embed.js') ?>"<?= $nonceAttr ?>></script>

<script>
  initTinyMCE({
    csrfToken: '<?= htmlspecialchars($csrf, ENT_QUOTES, "UTF-8") ?>',
    uploadUrl: 'upload_image.php',
    contentCss: 'style.css',
    type: 'post'
  });
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
