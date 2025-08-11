<?php
require_once 'config.php';

// 1. OBTENER Y VALIDAR EL SLUG
$slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : '';
if (empty($slug)) {
    header("Location: index.php");
    exit();
}

$page_title = 'Categoría: ' . ucwords(str_replace('-', ' ', $slug)); // Título dinámico
require_once 'header.php';

// 2. PREPARAR LA CONSULTA CON JOIN
$sql = "SELECT posts.id_post, posts.titulo, posts.fecha_publicacion
        FROM posts
        INNER JOIN categorias ON posts.id_categoria = categorias.id_categoria
        WHERE categorias.slug = ?
        ORDER BY posts.fecha_publicacion DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 3. VINCULAR PARÁMETRO Y EJECUTAR
    $stmt->bind_param("s", $slug); // "s" porque el slug es un string
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<main>
    <h2>Mostrando posts de la categoría: "<?php echo ucwords(str_replace('-', ' ', $slug)); ?>"</h2>
    <hr>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while($post = $resultado->fetch_assoc()): ?>
            <article>
                <h3>
                    <a href="post.php?id=<?php echo $post['id_post']; ?>">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                </h3>
                <p>Publicado el: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></p>
            </article>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No se encontraron posts en esta categoría.</p>
    <?php endif; ?>
</main>

<?php require_once 'sidebar.php'; ?>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
require_once 'footer.php';
?>