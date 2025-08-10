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

<aside>
    <h3>DEPARTAMENTOS</h3>
    <div class="sidebar-widget">
        <h4>Acerca de mí</h4>
        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgtUsMo7JzZqe2x0_oI4kpdla_db0L_uUd37YU5zKKlCyZosWtwZCyWs0EfEa6t9-NooVepFnKOt2yBQP5zlTjQJbKDS6gwc_c-sxGkvYL84axXd8RndyeJerDYolorZAPZGxVVM8rVqYieOL2Smx9bTE3M7ofb5tCmW_PB-Rwe6oafgA/s220/Logo%20divertido.png" alt="Foto de Ana Sampedro" style="width: 100%;">
        <p>Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela...</p>
        <a href="https://saperefilia.blogspot.com/">Leer más</a>
    </div>
    <hr>
    <?php
    // 1. Preparamos la consulta para obtener las categorías
    $sql_categorias = "SELECT nombre_categoria, slug FROM categorias ORDER BY nombre_categoria ASC";
    $resultado_categorias = $conn->query($sql_categorias);

    // 2. Verificamos si hay resultados
    if ($resultado_categorias && $resultado_categorias->num_rows > 0) {
        echo '<ul>';
        // 3. Usamos un bucle WHILE para recorrerlas
        while ($cat = $resultado_categorias->fetch_assoc()) {
            // 4. Creamos un enlace a una futura página 'categoria.php' pasando el slug
            echo '<li>';
            echo '<a href="categoria.php?slug=' . htmlspecialchars($cat['slug']) . '">';
            echo htmlspecialchars($cat['nombre_categoria']);
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No hay categorías para mostrar.</p>';
    }
    ?>
    <div class="sidebar-widget">
        <h4>Sitios de Interés</h4>
        <ul>
            <li><a href="https://www.atapuerca.org/" target="_blank">Fundación Atapuerca</a></li>
            <li><a href="https://www.cenieh.es/" target="_blank">CENIEH</a></li>
            <li><a href="https://www.iphes.cat/" target="_blank">IPHES</a></li>
        </ul>
    </div>
    <div class="sidebar-widget">
        <h4>Archivo</h4>
        <?php
        // Usamos la consulta que acabamos de construir
        $sql_archivo = "SELECT YEAR(fecha_publicacion) AS anio, MONTH(fecha_publicacion) AS mes_num, MONTHNAME(fecha_publicacion) AS mes_nombre, COUNT(id_post) AS total_posts FROM posts GROUP BY anio, mes_num, mes_nombre ORDER BY anio DESC, mes_num DESC";
        $resultado_archivo = $conn->query($sql_archivo);

        if ($resultado_archivo && $resultado_archivo->num_rows > 0) {
            echo '<ul>';
            while ($fila = $resultado_archivo->fetch_assoc()) {
                // Creamos un enlace a una futura página 'archivo.php'
                $enlace = 'archivo.php?anio=' . $fila['anio'] . '&mes=' . $fila['mes_num'];
                // Mostramos el nombre del mes y el año, con el total de posts
                echo '<li><a href="' . $enlace . '">' . ucfirst($fila['mes_nombre']) . ' ' . $fila['anio'] . ' (' . $fila['total_posts'] . ')</a></li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
    <hr>
    <div class="sidebar-widget">
        <h4>Buscar</h4>
        <form action="search.php" method="GET">
            <input type="search" name="q" placeholder="Escribe aquí..." required>
            <button type="submit">Buscar</button>
        </form>
    </div>
    <hr>
</aside>
<?php
$conn->close();
require_once 'footer.php'; // Incluimos el pie de página
?>