<?php
// search.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Capturar y validar término
$q = trim((string)($_GET['q'] ?? ''));
if ($q === '') {
    header('Location: /index.php');
    exit();
}
if (mb_strlen($q) > 120) { // límite defensivo
    $q = mb_substr($q, 0, 120);
}

// Título y meta
$page_title = 'Resultados para: ' . $q;
$meta_description = 'Resultados de búsqueda para "' . $q . '" en Anthropofilia.';
require_once __DIR__ . '/header.php';

// 2) Preparar LIKE escapando comodines (% y _)
$like = '%' . str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $q) . '%';

// 3) Paginación
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;

try {
    // Total
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM posts
         WHERE titulo LIKE :q ESCAPE '\\' OR contenido LIKE :q ESCAPE '\\'"
    );
    $countStmt->execute([':q' => $like]);
    $total = (int)$countStmt->fetchColumn();

    // Resultados página
    $stmt = $pdo->prepare(
        "SELECT id_post, titulo, fecha_publicacion
         FROM posts
         WHERE titulo LIKE :q ESCAPE '\\' OR contenido LIKE :q ESCAPE '\\'
         ORDER BY fecha_publicacion DESC, id_post DESC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue(':q', $like, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $security->logEvent('error', 'search_failed', ['q' => $q, 'error' => $e->getMessage()]);
    $results = [];
    $total   = 0;
}

$pages = max(1, (int)ceil($total / $perPage));
?>
<main>
  <h2>Resultados de búsqueda para: "<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>"</h2>
  <hr>

  <?php if ($total > 0): ?>
    <p>Se encontraron <?= (int)$total; ?> resultado(s).</p>

    <?php foreach ($results as $post): ?>
      <article>
        <h3>
          <a href="/post.php?id=<?= (int)$post['id_post']; ?>">
            <?= htmlspecialchars($post['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?>
          </a>
        </h3>
        <p>
          <?php
          $ts = isset($post['fecha_publicacion']) ? strtotime((string)$post['fecha_publicacion']) : false;
          echo $ts ? 'Publicado el: ' . date('d/m/Y', $ts) : 'Fecha no disponible';
          ?>
        </p>
      </article>
    <?php endforeach; ?>

    <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Paginación" style="margin-top:1rem;">
        <?php
        $base = '/search.php?q=' . urlencode($q) . '&';
        ?>
        <?php if ($page > 1): ?>
          <a href="<?= $base . 'page=' . ($page - 1); ?>">&laquo; Anterior</a>
        <?php endif; ?>
        <span>Página <?= $page; ?> de <?= $pages; ?></span>
        <?php if ($page < $pages): ?>
          <a href="<?= $base . 'page=' . ($page + 1); ?>">Siguiente &raquo;</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

  <?php else: ?>
    <p>No se encontraron resultados para tu búsqueda.</p>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/sidebar.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
