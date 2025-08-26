<?php
// editar_pagina.php — versión pulida
require_once __DIR__ . '/init.php';

// 1) Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 2) Obtener y validar id
$pagina_id = (int)$security->cleanInput($_GET['id'] ?? '', 'int');
if ($pagina_id <= 0) {
    http_response_code(400);
    header("Location: gestionar_paginas.php?error=id_invalido");
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3) Cargar la página (evita SELECT *)
    $stmt = $pdo->prepare("SELECT id_pagina, titulo, slug, contenido FROM paginas WHERE id_pagina = ? LIMIT 1");
    $stmt->execute([$pagina_id]);
    $pagina = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pagina) {
        http_response_code(404);
        header("Location: gestionar_paginas.php?error=not_found");
        exit();
    }

} catch (Throwable $e) {
    http_response_code(500);
    // En prod: $security->logEvent('error','page_load_failed',['id'=>$pagina_id,'error'=>$e->getMessage()]);
    die('Error al cargar la página.');
}

$page_title = 'Editar Página';
require_once __DIR__ . '/header.php';
?>
<main class="container">
  <h1>Editar página estática</h1>

  <form action="actualizar_pagina.php" method="post" class="form-container" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($security->csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="id_pagina" value="<?= (int)$pagina['id_pagina'] ?>">

    <div>
      <label for="titulo">Título</label>
      <input
        type="text" id="titulo" name="titulo"
        required maxlength="150" autocomplete="off"
        value="<?= htmlspecialchars($pagina['titulo'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="slug">Slug (URL amigable)</label>
      <input
        type="text" id="slug" name="slug"
        required maxlength="191" spellcheck="false" autocapitalize="off" autocomplete="off"
        pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
        title="Solo minúsculas, números y guiones (ej: historia-da-filosofia)"
        value="<?= htmlspecialchars($pagina['slug'], ENT_QUOTES, 'UTF-8') ?>">
      <small>Usa minúsculas, sin acentos, separadas por guiones.</small>
    </div>

    <div>
      <label for="contenido">Contenido</label>
      <textarea
        id="contenido" name="contenido" rows="20" required maxlength="50000"><?= htmlspecialchars($pagina['contenido'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div style="display:flex; gap:.5rem; align-items:center;">
      <button type="submit">Actualizar página</button>
      <a href="gestionar_paginas.php">Cancelar</a>
    </div>
  </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>

<!-- Auto-slug opcional (no sustituye normalización server-side) -->
<script>
(function () {
  const t = document.getElementById('titulo');
  const s = document.getElementById('slug');
  let userEditedSlug = false;

  s.addEventListener('input', () => { userEditedSlug = true; });

  function slugify(str) {
    return str
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 191);
  }

  // Solo autogenera si el slug está vacío (en edición normalmente no lo estará)
  t.addEventListener('input', () => {
    if (!userEditedSlug && !s.value) {
      s.value = slugify(t.value);
    }
  });
})();
</script>
