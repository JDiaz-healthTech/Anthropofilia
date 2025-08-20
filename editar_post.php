<?php
// Barrera de seguridad e inicialización
session_start();

//VALIDACION DE USUARIO
//Genera token de aceso y lo guarda en la sesion del usuario
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Obtener el ID del post de la URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($post_id <= 0) {
    die('ID de post no válido.');
}

// Consulta para obtener los datos del post a editar
$sql_post = "SELECT * FROM posts WHERE id_post = ?";
$stmt = $conn->prepare($sql_post);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$resultado_post = $stmt->get_result();
$post = $resultado_post->fetch_assoc();
$stmt->close();

if (!$post) {
    die('Post no encontrado.');
}

// Consulta para las categorías
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$resultado_categorias = $conn->query($sql_categorias);

$page_title = 'Editar Post';
require_once 'header.php';
?>

<main>
    <h2>Editar Post</h2>
    <form action="actualizar_post.php" method="POST" class="form-container">

        <!-- Inyeccion oculta de token de acceso -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($post['titulo']); ?>" required>
        </div>
        <div>
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <?php while ($cat = $resultado_categorias->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>" <?php if ($cat['id_categoria'] == $post['id_categoria']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                    </option>
                <?php endwhile; ?>
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
    selector: '#contenido', // Apunta al ID de tu textarea
    plugins: 'code lists link image table autoresize',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | table | code',
    height: 500,
    menubar: false,
    content_css: 'style.css' // Opcional: para que el contenido en el editor se vea similar a tu web
  });
</script>

<?php
$conn->close();
require_once 'footer.php';
?>