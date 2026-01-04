<?php
require_once __DIR__ . '/init.php';

// 1) Obtener y validar slug
$slug = trim($security->cleanInput($_GET['slug'] ?? ''));
if ($slug === '') {
    header("Location: index.php");
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2) Cargar la categoría por slug (404 si no existe)
    $stmtCat = $pdo->prepare("SELECT id_categoria, nombre_categoria, slug FROM categorias WHERE slug = ? LIMIT 1");
    $stmtCat->execute([$slug]);
    $categoria = $stmtCat->fetch(PDO::FETCH_ASSOC);

    if ($categoria && $slug !== $categoria['slug']) {
        // 301 a la forma canónica del slug
        header("Location: " . url('categoria.php?slug=' . urlencode($categoria['slug'])), true, 301);
        exit;
    }

    if (!$categoria) {
        http_response_code(404);
        $page_title = 'Categoría no encontrada';
        require_once BASE_PATH . '/resources/views/partials/header.php';
        echo '<main class="container"><h1>Categoría no encontrada</h1><p>La categoría solicitada no existe.</p></main>';
        require_once BASE_PATH . '/resources/views/partials/footer.php';
        exit();
    }

    // 3) Paginación básica
    $perPage = 10;
    $page    = max(1, (int)$security->cleanInput($_GET['page'] ?? '1', 'int'));
    $offset  = ($page - 1) * $perPage;

    // 4) Traer posts de la categoría (con slug y limit/offset interpolados)
    $limitPlus = $perPage + 1; // para calcular hasMore (opcional)
    $sql = "SELECT id_post, slug, titulo, fecha_publicacion
            FROM posts
            WHERE id_categoria = :cat
            ORDER BY fecha_publicacion DESC
            LIMIT $limitPlus OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cat', (int)$categoria['id_categoria'], PDO::PARAM_INT);
    $stmt->execute();
    $rows    = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasMore = count($rows) > $perPage;
    $posts   = array_slice($rows, 0, $perPage);

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('error','category_query_failed',['slug'=>$slug,'error'=>$e->getMessage()]);
    die('Error al cargar la categoría.');
}

// 5) Render
$page_title = 'Categoría: ' . ($categoria['nombre_categoria'] ?? '');
$meta_description = 'Entradas de la categoría ' . ($categoria['nombre_categoria'] ?? '');

require_once BASE_PATH . '/resources/views/partials/header.php';
?>
<main class="container">
  <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <span aria-current="page"><?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></span>
  </nav>

  <h1>Categoría: <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></h1>
  <hr>

  <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
      <article>
        <h2>
          <?php if (!empty($post['slug'])): ?>
            <a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>">
              <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php else: ?>
            <a href="<?= url('post.php?id=' . (int)$post['id_post']) ?>">
              <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          <?php endif; ?>
        </h2>
        <?php $ts = strtotime($post['fecha_publicacion'] ?? '') ?: time(); ?>
        <p>
          Publicado el:
          <time datetime="<?= htmlspecialchars(date('c', $ts), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars(date('d/m/Y', $ts), ENT_QUOTES, 'UTF-8') ?>
          </time>
        </p>
      </article>
    <?php endforeach; ?>

    <!-- Paginación simple -->
    <nav class="pager" aria-label="Paginación">
      <ul>
        <?php if ($page > 1): ?>
          <li><a href="?slug=<?= urlencode($categoria['slug']) ?>&page=<?= $page-1 ?>">« Anteriores</a></li>
        <?php endif; ?>
        <?php if ($hasMore): ?>
          <li><a href="?slug=<?= urlencode($categoria['slug']) ?>&page=<?= $page+1 ?>">Siguientes »</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php else: ?>
    <p>No se encontraron posts en esta categoría.</p>
  <?php endif; ?>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
