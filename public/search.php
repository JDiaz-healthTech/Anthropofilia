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
require_once BASE_PATH . '/resources/views/partials/header.php';

// 2) Preparar LIKE escapando comodines (% y _)
$like = '%' . str_replace(['\\','%','_'], ['\\\\','\\%','\\_'], $q) . '%';

// 3) Paginación
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;
try {
  // Normaliza término y escapa comodines para LIKE
  $raw = $q; // ya viene recortado/validado arriba
  $like = '%' . strtr($raw, [
    '\\' => '\\\\',
    '%'  => '\%',
    '_'  => '\_',
  ]) . '%';

  // COUNT
  $sqlCount = "SELECT COUNT(*)
               FROM posts
               WHERE (titulo LIKE :q ESCAPE '\\' OR contenido LIKE :q ESCAPE '\\')";
  $stmt = $pdo->prepare($sqlCount);
  $stmt->bindValue(':q', $like, PDO::PARAM_STR);
  $stmt->execute(); // <- FALTABA
  $total = (int)$stmt->fetchColumn();

  // LISTADO
  $sql = "SELECT id_post, slug, titulo, contenido, fecha_publicacion
          FROM posts
          WHERE (titulo LIKE :q ESCAPE '\\' OR contenido LIKE :q ESCAPE '\\')
          ORDER BY fecha_publicacion DESC, id_post DESC
          LIMIT :limit OFFSET :offset";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':q', $like, PDO::PARAM_STR);
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
  $results = $stmt->fetchAll(); // <- usa $results (antes era $rows)
} catch (PDOException $e) {
  $security->logEvent('error', 'search_failed', ['q' => $q, 'error' => $e->getMessage()]);
  $results = [];
  $total   = 0;
}

$pages = max(1, (int)ceil($total / $perPage));

// Helpers locales (no rompen nada fuera de esta página)
function format_es_date($iso): string {
  $ts = strtotime((string)$iso);
  return $ts ? date('d/m/Y', $ts) : 'Fecha no disponible';
}
function excerpt_plain($html, $maxWords=30): string {
  $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$html)));
  if ($plain === '') return '';
  $words = preg_split('/\s+/u', $plain);
  if (count($words) <= $maxWords) return $plain;
  return implode(' ', array_slice($words, 0, $maxWords)) . '…';
}
function post_link(array $p): string {
  // Prefiere slug; si no hay, cae a id
  if (!empty($p['slug'])) {
    return 'post.php?slug=' . urlencode($p['slug']);
  }
  return 'post.php?id=' . (int)$p['id_post'];
}
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
 

  <h2>Resultados de búsqueda para: "<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>"</h2>
  <hr>

  <?php if ($total > 0): ?>
    <p>Se encontraron <?= (int)$total; ?> resultado(s).</p>

    <!-- Tarjetas de listado -->
    <section class="cards" style="display:grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap:1rem;">
      <?php foreach ($results as $post): ?>
        <article class="card" style="border:1px solid rgba(0,0,0,.08); border-radius:.5rem; padding:1rem; background:var(--bg-content, #fff);">
          <h3 style="margin-top:0; font-size:1.1rem;">
            <a href="<?= htmlspecialchars(post_link($post), ENT_QUOTES, 'UTF-8'); ?>">
              <?= htmlspecialchars($post['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?>
            </a>
          </h3>
          <p style="margin:.25rem 0 .75rem; font-size:.9rem; opacity:.8;">
            Publicado el: <time datetime="<?= htmlspecialchars((string)($post['fecha_publicacion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              <?= format_es_date($post['fecha_publicacion'] ?? null); ?>
            </time>
          </p>
          <?php $ex = excerpt_plain($post['contenido'] ?? '', 30); if ($ex): ?>
            <p style="margin:0; font-size:.95rem; line-height:1.4;"><?= htmlspecialchars($ex, ENT_QUOTES, 'UTF-8'); ?></p>
          <?php endif; ?>
          <p style="margin:.75rem 0 0;">
            <a class="btn" href="<?= htmlspecialchars(post_link($post), ENT_QUOTES, 'UTF-8'); ?>">Leer más</a>
          </p>
        </article>
      <?php endforeach; ?>
    </section>

    <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Paginación" style="margin-top:1rem; display:flex; gap:.75rem; align-items:center;">
        <?php $base = url('search.php') . '?q=' . urlencode($q) . '&'; ?>
        <?php if ($page > 1): ?>
          <a class="page-prev" href="<?= $base . 'page=' . ($page - 1); ?>">&laquo; Anterior</a>
        <?php endif; ?>
        <span>Página <?= (int)$page; ?> de <?= (int)$pages; ?></span>
        <?php if ($page < $pages): ?>
          <a class="page-next" href="<?= $base . 'page=' . ($page + 1); ?>">Siguiente &raquo;</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

  <?php else: ?>
    <p>No se encontraron resultados para tu búsqueda.</p>
  <?php endif; ?>
</main>

<!-- Si sidebar te estrecha el footer en algunas vistas, asegúrate del clearfix -->
<div style="clear:both"></div>


<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
