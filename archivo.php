<?php
require_once __DIR__ . '/init.php';

// 1) Obtener y validar año/mes
$anio = (int)$security->cleanInput($_GET['anio'] ?? '', 'int');
$mes  = (int)$security->cleanInput($_GET['mes']  ?? '', 'int');

if ($anio < 1970 || $anio > 2100 || $mes < 1 || $mes > 12) {
    header("Location: index.php");
    exit();
}

// 2) Rango de fechas (consulta sargable)
$start = (new DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $anio, $mes)));
$end   = $start->modify('first day of next month');

$meses = [1=>'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$mesNombre = $meses[$mes];
$page_title = "Archivo: {$mesNombre} {$anio}";
$meta_description = "Entradas publicadas en {$mesNombre} de {$anio}."; // si tu header lo usa
  $categoria = null; // para migas condicionales
  require_once __DIR__.'/header.php';

// 3) Paginación básica
$perPage = 10;
$page    = max(1, (int)$security->cleanInput($_GET['page'] ?? '1', 'int'));
$offset  = ($page - 1) * $perPage;

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta sargable (aprovecha índice en fecha_publicacion)
    $sql = "SELECT id_post, titulo, fecha_publicacion
            FROM posts
            WHERE fecha_publicacion >= :start
              AND fecha_publicacion <  :end
            ORDER BY fecha_publicacion DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start', $start->format('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt->bindValue(':end',   $end->format('Y-m-d H:i:s'),   PDO::PARAM_STR);
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset',$offset,  PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('error','archive_query_failed',['error'=>$e->getMessage()]);
    die('Error al cargar el archivo.');
}
?>
<main class="container">
  <h1>Archivo de posts de: <?= htmlspecialchars($mesNombre . ' ' . $anio, ENT_QUOTES, 'UTF-8') ?></h1>
  <hr>
<?php if (!empty($posts)): ?>
  <?php foreach ($posts as $post): ?>
    <article>
      <h2>
        <?php if (!empty($post['slug'])): ?>
          <a href="post.php?slug=<?= urlencode($post['slug']) ?>">
            <?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        <?php else: ?>
          <a href="post.php?id=<?= (int)$post['id_post'] ?>">
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
    <!-- Navegación simple de páginas -->
    <nav class="pager" aria-label="Paginación">
      <ul>
        <?php if ($page > 1): ?>
          <li><a href="?anio=<?= $anio ?>&mes=<?= $mes ?>&page=<?= $page-1 ?>">« Anteriores</a></li>
        <?php endif; ?>
        <?php if (count($posts) === $perPage): ?>
          <li><a href="?anio=<?= $anio ?>&mes=<?= $mes ?>&page=<?= $page+1 ?>">Siguientes »</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  <?php else: ?>
    <p>No se encontraron posts para esta fecha.</p>
  <?php endif; ?>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
