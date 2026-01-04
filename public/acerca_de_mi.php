<?php 
$page_title = 'Acerca de mí';
$meta_description = 'Perfil de Ana López Sampedro: filosofía, biología y líneas de investigación en evolución del comportamiento.'; // si tu header lo usa
require_once __DIR__ . '/init.php';   // define $baseUrl y url()
  $categoria = null; // para migas condicionales
  require_once __DIR__.'/header.php';
?>


<div class="main-content-area container about">

<main>

    <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <?php if (!empty($categoria)): ?>
      <a href="<?= url('categoria.php?slug=' . urlencode($categoria['slug'])) ?>">
        <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
      </a> <span aria-hidden="true">›</span>
    <?php endif; ?>
    <span aria-current="page"><?= htmlspecialchars($page_title ?? 'Actual', ENT_QUOTES, 'UTF-8') ?></span>
  </nav>
 
  
  <article aria-labelledby="about-title">
    <header>
      <h1 id="about-title">Acerca de mí</h1>
    </header>

    <figure class="about__photo">
      <picture>
        <!-- Sirve formatos modernos si los tienes en /assets/img/ -->
        <source srcset="/assets/img/ana.avif" type="image/avif">
        <source srcset="/assets/img/ana.webp" type="image/webp">
        <img
          src="<?= url('assets/img/ana.png') ?>"          
          alt="Retrato de Ana Sampedro"
          width="200" height="200"             
          loading="lazy" decoding="async">
      </picture>
      <figcaption>Ana López Sampedro</figcaption>
    </figure>

    <section class="about__bio">
      <p>Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela y doctora en Biología por la Universidad de Vigo.</p>
      <p>Mis intereses se centran en la evolución del comportamiento humano, la ecología del comportamiento y la evolución cultural...</p>
    </section>
  </article>

  
</main>
</div> <!-- .main-content-area -->

<!-- <script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Person",
  "name":"Ana Sampedro",
  "image": "https://tudominio.com/assets/img/ana.webp",
  "jobTitle":"Doctora en Biología",
  "alumniOf":[{"@type":"CollegeOrUniversity","name":"Universidad de Santiago de Compostela"},
              {"@type":"CollegeOrUniversity","name":"Universidad de Vigo"}],
  "description":"Evolución del comportamiento humano, ecología del comportamiento y evolución cultural.",
  "url":"https://tudominio.com/acerca-de-mi"
}
</script> -->
<?php require_once 'footer.php'; ?>
