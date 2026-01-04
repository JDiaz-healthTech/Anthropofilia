<?php
// public/index.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Post;

// 1. LÓGICA
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10; // Sigue en 1 para tus pruebas (luego ponlo a 10)
$offset  = ($page - 1) * $perPage;

try {
    $totalPosts = Post::countAll();
    $posts      = Post::getPaginated($perPage, $offset);
    $totalPages = max(1, (int)ceil($totalPosts / $perPage));
    
} catch (Exception $e) {
    error_log("Error cargando portada: " . $e->getMessage());
    $posts = [];
    $totalPages = 1;
}

$page_title = 'Página de inicio';
$meta_description = 'Últimas publicaciones de Anthropofilia.';

// 2. VISTA - HEADER
// (El header YA ABRE el div class="main-content-area", no hace falta abrirlo de nuevo)
require_once BASE_PATH . '/resources/views/partials/header.php'; 
?>

<main class="home-list">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <h2 class="post-title">
                        <a href="<?= url('post.php?id=' . $post['id_post']) ?>">
                            <?= htmlspecialchars($post['titulo']) ?>
                        </a>
                    </h2>
                    <p class="post-meta">
                        <?php 
                            $fecha = strtotime($post['fecha_publicacion']);
                            echo $fecha ? 'Publicado el ' . date('d/m/Y', $fecha) : ''; 
                        ?>
                    </p>
                </article>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-posts">No hay publicaciones disponibles.</p>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
                <?php if ($page > 1): ?>
                    <a href="<?= url('index.php?page=' . ($page - 1)) ?>" class="btn-pag">&laquo; Anterior</a>
                <?php endif; ?>

                <span class="current-page" style="align-self: center;">Página <?= $page ?> de <?= $totalPages ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('index.php?page=' . ($page + 1)) ?>" class="btn-pag">Siguiente &raquo;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </main>

 

<?php 
// 4. FOOTER
require_once BASE_PATH . '/resources/views/partials/footer.php'; 
?>