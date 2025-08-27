<?php
// post.php (PDO + SecurityManager + canónica por slug)
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Entrada: id (int) o slug (string). Debe llegar al menos uno
$id   = isset($_GET['id'])   ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
$slug = isset($_GET['slug']) ? trim((string)$_GET['slug']) : null;

if (($id === null || $id <= 0) && ($slug === null || $slug === '')) {
    $security->abort(400, 'Solicitud inválida.');
}

try {
    // 2) Cargar post (por slug si viene; si no, por id)
    $useSlug = ($slug !== null && $slug !== '');
    $sql = "SELECT p.id_post, p.slug, p.titulo, p.contenido, p.imagen_destacada_url, p.fecha_publicacion,
                   p.id_categoria, c.nombre_categoria
            FROM posts p
            LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
            WHERE " . ($useSlug ? "p.slug = ?" : "p.id_post = ?") . "
            LIMIT 1";
    $param = $useSlug ? $slug : $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$param]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $security->abort(404, 'Post no encontrado.');
    }

    // 3) Canonical por slug (si tenemos ambos y no coinciden, o si vino id sin slug)
if (!empty($post['slug'])) {
    $wantSlug = (string)$post['slug'];
    $haveSlug = (string)($slug ?? '');
    if ($haveSlug === '' || $haveSlug !== $wantSlug) {
        header('Location: ' . url('post.php?slug=' . urlencode($wantSlug)), true, 301);
        exit();
    }
}

    // 4) Etiquetas del post
    $stmtTags = $pdo->prepare(
        "SELECT e.nombre_etiqueta
         FROM post_etiquetas pe
         INNER JOIN etiquetas e ON e.id_etiqueta = pe.id_etiqueta
         WHERE pe.id_post = ?
         ORDER BY e.nombre_etiqueta ASC"
    );
    $stmtTags->execute([(int)$post['id_post']]);
    $tags = $stmtTags->fetchAll(PDO::FETCH_COLUMN) ?: [];

} catch (Throwable $e) {
    $security->logEvent('error', 'post_view_failed', ['error' => $e->getMessage()]);
    $security->abort(500, 'Error del servidor.');
}

// 5) Render (salida segura)
// Meta
$page_title = $post['titulo'] ?? 'Post';
$meta_description = mb_substr(
    trim(preg_replace('/\s+/', ' ', strip_tags($post['contenido'] ?? ''))),
    0, 160
);

// Campos saneados
$titulo_safe    = htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$categoria_safe = htmlspecialchars($post['nombre_categoria'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8');
$imagen_url     = $post['imagen_destacada_url'] ?? null;

// Contenido HTML (defensa en profundidad)
$contenido_html = $security->sanitizeHTML($post['contenido'] ?? '');

require_once __DIR__ . '/header.php';
?>
<main class="container post">
  <article>
    <header>
      <h1><?= $titulo_safe ?></h1>
      <p class="meta">
        <span class="categoria"><?= $categoria_safe ?></span>
        <?php if (!empty($tags)): ?>
          · <span class="tags">
              <?php foreach ($tags as $t): ?>
                <a href="<?= url('search.php?q=' . urlencode($t)) ?>=<?= urlencode($t) ?>" rel="tag">
                  <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                </a>
              <?php endforeach; ?>
            </span>
        <?php endif; ?>
      </p>
      <?php if (!empty($imagen_url)): ?>
        <figure class="imagen-destacada">
          <!-- Alt vacío intencionadamente; la imagen es decorativa 
          <img src="<?= htmlspecialchars($imagen_url, ENT_QUOTES, 'UTF-8') ?>" alt="Imagen destacada">
          a continuacion propuesta para mejorar la presetacion de la imagen -->
          <img src="<?= htmlspecialchars($imagen_url, ENT_QUOTES, 'UTF-8') ?>"
              alt="Imagen destacada"
              loading="lazy" decoding="async"
              sizes="(min-width: 800px) 720px, 100vw">

        </figure>
      <?php endif; ?>
    </header>

    <section class="contenido">
      <?= $contenido_html ?>
    </section>
  </article>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
