<?php
require_once __DIR__ . '/init.php';

// Auth
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// PRG: datos previos si hubo error al guardar
$old = $_SESSION['form_post'] ?? [];
unset($_SESSION['form_post']); // limpia tras mostrar

// CSRF
$csrf = $security->csrfToken();

// Cargar categorías (PDO)
$categorias = [];
try {
    $sql = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // En prod: $security->logEvent('error','load_categories_failed',['error'=>$e->getMessage()]);
    $categorias = [];
}

$page_title = 'Crear Nuevo Post';
require_once __DIR__ . '/header.php';
?>
<main class="container">
  <h1>Crear nuevo post</h1>

  <form action="/guardar_post.php" method="post" enctype="multipart/form-data" class="form-container" accept-charset="UTF-8">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

    <div>
      <label for="titulo">Título</label>
      <input
        type="text" id="titulo" name="titulo"
        required maxlength="150" autocomplete="off"
        value="<?= htmlspecialchars($old['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="id_categoria">Categoría</label>
      <select id="id_categoria" name="id_categoria" required>
        <option value="">— Selecciona una categoría —</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['id_categoria'] ?>"
            <?= isset($old['id_categoria']) && (int)$old['id_categoria'] === (int)$cat['id_categoria'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label for="etiquetas">Etiquetas</label>
      <input
        type="text" id="etiquetas" name="etiquetas"
        maxlength="500" placeholder="separa con comas: ciencia, evolución"
        value="<?= htmlspecialchars($old['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
      <small>Se guardan en minúsculas y se desduplican automáticamente.</small>
    </div>

    <div>
      <label for="contenido">Contenido</label>
      <textarea id="contenido" name="contenido" rows="15" required maxlength="100000"><?= htmlspecialchars($old['contenido'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
      <label for="imagen">Imagen destacada (opcional)</label>
      <input
        type="file" id="imagen" name="imagen"
        accept="image/jpeg,image/png,image/gif">
      <!-- (Opcional) <input type="hidden" name="MAX_FILE_SIZE" value="2097152"> -->
    </div>

    <button type="submit">Guardar post</button>
  </form>
</main>

<script>
  // TinyMCE: asume que incluyes el script de la librería en header.php
  if (typeof tinymce !== 'undefined') {
    tinymce.init({
      selector: '#contenido',
      plugins: 'code lists link image table autoresize',
      toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
      height: 500,
      menubar: false,
      content_css: '/assets/css/styles.css' // ajusta a tu hoja
    });
  }
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
