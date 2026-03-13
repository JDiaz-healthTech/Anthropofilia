<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$showSidebar = true;

// 1. Obtener y validar la etiqueta desde la URL
$nombre_etiqueta = trim((string)($_GET['tag'] ?? ''));

if ($nombre_etiqueta === '') {
    header('Location: ' . url('index.php'));
    exit();
}

// 2. Consulta con slug para generar URLs canónicas
$sql = "SELECT p.id_post, p.slug, p.titulo, p.fecha_publicacion
        FROM posts AS p
        INNER JOIN post_etiquetas AS pe ON p.id_post = pe.id_post
        INNER JOIN etiquetas AS e ON pe.id_etiqueta = e.id_etiqueta
        WHERE e.nombre_etiqueta = :nombre_etiqueta
        ORDER BY p.fecha_publicacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':nombre_etiqueta' => $nombre_etiqueta]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title      = 'Etiqueta: ' . $nombre_etiqueta;
$meta_description = 'Entradas etiquetadas como "' . $nombre_etiqueta . '" en Anthropofilia.';

require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="search-results">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Etiqueta: <?= e($nombre_etiqueta) ?></span>
    </nav>

    <header class="search-header">
        <h1>Entradas etiquetadas como: <em><?= e($nombre_etiqueta) ?></em></h1>
    </header>

    <?php if ($posts): ?>
        <p class="search-count">
            <?= count($posts) === 1 ? 'Se encontró' : 'Se encontraron' ?>
            <strong><?= count($posts) ?></strong>
            <?= pluralize(count($posts), 'entrada', 'entradas') ?>.
        </p>

        <section class="search-grid" aria-label="Entradas con esta etiqueta">
            <?php foreach ($posts as $post): ?>
                <article class="search-card">
                    <h2 class="search-card__title">
                        <a href="<?= url(post_url($post)) ?>">
                            <?= e($post['titulo']) ?>
                        </a>
                    </h2>
                    <p class="search-card__meta">
                        <time datetime="<?= e($post['fecha_publicacion'] ?? '') ?>">
                            Publicado el <?= format_date_es($post['fecha_publicacion'] ?? null) ?>
                        </time>
                    </p>
                    <a href="<?= url(post_url($post)) ?>" class="search-card__link">Leer más →</a>
                </article>
            <?php endforeach; ?>
        </section>
    <?php else: ?>
        <div class="search-empty">
            <p class="search-empty__message">No se encontraron entradas con esta etiqueta.</p>
            <a href="<?= url('index.php') ?>" class="btn">← Volver al inicio</a>
        </div>
    <?php endif; ?>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>