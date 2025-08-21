<?php
require_once 'init.php';
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    die('ID de post no válido.');
}

// ===================================================================
// !! ESTE ES EL BLOQUE DE CÓDIGO CRÍTICO QUE FALTABA !!
// ===================================================================
$sql = "SELECT * FROM posts WHERE id_post = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$resultado = $stmt->get_result();
$post = $resultado->fetch_assoc();
// La sentencia se puede cerrar aquí, ya no la necesitamos más
$stmt->close(); 
// ===================================================================

// Verificación crucial: ¿se encontró el post?
if (!$post) {
    // Si no se encuentra, detenemos la ejecución con un mensaje claro.
    die('Post no encontrado.');
}

// Ahora que sabemos que $post existe, podemos continuar.
$page_title = $post['titulo'];
require_once 'header.php';
?>

<main>
    <article>
        <div class="single-post-content">
            <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
            
            <span class="post-meta">
                Publicado el: <?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?>
            </span>
            
            <?php if (!empty($post['imagen_destacada_url'])): ?>
                <img src="<?php echo htmlspecialchars($post['imagen_destacada_url']); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>" style="margin-bottom: 2rem;">
            <?php endif; ?>

            <div>
                <?php
                // Usamos la instancia del SecurityManager que ya fue creada en init.php
                // para purificar el HTML antes de mostrarlo.
                echo SecurityManager::instance()->sanitizeHTML($post['contenido']);
                ?>
            </div>
        </div>

        <?php
        // --- Mostrar etiquetas ---
        $sql_tags = "SELECT e.nombre_etiqueta FROM etiquetas e INNER JOIN post_etiquetas pe ON e.id_etiqueta = pe.id_etiqueta WHERE pe.id_post = ?";
        $stmt_tags = $conn->prepare($sql_tags);
        $stmt_tags->bind_param("i", $post_id);
        $stmt_tags->execute();
        $resultado_tags = $stmt_tags->get_result();

        if ($resultado_tags->num_rows > 0) {
            echo '<div class="etiquetas-container">';
            echo '<strong>Etiquetas:</strong> ';
            while ($tag = $resultado_tags->fetch_assoc()) {
                echo '<span class="etiqueta">' . htmlspecialchars($tag['nombre_etiqueta']) . '</span>';
            }
            echo '</div>';
        }
        $stmt_tags->close();
        ?>
    </article>
</main>

<?php require_once 'sidebar.php'; ?>

<?php
$conn->close();
require_once 'footer.php';
?>