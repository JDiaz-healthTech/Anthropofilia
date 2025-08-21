<?php
// index.php

// 1. INICIALIZACIÓN: Requerimos el archivo central que maneja la conexión a la BBDD.
require_once 'init.php';

$page_title = 'Página de Inicio'; // Título específico para esta página
require_once 'header.php'; // Incluimos la cabecera

// 2. CONSULTA A LA BASE DE DATOS CON PDO
// Usamos el objeto $pdo, que ya está disponible globalmente.
// PDO::query() es ideal para consultas SELECT sin parámetros.
$sql = "SELECT id_post, titulo, fecha_publicacion FROM posts ORDER BY fecha_publicacion DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <article>
                <h2>
                    <a href="post.php?id=<?php echo $post['id_post']; ?>">
                        <?php echo htmlspecialchars($post['titulo']); ?>
                    </a>
                </h2>
                <p>Publicado el: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></p>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay posts para mostrar.</p>
    <?php endif; ?>
</main>

<?php require_once 'sidebar.php'; ?>
<?php
// Ya no es necesario cerrar la conexión de PDO.
require_once 'footer.php'; // Incluimos el pie de página
?>