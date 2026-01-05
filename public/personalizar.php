<?php
// public/personalizar.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// Auth
$security->requireLogin();

// Obtener configuración actual
$config = [
    'primary_color' => get_setting($pdo, 'theme_primary_color', '#0645ad'),
    'bg_color' => get_setting($pdo, 'theme_bg_color', '#ffffff'),
    'header_bg_url' => get_setting($pdo, 'header_bg_url', ''),
];

// Mensajes de estado
$status = $_GET['status'] ?? '';
$message = '';
if ($status === 'success') {
    $message = '<p class="status-success">✓ Configuración guardada correctamente.</p>';
} elseif ($status === 'error') {
    $message = '<p class="status-error">✗ Error al guardar la configuración.</p>';
}

$page_title = 'Personalizar Diseño';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a> 
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Dashboard</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Personalizar Diseño</span>
    </nav>

    <h1>🎨 Personalizar Diseño</h1>

    <?php if ($message): ?>
        <div aria-live="polite"><?= $message ?></div>
    <?php endif; ?>

    <form action="<?= url('guardar_personalizacion.php') ?>" method="post" enctype="multipart/form-data" class="form-container">
        <?= $security->csrfField() ?>

        <!-- SECCIÓN: Colores -->
        <fieldset style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <legend style="font-weight: bold; font-size: 1.2rem;">Colores del Tema</legend>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <label for="primary_color">Color principal (enlaces, botones)</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input 
                            type="color" 
                            id="primary_color" 
                            name="primary_color" 
                            value="<?= htmlspecialchars($config['primary_color'], ENT_QUOTES, 'UTF-8') ?>"
                            style="width: 60px; height: 40px; border: 1px solid #ccc; cursor: pointer;">
                        <input 
                            type="text" 
                            value="<?= htmlspecialchars($config['primary_color'], ENT_QUOTES, 'UTF-8') ?>"
                            readonly
                            style="flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #f5f5f5;">
                    </div>
                    <small>Predeterminado: #0645ad</small>
                </div>

                <div>
                    <label for="bg_color">Color de fondo</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input 
                            type="color" 
                            id="bg_color" 
                            name="bg_color" 
                            value="<?= htmlspecialchars($config['bg_color'], ENT_QUOTES, 'UTF-8') ?>"
                            style="width: 60px; height: 40px; border: 1px solid #ccc; cursor: pointer;">
                        <input 
                            type="text" 
                            value="<?= htmlspecialchars($config['bg_color'], ENT_QUOTES, 'UTF-8') ?>"
                            readonly
                            style="flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; background: #f5f5f5;">
                    </div>
                    <small>Predeterminado: #ffffff</small>
                </div>
            </div>
        </fieldset>

        <!-- SECCIÓN: Imagen de Cabecera -->
        <fieldset style="border: 1px solid #ddd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
            <legend style="font-weight: bold; font-size: 1.2rem;">Imagen de Cabecera</legend>

            <?php if (!empty($config['header_bg_url'])): ?>
                <div style="margin-bottom: 1rem;">
                    <p><strong>Imagen actual:</strong></p>
                    <img 
                        src="<?= htmlspecialchars($config['header_bg_url'], ENT_QUOTES, 'UTF-8') ?>" 
                        alt="Imagen de cabecera actual" 
                        style="max-width: 100%; height: auto; border: 2px solid #ddd; border-radius: 8px;">
                    <label style="display: block; margin-top: 1rem;">
                        <input type="checkbox" name="remove_header_image" value="1">
                        Eliminar imagen actual
                    </label>
                </div>
            <?php endif; ?>

            <div>
                <label for="header_image">Subir nueva imagen de cabecera</label>
                <input 
                    type="file" 
                    id="header_image" 
                    name="header_image" 
                    accept="image/jpeg,image/png,image/gif,image/webp">
                <small>Máximo 2MB. Recomendado: 1200x300px. Formatos: JPG, PNG, GIF, WebP</small>
            </div>
        </fieldset>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="submit" style="padding: 0.75rem 2rem;">💾 Guardar Cambios</button>
            <a href="<?= url('dashboard.php') ?>" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Cancelar</a>
            <button 
                type="button" 
                onclick="if(confirm('¿Restaurar valores predeterminados?')) location.href='<?= url('guardar_personalizacion.php?reset=1') ?>'"
                style="margin-left: auto; padding: 0.75rem 1.5rem; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                🔄 Restablecer
            </button>
        </div>
    </form>
</main>

<script>
// Actualizar el texto del color cuando cambia el picker
document.getElementById('primary_color').addEventListener('input', function(e) {
    e.target.nextElementSibling.value = e.target.value.toUpperCase();
});

document.getElementById('bg_color').addEventListener('input', function(e) {
    e.target.nextElementSibling.value = e.target.value.toUpperCase();
});
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>