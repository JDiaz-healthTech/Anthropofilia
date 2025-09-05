<?php
declare(strict_types=1); require_once __DIR__ . '/init.php';
header('Content-Type: application/rss+xml; charset=UTF-8');

$siteTitle = 'Anthropofilia';
$siteLink  = rtrim(($GLOBALS['baseUrl'] ?? ''), '/');

$stmt = $pdo->query("SELECT slug, titulo, fecha_publicacion, SUBSTRING(contenido,1,400) AS excerpt FROM posts ORDER BY fecha_publicacion DESC LIMIT 20");
$items = $stmt->fetchAll();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rss version="2.0">
  <channel>
    <title><?= htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link><?= htmlspecialchars($siteLink, ENT_QUOTES, 'UTF-8') ?></link>
    <description>Últimas entradas</description>
    <?php foreach ($items as $it): ?>
    <item>
      <title><?= htmlspecialchars($it['titulo'], ENT_QUOTES, 'UTF-8') ?></title>
      <link><?= htmlspecialchars(url('post.php?slug=' . urlencode($it['slug'])), ENT_QUOTES, 'UTF-8') ?></link>
      <guid><?= htmlspecialchars(url('post.php?slug=' . urlencode($it['slug'])), ENT_QUOTES, 'UTF-8') ?></guid>
      <pubDate><?= date(DATE_RSS, strtotime($it['fecha_publicacion'])) ?></pubDate>
      <description><![CDATA[<?= htmlspecialchars(trim($it['excerpt']), ENT_QUOTES, 'UTF-8') ?>...]]></description>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>
