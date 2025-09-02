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

    if (!$categoria) {
        http_response_code(404);
        $page_title = 'Categoría no encontrada';
        require_once __DIR__ . '/header.php';
        echo '<main class="container"><h1>Categoría no encontrada</h1><p>La categoría solicitada no existe.</p></main>';
        require_once __DIR__ . '/footer.php';
        exit();
    }

    // 3) Paginación básica
    $perPage = 10;
    $page    = max(1, (int)$security->cleanInput($_GET['page'] ?? '1', 'int'));
    $offset  = ($page - 1) * $perPage;

    // 4) Traer posts de la categoría
    $sql = "SELECT id_post, titulo, fecha_publicacion
            FROM posts
            WHERE id_categoria = :cat
            ORDER BY fecha_publicacion DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':cat',   (int)$categoria['id_categoria'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset',$offset,  PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('error','category_query_failed',['slug'=>$slug,'error'=>$e->getMessage()]);
    die('Error al cargar la categoría.');
}

// 5) Render
$page_title = 'Categoría: ' . $categoria['nombre_categoria'];
$meta_description = 'Entradas de la categoría ' . $categoria['nombre_categoria']; // si tu header lo usa
require_once __DIR__ . '/header.php';
?>
<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <?php if (!empty($categoria)): ?>
      <a href="<?= url('categoria.php?slug=' . urlencode($categoria['slug'])) ?>">
        <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
      </a> <span aria-hidden="true">›</span>
    <?php endif; ?>
    <span aria-current="page"><?= htmlspecialchars($page_title ?? 'Actual', ENT_QUOTES, 'UTF-8') ?></span>
  </nav>
  <h1>Categoría: <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?></h1>
  <hr>
  <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $post): ?>
      <article>
        <h2>
<a href="post.php?id=<?= (int)$post['id_post'] ?>">
            <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        </h2>
        <?php $ts = strtotime($post['fecha_publicacion']); ?>
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
        <?php if (count($posts) === $perPage): ?>
          <li><a href="?slug=<?= urlencode($categoria['slug']) ?>&page=<?= $page+1 ?>">Siguientes »</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php else: ?>
    <p>No se encontraron posts en esta categoría.</p>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/sidebar.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
