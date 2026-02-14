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
// Generar extracto limpio
$textoLimpio = strip_tags($post['contenido'] ?? '');
$textoLimpio = html_entity_decode($textoLimpio, ENT_QUOTES | ENT_HTML5, 'UTF-8');
$textoLimpio = preg_replace('/\s+/u', ' ', $textoLimpio);
$textoLimpio = trim($textoLimpio);
$extracto = mb_strlen($textoLimpio) > 20 ? mb_substr($textoLimpio, 0, 150) . '…' : '';

            // URL del post
            $postUrl = url('post.php?slug=' . urlencode($post['slug'] ?? '') . '&id=' . $post['id_post']);

                // Obtener URL de imagen y tipo de contenido
                $thumb = get_thumbnail_url($post['imagen_destacada_url'] ?? null, $post['contenido'] ?? null);
                $imagenUrl = $thumb['url'];
                $thumbType = $thumb['type'];
        ?>
          <article class="post-card">
                <?php if (!empty($imagenUrl)): ?>
                    <div class="post-card__image">
                        <a href="<?= $postUrl ?>">
                            <img src="<?= htmlspecialchars($imagenUrl, ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                 loading="lazy">
                        </a>
                    </div>
                <?php elseif ($thumbType !== 'none'): ?>
                    <div class="post-card__image post-card__placeholder post-card__placeholder--<?= $thumbType ?>">
                        <a href="<?= $postUrl ?>">
                            <span class="placeholder-icon"><?= match($thumbType) {
                                'genially'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>',
                                'calameo'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="8" y1="7" x2="16" y2="7"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
                                'vimeo'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>',
                                default     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
                            } ?></span>
                            <span class="placeholder-label"><?= match($thumbType) {
                                'genially'  => 'Contenido interactivo',
                                'calameo'   => 'Publicación digital',
                                'vimeo'     => 'Vídeo',
                                default     => 'Recurso externo'
                            } ?></span>
                        </a>
                    </div>
                <?php endif; ?>

                <div class="post-card__body">
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
                        <?php if ($thumbType === 'youtube'): ?>
                            <span class="post-meta__type" title="Vídeo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/><polygon points="10 12 16 15 10 18 10 12"/></svg></span>
                        <?php elseif ($thumbType === 'genially'): ?>
                            <span class="post-meta__type" title="Contenido interactivo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg></span>
                        <?php elseif ($thumbType === 'calameo'): ?>
                            <span class="post-meta__type" title="Publicación digital"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($extracto): ?>
                        <p class="post-card__excerpt">
                            <?= htmlspecialchars($extracto, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>

                    <a href="<?= $postUrl ?>" class="post-card__link">Leer más</a>
                </div>
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
