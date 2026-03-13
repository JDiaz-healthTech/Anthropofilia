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
    'overlay_opacity' => (int)get_setting($pdo, 'header_overlay_opacity', '50'),
    'bg_history' => json_decode(get_setting($pdo, 'header_bg_history', '[]'), true) ?: [],
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
$extra_css = 'css/pages/personalizar.css';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Panel de Control</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Personalizar Diseño</span>
    </nav>

    <h1>🎨 Personalizar Diseño</h1>

    <?php if ($message): ?>
        <div aria-live="polite"><?= $message ?></div>
    <?php endif; ?>

    <form action="<?= url('guardar_personalizacion.php') ?>" method="post" enctype="multipart/form-data" class="form-container" id="personalizar-form">
        <?= $security->csrfField() ?>

        <!-- SECCIÓN: Imagen de Cabecera -->
        <fieldset class="pz-fieldset">
            <legend class="pz-legend">Imagen de Cabecera</legend>

            <!-- Preview en vivo -->
            <div class="pz-preview" id="header-preview">
                <div class="pz-preview__overlay" id="preview-overlay"></div>
                <div class="pz-preview__content">
                    <span class="pz-preview__title">ANTHROPOFILIA</span>
                    <span class="pz-preview__subtitle">Ana López Sampedro</span>
                </div>
                <?php if (empty($config['header_bg_url'])): ?>
                    <p class="pz-preview__empty">Sin imagen de cabecera</p>
                <?php endif; ?>
            </div>

            <!-- Subir nueva imagen -->
            <div class="pz-upload">
                <label for="header_image" class="pz-upload__label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Seleccionar imagen
                </label>
                <input type="file" id="header_image" name="header_image"
                       accept="image/jpeg,image/png,image/gif,image/webp" class="pz-upload__input">
                <span class="pz-upload__info" id="file-info">Máximo 2MB · JPG, PNG, GIF, WebP</span>
            </div>

            <?php if (!empty($config['header_bg_url'])): ?>
                <label class="pz-checkbox">
                    <input type="checkbox" name="remove_header_image" value="1">
                    Eliminar imagen actual
                </label>
            <?php endif; ?>

            <!-- Slider de oscurecimiento -->
            <div class="pz-slider-group">
                <label for="header_overlay_opacity" class="pz-slider-group__label">
                    Oscurecimiento del overlay
                    <span class="pz-slider-group__value" id="overlay-value"><?= $config['overlay_opacity'] ?>%</span>
                </label>
                <input type="range" id="header_overlay_opacity" name="header_overlay_opacity"
                       min="0" max="100" step="5"
                       value="<?= $config['overlay_opacity'] ?>"
                       class="pz-slider">
                <div class="pz-slider-group__hints">
                    <span>Sin overlay</span>
                    <span>Muy oscuro</span>
                </div>
            </div>

            <!-- Historial de fondos -->
            <?php if (!empty($config['bg_history'])): ?>
                <div class="pz-history">
                    <p class="pz-history__title">Fondos anteriores</p>
                    <div class="pz-history__grid">
                        <?php foreach ($config['bg_history'] as $bgPath): ?>
                            <?php if (file_exists(__DIR__ . '/' . $bgPath)): ?>
                                <button type="submit" name="restore_from_history"
                                        value="<?= htmlspecialchars($bgPath, ENT_QUOTES, 'UTF-8') ?>"
                                        class="pz-history__item"
                                        title="Restaurar este fondo">
                                    <img src="<?= htmlspecialchars($bgPath, ENT_QUOTES, 'UTF-8') ?>"
                                         alt="Fondo anterior" loading="lazy">
                                </button>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </fieldset>

        <!-- SECCIÓN: Colores (deshabilitada) -->
        <fieldset class="pz-fieldset pz-fieldset--disabled" disabled>
            <legend class="pz-legend">Colores del Tema <span class="pz-badge">Próximamente</span></legend>

            <div class="pz-color-grid">
                <div class="pz-color-field">
                    <label for="primary_color">Color principal</label>
                    <div class="pz-color-picker">
                        <input type="color" id="primary_color" name="primary_color"
                               value="<?= htmlspecialchars($config['primary_color'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="pz-color-picker__hex"><?= $config['primary_color'] ?></span>
                    </div>
                </div>
                <div class="pz-color-field">
                    <label for="bg_color">Color de fondo</label>
                    <div class="pz-color-picker">
                        <input type="color" id="bg_color" name="bg_color"
                               value="<?= htmlspecialchars($config['bg_color'], ENT_QUOTES, 'UTF-8') ?>">
                        <span class="pz-color-picker__hex"><?= $config['bg_color'] ?></span>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Acciones -->
        <div class="pz-actions">
            <button type="submit" class="pz-btn pz-btn--primary">Guardar Cambios</button>
            <a href="<?= url('dashboard.php') ?>" class="pz-btn pz-btn--secondary">Cancelar</a>
        </div>
    </form>

    <form method="POST" action="<?= url('guardar_personalizacion.php') ?>"
          onsubmit="return confirm('¿Restaurar valores predeterminados?')">
        <?= $security->csrfField() ?>
        <input type="hidden" name="action" value="reset">
        <button type="submit" class="pz-btn pz-btn--danger">🔄 Restablecer</button>
    </form>
</main>

<script src="<?= url('js/personalizar.js') ?>" defer></script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
