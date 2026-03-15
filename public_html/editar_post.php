<?php
require_once __DIR__ . '/init.php';

// 1) Requiere login
$security->requireLogin();

// 2) Entrada robusta
$post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$post_id || $post_id <= 0) {
    $security->abort(400, 'ID de post no válido.');
}

// 3) Cargar post
$sql_post = "SELECT id_post, slug, titulo, contenido, imagen_destacada_url,
                              fecha_publicacion, id_usuario, id_categoria
                       FROM posts
                       WHERE id_post = :id";
$stmt = $pdo->prepare($sql_post);
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    $security->abort(404, 'Post no encontrado.');
}

// 4) Autorización
$security->requireOwnershipOrRole((int)$post['id_usuario'], ['admin']);

// 5) Categorías
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute();
$resultado_categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Editar Post';
  $categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main>

    <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <?php if (!empty($categoria)): ?>
      <a href="<?= url('categoria.php?slug=' . urlencode($categoria['slug'])) ?>">
        <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
      </a> <span aria-hidden="true">›</span>
    <?php endif; ?>
    <span aria-current="page"><?= htmlspecialchars($page_title ?? 'Actual', ENT_QUOTES, 'UTF-8') ?></span>
  </nav>

    <h2>Editar Post</h2>
    <form action="actualizar_post.php" method="POST" enctype="multipart/form-data" class="form-container">
            <?php echo $security->csrfField(); ?>
        <input type="hidden" name="id_post" value="<?php echo (int)$post['id_post']; ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required
                   value="<?php echo htmlspecialchars($post['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div>
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <?php foreach ($resultado_categorias as $cat): ?>
                    <option value="<?php echo (int)$cat['id_categoria']; ?>"
                        <?php echo ((int)$cat['id_categoria'] === (int)$post['id_categoria']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre_categoria'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="etiquetas">Etiquetas:</label>
            <input type="text" id="etiquetas" name="etiquetas"
                   placeholder="introduce etiquetas separadas por comas"
                   value="<?php echo htmlspecialchars($post['etiquetas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="15" required><?php
                echo htmlspecialchars($post['contenido'] ?? '', ENT_QUOTES, 'UTF-8');
            ?></textarea>
        </div>

        <div>
            <label for="imagen_url">URL de la Imagen Destacada:</label>
          <input type="text" id="imagen_url" name="imagen_url"
                placeholder="https://ejemplo.com/imagen.jpg o ruta local"
                value="<?php echo htmlspecialchars($post['imagen_destacada_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div>
            <label for="imagen">O subir nueva imagen:</label>
            <input type="file"
                   id="imagen"
                   name="imagen"
                   accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Máximo 2MB. Si subes un archivo nuevo, reemplazará la URL anterior.</small>
        </div>

        <button type="submit">Actualizar Post</button>
    </form>
</main>

<?php
// Si NO usas 'unsafe-inline' en CSP:
$nonceAttr = ($security->cspNonce())
    ? ' nonce="'.htmlspecialchars($security->cspNonce(), ENT_QUOTES, 'UTF-8').'"'
    : '';
?>

<script src="js/tinymce-config.js"<?= $nonceAttr ?>></script>
<script<?= $nonceAttr ?>>
  initTinyMCE({
    csrfToken: '<?= $security->csrfToken() ?>',
    uploadUrl: '<?= url("upload_image.php") ?>',
    contentCss: '<?= url("css/style.css") ?>',
    type: 'post'
  });
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
