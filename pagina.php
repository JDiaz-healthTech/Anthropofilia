<?php
// pagina.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Obtener y validar slug (solo a-z, 0-9 y guiones)
$slug = $_GET['slug'] ?? '';
if (!preg_match('/^[a-z0-9-]{1,120}$/', $slug)) {
    $security->abort(404, 'Página no encontrada.');
}

// 2) Consultar la tabla `paginas`
$stmt = $pdo->prepare('SELECT id_pagina, titulo, contenido FROM paginas WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$pagina = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pagina) {
    $security->abort(404, 'Página no encontrada.');
}

// 3) Metas
$page_title = $pagina['titulo'] ?? 'Página';
$meta_description = mb_substr(
    trim(preg_replace('/\s+/', ' ', strip_tags($pagina['contenido'] ?? ''))),
    0, 160
);

require_once __DIR__ . '/header.php';
?>
<main>
  <article>
    <h1><?php echo htmlspecialchars($pagina['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?></h1>
    <hr>
    <div class="page-content">
      <?php
      // Si ya saneas al guardar, puedes imprimir tal cual;
      // como defensa extra, saneamos aquí también.
      echo $security->sanitizeHTML($pagina['contenido'] ?? '');
      ?>
    </div>
  </article>
</main>

<?php require_once __DIR__ . '/sidebar.php'; ?>
<?php require_once __DIR__ . '/footer.php'; ?>
