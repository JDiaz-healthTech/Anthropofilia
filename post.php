<?php
// post.php (refactor con init + SecurityManager + PDO)
require_once __DIR__ . '/init.php';

// 1) Entrada: id (int) o slug (string). Ambos opcionales, pero debe venir al menos uno.
$id_raw   = $_GET['id']   ?? null;
$slug_raw = $_GET['slug'] ?? null;

$id   = $id_raw !== null ? (int)$security->cleanInput($id_raw, 'int') : null;
$slug = $slug_raw !== null ? trim($security->cleanInput($slug_raw)) : null;

if (($id === null || $id <= 0) && ($slug === null || $slug === '')) {
    http_response_code(400);
    die('Solicitud inválida.');
}

// 2) Carga del post + categoría (id o slug, sin duplicar consultas)
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Usa slug si viene; si no, usa id
    $useSlug = isset($slug) && $slug !== null && $slug !== '';

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
        http_response_code(404);
        include __DIR__ . '/header.php';
        echo "<main class='container'><h1>Post no encontrado</h1><p>Lo sentimos, no existe ese contenido.</p></main>";
        include __DIR__ . '/footer.php';
        exit();
    }

    // (Opcional) Si te llegan ambos y no coinciden, fuerza URL canónica por slug
    if ($useSlug && isset($id) && $id && (int)$post['id_post'] !== (int)$id) {
        header("Location: /post.php?slug=" . urlencode($post['slug']), true, 301);
        exit();
    }

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('post_view_failed', ['error' => $e->getMessage()]);
    die('Error del servidor al cargar el post.');
}
 
    // 3) Etiquetas del post
    $stmtTags = $pdo->prepare(
        "SELECT e.nombre_etiqueta
         FROM post_etiquetas pe
         INNER JOIN etiquetas e ON e.id_etiqueta = pe.id_etiqueta
         WHERE pe.id_post = ?
         ORDER BY e.nombre_etiqueta ASC"
    );
    $stmtTags->execute([$post['id_post']]);
    $tags = $stmtTags->fetchAll(PDO::FETCH_COLUMN) ?: [];

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('post_view_failed', ['error' => $e->getMessage()]);
    die('Error del servidor al cargar el post.');
}

// 4) Render (salida segura). Escapa strings. El contenido puede tener HTML rico.
$titulo_safe    = htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$categoria_safe = htmlspecialchars($post['nombre_categoria'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8');
$imagen_url     = $post['imagen_destacada_url'] ?? null;
// NOTA: Si permites HTML, sanea $post['contenido'] con un sanitizador (p.ej. HTML Purifier) antes de imprimir.
$contenido_html = $post['contenido'] ?? '';

include __DIR__ . '/header.php';
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
                <a href="/search.php?q=<?= urlencode($t) ?>" rel="tag">
                  <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                </a>
              <?php endforeach; ?>
            </span>
        <?php endif; ?>
      </p>
      <?php if ($imagen_url): ?>
        <figure class="imagen-destacada">
          <img src="<?= htmlspecialchars($imagen_url, ENT_QUOTES, 'UTF-8') ?>" alt="Imagen destacada">
        </figure>
      <?php endif; ?>
    </header>

    <section class="contenido">
      <?= $contenido_html ?>
    </section>
  </article>
</main>
<?php include __DIR__ . '/footer.php'; ?>
<?php endif; ?>
<?php endif;    // post.php