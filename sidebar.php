<?php
// sidebar.php

// Este script asume que la conexión a la base de datos ($pdo) ya existe
?>
<aside>
    <div class="sidebar-widget">
        <h4>Buscar</h4>
        <form action="search.php" method="GET" class="form-container" style="padding: 0; border: none; margin: 0;">
            <input type="search" name="q" placeholder="Escribe aquí..." required>
            <button type="submit">Buscar</button>
        </form>
    </div>

    <div class="sidebar-widget">
        <h4>Acerca de mí</h4>
        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgtUsMo7JzZqe2x0_oI4kpdla_db0L_uUd37YU5zKKlCyZosWtwZCyWs0EfEa6t9-NooVepFnKOt2yBQP5zlTjQJbKDS6gwc_c-sxGkvYL84axXd8RndyeJerDYolorZAPZGxVVM8rVqYieOL2Smx9bTE3M7ofb5tCmW_PB-Rwe6oafgA/s220/Logo%20divertido.png" alt="Foto de Ana Sampedro" style="width: 100%;">
        <p>Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela...</p>
        <a href="acerca_de_mi.php">Leer más</a>
    </div>

    <div class="sidebar-widget">
        <h4>Departamentos</h4>
        <?php
        $sql_categorias_sidebar = "SELECT nombre_categoria, slug FROM categorias ORDER BY nombre_categoria ASC";
        $resultado_categorias_sidebar = $pdo->query($sql_categorias_sidebar)->fetchAll();

        // Verifica si hay categorías y las muestra en el sidebar
        if (!empty($resultado_categorias_sidebar)) {
            echo '<ul>';
            foreach ($resultado_categorias_sidebar as $cat) {
                echo '<li><a href="categoria.php?slug=' . htmlspecialchars($cat['slug']) . '">' . htmlspecialchars($cat['nombre_categoria']) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No hay categorías.</p>';
        }
        ?>
    </div>

    <div class="sidebar-widget">
        <h4>Archivo</h4>
        <?php
        $sql_archivo_sidebar = "SELECT YEAR(fecha_publicacion) AS anio, MONTH(fecha_publicacion) AS mes_num, MONTHNAME(fecha_publicacion) AS mes_nombre, COUNT(id_post) AS total_posts FROM posts GROUP BY anio, mes_num, mes_nombre ORDER BY anio DESC, mes_num DESC";
        $resultado_archivo_sidebar = $pdo->query($sql_archivo_sidebar)->fetchAll();

        if (!empty($resultado_archivo_sidebar)) {
            echo '<ul>';
            foreach ($resultado_archivo_sidebar as $fila) {
                $enlace = 'archivo.php?anio=' . $fila['anio'] . '&mes=' . $fila['mes_num'];
                // La función ucfirst() en PHP puede no funcionar bien con tildes, ojo futuro
                echo '<li><a href="' . $enlace . '">' . ucfirst($fila['mes_nombre']) . ' ' . $fila['anio'] . ' (' . $fila['total_posts'] . ')</a></li>';
            }
            echo '</ul>';
        }
        ?>
    </div>

    <div class="sidebar-widget">
        <h4>Sitios de Interés</h4>
        <ul>
            <li><a href="https://www.atapuerca.org/" target="_blank">Fundación Atapuerca</a></li>
            <li><a href="https://www.cenieh.es/" target="_blank">CENIEH</a></li>
            <li><a href="https://www.iphes.cat/" target="_blank">IPHES</a></li>
        </ul>
    </div>
</aside>