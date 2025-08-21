<?php
require_once 'init.php';

// 1. OBTENER Y VALIDAR AÑO Y MES DE LA URL
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;

if ($anio <= 0 || $mes <= 0) {
    header("Location: index.php");
    exit();
}

$page_title = 'Archivo: ' . $mes . '/' . $anio; // Título dinámico
require_once 'header.php';

// 2. PREPARAR LA CONSULTA SEGURA
$sql = "SELECT id_post, titulo, fecha_publicacion 
        FROM posts 
        WHERE YEAR(fecha_publicacion) = ? AND MONTH(fecha_publicacion) = ?
        ORDER BY fecha_publicacion DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // 3. VINCULAR PARÁMETROS Y EJECUTAR
    $stmt->bind_param("ii", $anio, $mes); // "ii" porque ambos son enteros
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<main>
    <h2>Archivo de posts de: <?php echo $mes . '/' . $anio; ?></h2>
    <hr>
    <?php if (isset($resultado) && $resultado->num_rows > 0): ?>
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
        <p>No se encontraron posts para esta fecha.</p>
    <?php endif; ?>
</main>

<?php require_once 'sidebar.php'; ?>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
require_once 'footer.php';
?>