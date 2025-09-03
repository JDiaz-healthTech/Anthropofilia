<?php
// sidebar.php
declare(strict_types=1);

// Se asume que init.php ya fue requerido por la página que incluye este sidebar
// y que existe $pdo.

?>
<aside>
  <div class="sidebar-widget">
    <h4>Buscar</h4>
    <form action="<?= url('search.php') ?>" method="GET" class="form-container" style="padding:0;border:none;margin:0;">
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
    <a href="<?= url('acerca_de_mi.php') ?>">Leer más</a>
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
                echo '<li><a href="' . url('categoria.php?slug=' . $slug) . '">' . $name . '</a></li>';
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

$mesesES = [1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'];
try {
  $sql = "SELECT YEAR(fecha_publicacion) anio, MONTH(fecha_publicacion) mes, COUNT(*) total
          FROM posts GROUP BY anio, mes ORDER BY anio DESC, mes DESC";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

  // Agrupar por año
  $byYear = [];
  foreach ($rows as $r) { $byYear[(int)$r['anio']][] = $r; }

  echo '<ul class="archive-tree">';
  foreach ($byYear as $anio => $meses) {
    $countYear = array_sum(array_map(fn($r)=> (int)$r['total'], $meses));
    echo '<li><details'.($anio===date('Y')?' open':'').'><summary>'
        . $anio . ' <span class="count">(' . $countYear . ')</span></summary><ul>';
    // meses con enlace
    foreach ($meses as $r) {
      $mesNum = (int)$r['mes'];
      $href = url('archivo.php?anio='.$anio.'&mes='.$mesNum);
      $label = ucfirst($mesesES[$mesNum]).' ('.$r['total'].')';
      echo '<li><a href="'.htmlspecialchars($href,ENT_QUOTES,'UTF-8').'">'.$label.'</a></li>';
    }
    echo '</ul></details></li>';
  }
  echo '</ul>';
} catch (Throwable $e) {
  echo '<p>No se pudo cargar el archivo.</p>';
}

    ?>
  </div>

    <div class="sidebar-widget">
    <h4>Sitios de interés</h4>
    <?php
      // Define aquí tus sitios, con opción a mini-foto local si existe
      $sites = [
        ['url' => 'https://www.atapuerca.org/', 'label' => 'Fundación Atapuerca', 'thumb' => '/images/links/atapuerca.jpg'],
        ['url' => 'https://www.cenieh.es/',      'label' => 'CENIEH',              'thumb' => '/images/links/cenieh.jpg'],
        ['url' => 'https://www.iphes.cat/',      'label' => 'IPHES',               'thumb' => '/images/links/iphes.jpg'],
      ];

      echo '<ul class="link-thumbs">';
      foreach ($sites as $s) {
        $u = $s['url'];
        $label = htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8');
        $domain = parse_url($u, PHP_URL_HOST) ?? '';
        $local = $s['thumb'] ?? null;
        $imgSrc = '';

        // Prefiere mini-foto local si existe
        if ($local) {
          $fs = $_SERVER['DOCUMENT_ROOT'] . $local;
          if (@is_file($fs)) {
            $imgSrc = $local;
          }
        }
        // Si no hay local, usa el favicon como placeholder
        if (!$imgSrc && $domain) {
          $imgSrc = 'https://www.google.com/s2/favicons?domain=' . urlencode($domain) . '&sz=64';
        }

        $safeUrl = htmlspecialchars($u, ENT_QUOTES, 'UTF-8');
        $safeImg = htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8');
        echo '<li>
                <a class="link-with-thumb" href="'.$safeUrl.'" target="_blank" rel="noopener noreferrer">
                  <img src="'.$safeImg.'" alt="" aria-hidden="true" loading="lazy" width="22" height="22">
                  <span>'.$label.'</span>
                </a>
              </li>';
      }
      echo '</ul>';
    ?>
  </div>


</aside>
