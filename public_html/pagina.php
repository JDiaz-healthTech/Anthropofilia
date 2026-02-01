<?php
// pagina.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$showSidebar = true;

require_once __DIR__ . '/app/Models/PaginaPost.php';

use App\Models\PaginaPost;

// 1) Obtener y validar slug (solo a-z, 0-9 y guiones)
$slug = $_GET['slug'] ?? '';
if (!preg_match('/^[a-z0-9-]{1,120}$/', $slug)) {
    $security->abort(404, 'Página no encontrada.');
}

// 2) Consultar la tabla `paginas`
$stmt = $pdo->prepare('SELECT id_pagina, titulo, contenido FROM paginas WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pagina) {
    $security->abort(404, 'Página no encontrada.');
}

// 3) Cargar posts relacionados
$paginaPost = new PaginaPost($pdo);
$postsRelacionados = $paginaPost->getPostsInPagina((int)$pagina['id_pagina']);

// 4) Metas
$page_title = $pagina['titulo'] ?? 'Página';
$meta_description = mb_substr(
    trim(preg_replace('/\s+/', ' ', strip_tags($pagina['contenido'] ?? ''))),
    0, 160
);
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main>
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page"><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <article class="pagina">
        <h1><?= htmlspecialchars($pagina['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8') ?></h1>
        <hr>

        <!-- Contenido principal de la página -->
        <div class="pagina-contenido">
            <?= $security->sanitizeHTML($pagina['contenido'] ?? '') ?>
        </div>

        <!-- Posts relacionados -->
        <?php if (!empty($postsRelacionados)): ?>
            <section class="pagina-posts-relacionados">
                <h2>Artículos relacionados</h2>

                <?php foreach ($postsRelacionados as $post): ?>
                    <?php if ($post['tipo_visualizacion'] === 'embebido'): ?>
                        <!-- POST EMBEBIDO (contenido completo) -->
                        <article class="post-embebido">
                            <header class="post-embebido__header">
                                <h3>
                                    <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>">
                                        <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <div class="post-embebido__meta">
                                    <?php if ($post['nombre_categoria']): ?>
                                        <span class="categoria">📁 <?= htmlspecialchars($post['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <time datetime="<?= date('Y-m-d', strtotime($post['fecha_publicacion'])) ?>">
                                        <?= date('d/m/Y', strtotime($post['fecha_publicacion'])) ?>
                                    </time>
                                </div>
                            </header>

                            <?php if ($post['imagen_destacada_url']): ?>
                                <div class="post-embebido__imagen">
                                    <img src="<?= htmlspecialchars($post['imagen_destacada_url'], ENT_QUOTES, 'UTF-8') ?>"
                                         alt="<?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                         loading="lazy">
                                </div>
                            <?php endif; ?>

                            <div class="post-embebido__contenido">
                                <?= $security->sanitizeHTML($post['contenido']) ?>
                            </div>

                            <footer class="post-embebido__footer">
                                <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>" class="btn-link">
                                    Ver post original →
                                </a>
                            </footer>
                        </article>

                    <?php else: ?>
                        <!-- POST EN CARD (preview) -->
                        <article class="post-card-pagina">
                            <?php if ($post['imagen_destacada_url']): ?>
                                <div class="post-card-pagina__imagen">
                                    <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>">
                                        <img src="<?= htmlspecialchars($post['imagen_destacada_url'], ENT_QUOTES, 'UTF-8') ?>"
                                             alt="<?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                             loading="lazy">
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-card-pagina__contenido">
                                <h3>
                                    <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>">
                                        <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>

                                <div class="post-card-pagina__meta">
                                    <?php if ($post['nombre_categoria']): ?>
                                        <span class="categoria">📁 <?= htmlspecialchars($post['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                    <time datetime="<?= date('Y-m-d', strtotime($post['fecha_publicacion'])) ?>">
                                        <?= date('d/m/Y', strtotime($post['fecha_publicacion'])) ?>
                                    </time>
                                </div>

                                <div class="post-card-pagina__excerpt">
                                    <?php
                                    $excerpt = strip_tags($post['contenido']);
                                    $excerpt = mb_substr($excerpt, 0, 200);
                                    echo htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') . '...';
                                    ?>
                                </div>

                                <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>" class="btn btn-sm">
                                    Leer más →
                                </a>
                            </div>
                        </article>
                    <?php endif; ?>
                <?php endforeach; ?>

            </section>
        <?php endif; ?>
    </article>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
