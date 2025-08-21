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
$sql_post = "SELECT id_post, id_usuario, titulo, contenido, imagen_destacada_url, id_categoria, etiquetas
             FROM posts WHERE id_post = ?";
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
$resultado_categorias = $pdo->query($sql_categorias)->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Editar Post';
require_once __DIR__ . '/header.php';
?>

<main>
    <h2>Editar Post</h2>
    <form action="actualizar_post.php" method="POST" class="form-container">
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
            <input type="url" id="imagen_url" name="imagen_url"
                   value="<?php echo htmlspecialchars($post['imagen_destacada_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
<script<?php echo $nonceAttr; ?>>
    tinymce.init({
        selector: '#contenido',
        plugins: 'code lists link image table autoresize',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
        height: 500,
        menubar: false,
        content_css: 'style.css'
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
