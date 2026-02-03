<?php
/**
 * search.php - Página de resultados de búsqueda
 */
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Post;

// ============================================================
// CONFIGURACIÓN DE PÁGINA
// ============================================================

$showSidebar = true;
$categoria = null;

// ============================================================
// CAPTURAR Y VALIDAR TÉRMINO DE BÚSQUEDA
// ============================================================

$q = trim((string) ($_GET['q'] ?? ''));

if ($q === '') {
    header('Location: ' . url('index.php'));
    exit();
}

if (mb_strlen($q, 'UTF-8') > 120) {
    $q = mb_substr($q, 0, 120, 'UTF-8');
}

// ============================================================
// PAGINACIÓN
// ============================================================

$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

// ============================================================
// BÚSQUEDA EN BASE DE DATOS
// ============================================================

try {
    $total   = Post::countSearch($q);
    $results = Post::searchPaginated($q, $perPage, $offset);
} catch (Throwable $e) {
    $security->logEvent('error', 'search_failed', [
        'q'     => $q,
        'error' => $e->getMessage()
    ]);
    $results = [];
    $total   = 0;
}

$totalPages = max(1, (int) ceil($total / $perPage));

// ============================================================
// META INFORMACIÓN
// ============================================================

$page_title = 'Resultados para: ' . $q;
$meta_description = 'Resultados de búsqueda para "' . e($q) . '" en Anthropofilia.';

require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="search-results">

    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Búsqueda</span>
    </nav>

    <header class="search-header">
        <h1>Resultados de búsqueda</h1>
        <p class="search-query">
            Mostrando resultados para: <strong>"<?= e($q) ?>"</strong>
        </p>
    </header>

    <?php if ($total > 0): ?>

        <p class="search-count">
            Se <?= $total === 1 ? 'encontró' : 'encontraron' ?>
            <strong><?= $total ?></strong>
            <?= pluralize($total, 'resultado', 'resultados') ?>.
        </p>

        <section class="search-grid" aria-label="Resultados de búsqueda">
            <?php foreach ($results as $post): ?>
                <article class="search-card">
                    <h2 class="search-card__title">
                        <a href="<?= url(post_url($post)) ?>">
                            <?= e($post['titulo'] ?? '(Sin título)') ?>
                        </a>
                    </h2>

                    <p class="search-card__meta">
                        <time datetime="<?= e($post['fecha_publicacion'] ?? '') ?>">
                            Publicado el <?= format_date_es($post['fecha_publicacion'] ?? null) ?>
                        </time>
                    </p>

                    <?php $extracto = excerpt($post['contenido'] ?? '', 30); ?>
                    <?php if ($extracto): ?>
                        <p class="search-card__excerpt"><?= e($extracto) ?></p>
                    <?php endif; ?>

                    <a href="<?= url(post_url($post)) ?>" class="search-card__link">
                        Leer más →
                    </a>
                </article>
            <?php endforeach; ?>
        </section>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Paginación de resultados">
                <?php $baseUrl = url('search.php') . '?q=' . urlencode($q); ?>

                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>" class="pagination__prev">
                        « Anterior
                    </a>
                <?php endif; ?>

                <span class="pagination__info">Página <?= $page ?> de <?= $totalPages ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>" class="pagination__next">
                        Siguiente »
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

    <?php else: ?>

        <div class="search-empty">
            <p class="search-empty__message">
                No se encontraron resultados para tu búsqueda.
            </p>
            <div class="search-empty__suggestions">
                <h2>Sugerencias:</h2>
                <ul>
                    <li>Verifica que las palabras estén escritas correctamente</li>
                    <li>Prueba con términos más generales</li>
                    <li>Usa menos palabras en tu búsqueda</li>
                </ul>
            </div>
            <a href="<?= url('index.php') ?>" class="btn">← Volver al inicio</a>
        </div>

    <?php endif; ?>

</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
