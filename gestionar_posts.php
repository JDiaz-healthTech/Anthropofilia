<?php

// 1. Centralizamos todo en init.php
require_once 'init.php';

// Ya no necesitamos generar el token aquí. Lo gestiona el SecurityManager
// que arrancó en init.php.

// 2. Lógica de negocio con PDO
// Usamos el objeto $pdo, que ya está disponible.
// PDO::query() es ideal para consultas SELECT simples sin parámetros.
$sql = "SELECT id_post, titulo, fecha_publicacion FROM posts ORDER BY fecha_publicacion DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'Gestionar Posts';
require_once 'header.php';
?>

<main>
    <h2>Gestionar Posts Existentes</h2>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Fecha de Publicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($posts)): ?>
                <?php foreach($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['titulo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></td>
                        <td>
                            <a href="editar_post.php?id=<?php echo $post['id_post']; ?>">Editar</a> |
                            <a href="eliminar_post.php?id=<?php echo $post['id_post']; ?>&token=<?php echo $security->csrfToken(); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este post?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay posts para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php require_once 'footer.php'; ?>