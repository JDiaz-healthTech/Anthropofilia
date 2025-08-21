<?php 
$page_title = 'Acerca de mí';
$meta_description = 'Perfil de Ana Sampedro: filosofía, biología y líneas de investigación en evolución del comportamiento.'; // si tu header lo usa
require_once 'header.php'; 
?>
<main class="container about">
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
          src="/assets/img/ana.png"            <!-- mejor local que hotlink -->
          alt="Retrato de Ana Sampedro"
          width="200" height="200"             <!-- evita CLS -->
          loading="lazy" decoding="async">
      </picture>
      <figcaption>Ana Sampedro</figcaption>
    </figure>

    <section class="about__bio">
      <p>Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela y doctora en Biología por la Universidad de Vigo.</p>
      <p>Mis intereses se centran en la evolución del comportamiento humano, la ecología del comportamiento y la evolución cultural...</p>
    </section>
  </article>
</main>
<script type="application/ld+json">
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
</script>
<?php require_once 'footer.php'; ?>
