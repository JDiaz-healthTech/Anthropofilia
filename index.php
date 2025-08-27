<?php
// index.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$page_title = 'Página de inicio';
$meta_description = 'Últimas publicaciones de Anthropofilia.';
require_once __DIR__ . '/header.php';

// --- Parámetros de paginación ---
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

// --- Total de posts (para paginación) ---
try {
    $total = (int)$pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();

    // --- Listado de posts (solo campos necesarios) ---
    $sql = "SELECT id_post, titulo, fecha_publicacion
            FROM posts
            ORDER BY fecha_publicacion DESC, id_post DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $security->logEvent('error', 'home_query_failed', ['error' => $e->getMessage()]);
    $posts = [];
    $total = 0;
}

$pages = max(1, (int)ceil($total / $perPage));
?>
<main class="home-list">
  <?php if ($posts): ?>
    <?php foreach ($posts as $post): ?>
      <article class="post-card">
        <h2 class="post-title">
<a href="post.php?id=<?= (int)$post['id_post'] ?>">
            <?php echo htmlspecialchars($post['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?>
          </a>
        </h2>
        <p class="post-meta">
          <?php
          $ts = isset($post['fecha_publicacion']) ? strtotime((string)$post['fecha_publicacion']) : false;
          echo $ts ? 'Publicado el ' . date('d/m/Y', $ts) : 'Fecha no disponible';
          ?>
        </p>
      </article>
      <hr>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No hay posts para mostrar.</p>
  <?php endif; ?>

  <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Paginación">
      <?php
      $base = '/index.php?';
      // conserva otros filtros si los añades en el futuro
      ?>
      <?php if ($page > 1): ?>
        <a href="<?php echo $base . 'page=' . ($page - 1); ?>">&laquo; Anterior</a>
      <?php endif; ?>
      <span>Página <?php echo $page; ?> de <?php echo $pages; ?></span>
      <?php if ($page < $pages): ?>
        <a href="<?php echo $base . 'page=' . ($page + 1); ?>">Siguiente &raquo;</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/sidebar.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
