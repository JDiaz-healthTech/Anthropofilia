<?php
require_once 'config.php';
$page_title = 'Página de Inicio'; // Título específico para esta página
require_once 'header.php'; // Incluimos la cabecera

$sql = "SELECT id_post, titulo, fecha_publicacion FROM posts ORDER BY fecha_publicacion DESC";
$resultado = $conn->query($sql);
?>

<main>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php while ($post = $resultado->fetch_assoc()): ?>
            <article>
                <h2>
                    <a href="post.php?id=<?php echo $post['id_post']; ?>">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                </h2>
                <p>Publicado el: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></p>
            </article>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No hay posts para mostrar.</p>
    <?php endif; ?>
</main>

<?php require_once 'sidebar.php'; ?>
<?php
$conn->close();
require_once 'footer.php'; // Incluimos el pie de página
?>