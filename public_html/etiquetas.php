<?php
// etiquetas.php — Listado completo de etiquetas
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Tag;

$showSidebar = true;
$categoria = null;
$page_title = 'Todas las etiquetas';
$meta_description = 'Listado completo de etiquetas en Anthropofilia.';

try {
    $tags = Tag::getMostUsed(100); // traer todas con al menos 1 post
} catch (Throwable $e) {
    error_log("Error cargando etiquetas: " . $e->getMessage());
    $tags = [];
}

require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<div class="main-content-area container">
    <main>
        <nav class="breadcrumbs" aria-label="Breadcrumbs">
            <a href="<?= url('index.php') ?>">Inicio</a>
            <span aria-hidden="true">›</span>
            <span aria-current="page">Etiquetas</span>
        </nav>

        <header class="search-header">
            <h1>Todas las etiquetas</h1>
        </header>

        <?php if ($tags): ?>
            <?php
            $counts = array_column($tags, 'total');
            $min = min($counts);
            $max = max($counts);
            ?>
            <div class="tag-cloud tag-cloud--page">
                <?php foreach ($tags as $tag):
                    $count = (int) $tag['total'];
                    if ($min === $max) {
                        $level = 3;
                    } else {
                        $level = (int) ceil((($count - $min) / ($max - $min)) * 4) + 1;
                    }
                    $name = e($tag['nombre_etiqueta']);
                ?>
                    <a href="<?= url('etiqueta.php?tag=' . urlencode($tag['nombre_etiqueta'])) ?>"
                       class="tag-item tag-item--level-<?= $level ?>"
                       title="<?= $count ?> <?= pluralize($count, 'entrada', 'entradas') ?>">
                        <?= $name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="search-empty">
                <p class="search-empty__message">No hay etiquetas todavía.</p>
                <a href="<?= url('index.php') ?>" class="btn">← Volver al inicio</a>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
