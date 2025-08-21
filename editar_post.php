<?php
// editar_post.php

// 1. INICIALIZACIÓN: Requerimos el archivo central que maneja todo.
// Esto nos da acceso a $pdo y $security.
require_once 'init.php';

// 2. BARRERA DE SEGURIDAD: Ya no necesitamos iniciar la sesión manualmente.
// En su lugar, simplemente validamos si el usuario está logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 3. OBTENER DATOS DEL POST (usando PDO)
// Obtenemos y validamos el ID del post desde la URL.
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    die('ID de post no válido.');
}

// Preparamos y ejecutamos la consulta de forma segura con PDO.
$sql_post = "SELECT * FROM posts WHERE id_post = ?";
$stmt = $pdo->prepare($sql_post);
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die('Post no encontrado.');
}

// 4. OBTENER LAS CATEGORÍAS (usando PDO)
// Hacemos una consulta simple sin parámetros.
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$resultado_categorias = $pdo->query($sql_categorias)->fetchAll();

$page_title = 'Editar Post';
require_once 'header.php';
?>

<main>
    <h2>Editar Post</h2>
    <form action="actualizar_post.php" method="POST" class="form-container">

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($security->csrfToken()); ?>">

        <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" required>
        </div>
        <div>
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <?php foreach ($resultado_categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>" <?php if ($cat['id_categoria'] == $post['id_categoria']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="etiquetas">Etiquetas:</label>
            <input type="text" id="etiquetas" name="etiquetas" placeholder="introduce etiquetas para identificar el post, separadas por comas">
        </div>
        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="15" required><?php echo htmlspecialchars($post['contenido']); ?></textarea>
        </div>
        <div>
            <label for="imagen_url">URL de la Imagen Destacada:</label>
            <input type="url" id="imagen_url" name="imagen_url" value="<?php echo htmlspecialchars($post['imagen_destacada_url']); ?>">
        </div>
        <button type="submit">Actualizar Post</button>
    </form>
</main>

<script>
    tinymce.init({
        selector: '#contenido',
        plugins: 'code lists link image table autoresize',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
        height: 500,
        menubar: false,
        content_css: 'style.css'
    });
</script>

<?php
// Ya no es necesario cerrar la conexión de PDO.
// require_once 'footer.php';
?>