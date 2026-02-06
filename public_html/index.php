<?php
// public/index.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Post;

$showSidebar = true;

// 1. LÓGICA
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

try {
    $totalPosts = Post::countAll();
    $posts      = Post::getPaginated($perPage, $offset);
    $totalPagesCount = max(1, (int)ceil($totalPosts / $perPage));
} catch (Exception $e) {
    error_log("Error cargando portada: " . $e->getMessage());
    $posts = [];
    $totalPagesCount = 1;
}

$page_title = 'Página de inicio';
$meta_description = 'Últimas publicaciones de Anthropofilia.';

require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="home-list">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post):
// Generar extracto limpio (ignorar si solo hay HTML/iframes)
$textoLimpio = trim(strip_tags($post['contenido'] ?? ''));
$extracto = mb_strlen($textoLimpio) > 20 ? mb_substr($textoLimpio, 0, 150) . '...' : '';

            // URL del post
            $postUrl = url('post.php?slug=' . urlencode($post['slug'] ?? '') . '&id=' . $post['id_post']);
        ?>
            <article class="post-card">
                <?php if (!empty($post['imagen_destacada_url'])): ?>
                    <div class="post-card__image">
                        <a href="<?= $postUrl ?>">
                            <img src="<?= htmlspecialchars($post['imagen_destacada_url'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                 loading="lazy">
                        </a>
                    </div>
                <?php endif; ?>

                <h2>
                    <a href="<?= $postUrl ?>">
                        <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>

                <div class="post-meta">
                    <?php if (!empty($post['nombre_categoria'])): ?>
                        <span class="categoria"><?= htmlspecialchars($post['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                    <time datetime="<?= date('Y-m-d', strtotime($post['fecha_publicacion'])) ?>">
                        <?= date('d/m/Y', strtotime($post['fecha_publicacion'])) ?>
                    </time>
                </div>

                <?php if ($extracto): ?>
                    <p class="post-card__excerpt">
                        <?= htmlspecialchars($extracto, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <a href="<?= $postUrl ?>" class="post-card__link">Leer más</a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-posts">No hay publicaciones disponibles.</p>
    <?php endif; ?>

    <?php if ($totalPagesCount > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= url('index.php?page=' . ($page - 1)) ?>" class="pagination__prev">&laquo; Anterior</a>
            <?php endif; ?>

            <span class="pagination__info">Página <?= $page ?> de <?= $totalPagesCount ?></span>

            <?php if ($page < $totalPagesCount): ?>
                <a href="<?= url('index.php?page=' . ($page + 1)) ?>" class="pagination__next">Siguiente &raquo;</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
