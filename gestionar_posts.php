<?php
// Barrera de seguridad
session_start();

//VALIDACION CSRF
//Genera token de acceso. Crea cadena aleatoria y la guarda en la sesion del usuario
$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 


if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Consulta para obtener todos los posts
$sql = "SELECT id_post, titulo, fecha_publicacion FROM posts ORDER BY fecha_publicacion DESC";
$resultado = $conn->query($sql);

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
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($post = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['titulo']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></td>
                        <td>
                            <a href="editar_post.php?id=<?php echo $post['id_post']; ?>">Editar</a> |
                            <a href="eliminar_post.php?id=<?php echo $post['id_post']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este post?');">Eliminar</a>                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay posts para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php
$conn->close();
require_once 'footer.php';
?>