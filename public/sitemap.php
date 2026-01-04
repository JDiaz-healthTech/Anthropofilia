<?php
declare(strict_types=1); require_once __DIR__ . '/init.php';
header('Content-Type: application/xml; charset=UTF-8');

$urls = [];

// Posts
$q = $pdo->query("SELECT slug, GREATEST(COALESCE(actualizado_en, '1970-01-01'), fecha_publicacion) AS updated FROM posts ORDER BY updated DESC");
foreach ($q as $r) {
  $urls[] = ['loc' => url('post.php?slug=' . urlencode($r['slug'])), 'lastmod' => substr($r['updated'],0,10)];
}

// Páginas
$q = $pdo->query("SELECT slug, COALESCE(DATE(actualizado_en), DATE(fecha_creacion)) AS updated FROM paginas ORDER BY updated DESC");
foreach ($q as $r) {
  $urls[] = ['loc' => url('pagina.php?slug=' . urlencode($r['slug'])), 'lastmod' => $r['updated']];
}

// Categorías
$q = $pdo->query("SELECT slug FROM categorias ORDER BY nombre_categoria");
foreach ($q as $r) {
  $urls[] = ['loc' => url('categoria.php?slug=' . urlencode($r['slug']))];
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= htmlspecialchars($u['loc'], ENT_QUOTES, 'UTF-8') ?></loc>
    <?php if (!empty($u['lastmod'])): ?><lastmod><?= $u['lastmod'] ?></lastmod><?php endif; ?>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
</urlset>
