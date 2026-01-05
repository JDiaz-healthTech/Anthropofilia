<?php
// sidebar.php
declare(strict_types=1);

// Se asume que init.php ya fue requerido por la página que incluye este sidebar
// y que existe $pdo.

?>
<aside class="site-sidebar">
  
  <!-- BUSCADOR -->
  <div class="sidebar-widget widget-search">
    <h3>🔍 Buscar</h3>
    <form action="<?= url('search.php') ?>" method="GET" class="search-form">
      <input 
        type="search" 
        name="q" 
        placeholder="Buscar en blog" 
        required
        value="<?= htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      <button type="submit">Buscar</button>
    </form>
  </div>

  <!-- ACERCA DE MÍ -->
  <div class="sidebar-widget widget-about">
    <h3>Acerca de mí</h3>
    <div class="about-card">
      <img 
        src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEgtUsMo7JzZqe2x0_oI4kpdla_db0L_uUd37YU5zKKlCyZosWtwZCyWs0EfEa6t9-NooVepFnKOt2yBQP5zlTjQJbKDS6gwc_c-sxGkvYL84axXd8RndyeJerDYolorZAPZGxVVM8rVqYieOL2Smx9bTE3M7ofb5tCmW_PB-Rwe6oafgA/s220/Logo%20divertido.png"
        alt="Ana López Sampedro" 
        class="about-photo"
        loading="lazy">
      <p class="about-text">
        Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela. 
        Este blog es un espacio de reflexión sobre temas filosóficos, educación y pensamiento crítico.
      </p>
      <a href="<?= url('acerca_de_mi.php') ?>" class="about-link">Ver perfil completo →</a>
    </div>
  </div>

  <!-- NUBE DE ETIQUETAS -->
  <div class="sidebar-widget widget-tags">
    <h3>Etiquetas populares</h3>
    <?php
    try {
        // Obtener etiquetas con conteo de posts
        $sql = "SELECT e.id_etiqueta, e.nombre_etiqueta, COUNT(pe.id_post) as total
                FROM etiquetas e
                INNER JOIN post_etiquetas pe ON e.id_etiqueta = pe.id_etiqueta
                GROUP BY e.id_etiqueta, e.nombre_etiqueta
                HAVING total > 0
                ORDER BY total DESC, e.nombre_etiqueta ASC
                LIMIT 20";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($tags) {
            // Calcular tamaños para la nube
            $counts = array_column($tags, 'total');
            $min = min($counts);
            $max = max($counts);
            $range = max($max - $min, 1); // Evitar división por cero
            
            echo '<div class="tag-cloud">';
            foreach ($tags as $tag) {
                $count = (int)$tag['total'];
                // Tamaño relativo: 0.8em - 1.8em
                $size = 0.8 + (($count - $min) / $range);
                $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $tag['nombre_etiqueta'])));
                $name = htmlspecialchars($tag['nombre_etiqueta'], ENT_QUOTES, 'UTF-8');
                
                echo '<a href="' . url('etiqueta.php?tag=' . urlencode($slug)) . '" 
                         class="tag-item" 
                         style="font-size: ' . $size . 'em;" 
                         title="' . $count . ' ' . ($count === 1 ? 'entrada' : 'entradas') . '">
                         ' . $name . '
                      </a>';
            }
            echo '</div>';
        } else {
            echo '<p class="sidebar-empty">No hay etiquetas todavía.</p>';
        }
    } catch (Throwable $e) {
        error_log("Error cargando etiquetas: " . $e->getMessage());
        echo '<p class="sidebar-empty">No se pudieron cargar las etiquetas.</p>';
    }
    ?>
  </div>

  <!-- CATEGORÍAS -->
  <div class="sidebar-widget widget-categories">
    <h3>Categorías</h3>
    <?php
    try {
        // Categorías con conteo de posts
        $sql = "SELECT c.nombre_categoria, c.slug, COUNT(p.id_post) as total
                FROM categorias c
                LEFT JOIN posts p ON c.id_categoria = p.id_categoria
                GROUP BY c.id_categoria, c.nombre_categoria, c.slug
                ORDER BY c.nombre_categoria ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($cats) {
            echo '<ul class="category-list">';
            foreach ($cats as $cat) {
                $slug = htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES, 'UTF-8');
                $name = htmlspecialchars($cat['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');
                $total = (int)$cat['total'];
                
                echo '<li>
                        <a href="' . url('categoria.php?slug=' . $slug) . '">
                          ' . $name . ' 
                          <span class="count">(' . $total . ')</span>
                        </a>
                      </li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="sidebar-empty">No hay categorías.</p>';
        }
    } catch (Throwable $e) {
        error_log("Error cargando categorías: " . $e->getMessage());
        echo '<p class="sidebar-empty">No se pudieron cargar las categorías.</p>';
    }
    ?>
  </div>

  <!-- ARCHIVO TEMPORAL -->
  <div class="sidebar-widget widget-archive">
    <h3>Archivo</h3>
    <?php
    $mesesES = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    try {
        $sql = "SELECT YEAR(fecha_publicacion) as anio, 
                       MONTH(fecha_publicacion) as mes, 
                       COUNT(*) as total
                FROM posts 
                WHERE fecha_publicacion <= NOW()
                GROUP BY anio, mes 
                ORDER BY anio DESC, mes DESC
                LIMIT 24"; // Últimos 24 meses
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar por año
        $byYear = [];
        foreach ($rows as $r) {
            $anio = (int)$r['anio'];
            $byYear[$anio][] = $r;
        }

        if ($byYear) {
            echo '<ul class="archive-list">';
            $currentYear = (int)date('Y');
            
            foreach ($byYear as $anio => $meses) {
                $countYear = array_sum(array_map(fn($r) => (int)$r['total'], $meses));
                $isOpen = ($anio === $currentYear) ? ' open' : '';
                
                echo '<li>
                        <details' . $isOpen . '>
                          <summary>
                            ' . $anio . ' 
                            <span class="count">(' . $countYear . ')</span>
                          </summary>
                          <ul>';
                
                foreach ($meses as $r) {
                    $mesNum = (int)$r['mes'];
                    $mesNombre = ucfirst($mesesES[$mesNum]);
                    $total = (int)$r['total'];
                    $href = url('archivo.php?anio=' . $anio . '&mes=' . $mesNum);
                    
                    echo '<li>
                            <a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">
                              ' . $mesNombre . ' 
                              <span class="count">(' . $total . ')</span>
                            </a>
                          </li>';
                }
                
                echo '  </ul>
                        </details>
                      </li>';
            }
            
            echo '</ul>';
        } else {
            echo '<p class="sidebar-empty">No hay entradas archivadas.</p>';
        }
    } catch (Throwable $e) {
        error_log("Error cargando archivo: " . $e->getMessage());
        echo '<p class="sidebar-empty">No se pudo cargar el archivo.</p>';
    }
    ?>
  </div>

  <!-- SITIOS DE INTERÉS -->
  <div class="sidebar-widget widget-links">
    <h3>Sitios de interés</h3>
    <?php
    $sites = [
        ['url' => 'https://www.atapuerca.org/', 'label' => 'Fundación Atapuerca', 'thumb' => '/images/links/atapuerca.jpg'],
        ['url' => 'https://www.cenieh.es/', 'label' => 'CENIEH', 'thumb' => '/images/links/cenieh.jpg'],
        ['url' => 'https://www.iphes.cat/', 'label' => 'IPHES', 'thumb' => '/images/links/iphes.jpg'],
        ['url' => 'https://redfilosofia.es/', 'label' => 'Red Española de Filosofía', 'thumb' => null],
        ['url' => 'https://www.realacademiagalega.org/', 'label' => 'Real Academia Galega', 'thumb' => null],
    ];

    echo '<ul class="link-list">';
    foreach ($sites as $s) {
        $url = $s['url'];
        $label = htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8');
        $domain = parse_url($url, PHP_URL_HOST) ?? '';
        $imgSrc = '';

        // Prefiere mini-foto local si existe
        if (!empty($s['thumb'])) {
            $fs = dirname(__DIR__, 2) . '/public' . $s['thumb'];
            if (file_exists($fs)) {
                $imgSrc = url(ltrim($s['thumb'], '/'));
            }
        }
        
        // Si no hay local, usa favicon
        if (!$imgSrc && $domain) {
            $imgSrc = 'https://www.google.com/s2/favicons?domain=' . urlencode($domain) . '&sz=32';
        }

        $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $safeImg = htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8');
        
        echo '<li>
                <a href="' . $safeUrl . '" target="_blank" rel="noopener noreferrer">
                  <img src="' . $safeImg . '" alt="" width="16" height="16" loading="lazy">
                  <span>' . $label . '</span>
                </a>
              </li>';
    }
    echo '</ul>';
    ?>
  </div>

</aside>