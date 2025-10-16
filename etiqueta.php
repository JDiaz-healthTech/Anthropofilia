<?php
// init.php se encarga de la conexión a la BD y la sesión
require_once 'init.php';

// 1. OBTENER Y VALIDAR EL NOMBRE DE LA ETIQUETA DESDE LA URL
// Usamos trim() para limpiar espacios y htmlspecialchars() por seguridad.
$nombre_etiqueta = isset($_GET['tag']) ? trim($_GET['tag']) : '';

if (empty($nombre_etiqueta)) {
    // Si no hay etiqueta, redirigimos al inicio.
    header("Location: index.php");
    exit();
}

// 2. PREPARAR LA CONSULTA SEGURA CON LOS JOINS
$sql = "SELECT p.id_post, p.titulo, p.fecha_publicacion
        FROM posts AS p
        INNER JOIN post_etiquetas AS pe ON p.id_post = pe.id_post
        INNER JOIN etiquetas AS e ON pe.id_etiqueta = e.id_etiqueta
        WHERE e.nombre_etiqueta = :nombre_etiqueta
        ORDER BY p.fecha_publicacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':nombre_etiqueta' => $nombre_etiqueta]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Título dinámico para la página
$page_title = 'Mostrando posts con la etiqueta: ' . htmlspecialchars($nombre_etiqueta);
require_once 'header.php';
?>

<main>
    <h2>Posts etiquetados como: "<?php echo htmlspecialchars($nombre_etiqueta); ?>"</h2>
    <hr>

    <?php if ($posts): ?>
        <?php foreach ($posts as $post): ?>
            <article>
                <h3>
                    <a href="post.php?id=<?php echo $post['id_post']; ?>">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                </h3>
                <p>Publicado el: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></p>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No se encontraron posts con esta etiqueta.</p>
    <?php endif; ?>
</main>

<?php
// Incluimos el sidebar y el footer
require_once 'sidebar.php';
require_once 'footer.php';
?>