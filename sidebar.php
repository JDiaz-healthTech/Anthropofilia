<?php
// sidebar.php
declare(strict_types=1);

// Se asume que init.php ya fue requerido por la página que incluye este sidebar
// y que existe $pdo.

?>
<aside>
  <div class="sidebar-widget">
    <h4>Buscar</h4>
    <form action="/search.php" method="GET" class="form-container" style="padding:0;border:none;margin:0;">
      <input type="search" name="q" placeholder="Escribe aquí..." required
             value="<?php echo htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit">Buscar</button>
    </form>
  </div>

  <div class="sidebar-widget">
    <h4>Acerca de mí</h4>
    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgtUsMo7JzZqe2x0_oI4kpdla_db0L_uUd37YU5zKKlCyZosWtwZCyWs0EfEa6t9-NooVepFnKOt2yBQP5zlTjQJbKDS6gwc_c-sxGkvYL84axXd8RndyeJerDYolorZAPZGxVVM8rVqYieOL2Smx9bTE3M7ofb5tCmW_PB-Rwe6oafgA/s220/Logo%20divertido.png"
         alt="Foto de Ana López Sampedro" style="width:100%;">
    <p>Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela...</p>
    <a href="/acerca_de_mi.php">Leer más</a>
  </div>

  <div class="sidebar-widget">
    <h4>Categorías</h4>
    <?php
    try {
        $sql = "SELECT nombre_categoria, slug
                FROM categorias
                ORDER BY nombre_categoria ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($cats) {
            echo '<ul>';
            foreach ($cats as $cat) {
                $slug = htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($cat['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');
                echo '<li><a href="/categoria.php?slug=' . $slug . '">' . $name . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No hay categorías.</p>';
        }
    } catch (Throwable $e) {
        // Silencioso en UI; opcional: $security->logEvent('error', 'sidebar_categories_failed', ['error'=>$e->getMessage()]);
        echo '<p>No se pudieron cargar las categorías.</p>';
    }
    ?>
  </div>

  <div class="sidebar-widget">
    <h4>Archivo</h4>
    <?php
    // Mapeo de meses en español (evita depender de lc_time_names/MONTHNAME)
    $mesesES = [
        1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',
        7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
    ];
    try {
        $sql = "SELECT YEAR(fecha_publicacion) AS anio,
                       MONTH(fecha_publicacion) AS mes_num,
                       COUNT(id_post) AS total_posts
                FROM posts
                GROUP BY anio, mes_num
                ORDER BY anio DESC, mes_num DESC";
        $archivo = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($archivo) {
            echo '<ul>';
            foreach ($archivo as $fila) {
                $anio = (int)$fila['anio'];
                $mes  = (int)$fila['mes_num'];
                $total = (int)$fila['total_posts'];
                $mesNombre = $mesesES[$mes] ?? (string)$mes;
                $href = '/archivo.php?anio=' . $anio . '&mes=' . $mes;
                echo '<li><a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">'
                   . ucfirst($mesNombre) . ' ' . $anio . ' (' . $total . ')</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Sin archivos disponibles.</p>';
        }
    } catch (Throwable $e) {
        // Opcional: $security->logEvent('error','sidebar_archive_failed',['error'=>$e->getMessage()]);
        echo '<p>No se pudo cargar el archivo.</p>';
    }
    ?>
  </div>

  <div class="sidebar-widget">
    <h4>Sitios de interés</h4>
    <ul>
      <li><a href="https://www.atapuerca.org/" target="_blank" rel="noopener noreferrer">Fundación Atapuerca</a></li>
      <li><a href="https://www.cenieh.es/" target="_blank" rel="noopener noreferrer">CENIEH</a></li>
      <li><a href="https://www.iphes.cat/" target="_blank" rel="noopener noreferrer">IPHES</a></li>
    </ul>
  </div>
</aside>
