<?php
// Barrera de seguridad
session_start();

//VALIDACION DE USUARIO
//Genera token de aceso y lo guarda en la sesion del usuario
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Consulta para obtener las categorías para el menú desplegable
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$resultado_categorias = $conn->query($sql_categorias);

$page_title = 'Crear Nuevo Post';
require_once 'header.php';
?>

<main>
    <h2>Crear Nuevo Post</h2>
    <form action="guardar_post.php" method="POST" enctype="multipart/form-data" class="form-container">
        
        <!-- Inyeccion oculta de token de acceso -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required>
        </div>
        <div>
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php
                if ($resultado_categorias->num_rows > 0) {
                    while ($cat = $resultado_categorias->fetch_assoc()) {
                        echo '<option value="' . $cat['id_categoria'] . '">' . htmlspecialchars($cat['nombre_categoria']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label for="etiquetas">Etiquetas:</label>
            <input type="text" id="etiquetas" name="etiquetas" placeholder="introduce etiquetas para identificar el post, separadas por comas">
        </div>
        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="15" required></textarea>
        </div>
        <div>
            <label for="imagen">Subir Imagen Destacada (Opcional):</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg, image/png, image/gif">
        </div>
        <button type="submit">Guardar Post</button>
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