<?php
require_once 'init.php';

// 1. CAPTURAR Y LIMPIAR EL TÉRMINO DE BÚSQUEDA
// Usamos trim() para eliminar espacios en blanco al inicio y al final.
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Si no hay término de búsqueda, redirigimos al inicio.
if (empty($query)) {
    header('Location: index.php');
    exit();
}

// Preparamos el término para usarlo en la consulta LIKE.
$search_term = "%" . $query . "%";

// Establecemos un título dinámico para la página.
$page_title = 'Resultados para: ' . htmlspecialchars($query);
require_once 'header.php';

// 2. PREPARAR LA CONSULTA SEGURA
// Buscamos el término en el título O en el contenido.
$sql = "SELECT id_post, titulo, fecha_publicacion 
        FROM posts 
        WHERE titulo LIKE ? OR contenido LIKE ?
        ORDER BY fecha_publicacion DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 3. VINCULAR PARÁMETROS Y EJECUTAR
    // "ss" significa que estamos vinculando dos variables de tipo string.
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<main>
    <h2>Resultados de búsqueda para: "<?php echo htmlspecialchars($query); ?>"</h2>
    <hr>
    <?php if (isset($resultado) && $resultado->num_rows > 0): ?>
        <p>Se encontraron <?php echo $resultado->num_rows; ?> resultado(s).</p>
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
        <p>No se encontraron resultados para tu búsqueda.</p>
    <?php endif; ?>
</main>

<?php require_once 'sidebar.php'; ?>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
require_once 'footer.php';
?>