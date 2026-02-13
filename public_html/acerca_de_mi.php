<?php
$page_title = 'Acerca de mí';
$meta_description = 'Perfil de Ana López Sampedro: filosofía, biología y líneas de investigación.';
require_once __DIR__ . '/init.php';
$showSidebar = true;
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main>
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Perfil</span>
    </nav>

    <article class="about-article">

        <h1 class="visually-hidden">Acerca de Ana López Sampedro</h1>

        <div class="about-grid">

            <figure class="about__photo">
                <img
                    src="<?= url('assets/img/MiFoto.jpg') ?>"
                    alt="Retrato de Ana López Sampedro"
                    loading="lazy"
                    decoding="async">
            </figure>

            <div class="about__content">

                <header>
                    <p class="about__role">Filosofía & Historia</p>
                    <h2 class="about__name">Ana</h2>
                    <h2 class="about__name">López</h2>
                    <h2 class="about__name">Sampedro</h2>
                </header>

                <div class="about__bio">
                    <p class="lead-text">Soy Ana López Sampedro. He sido profesora de filosofía en bachillerato durante más de tres décadas y en ese tiempo he debido elaborar múltiples materiales para mi alumnado. Mi última etapa profesional ha resultado fructífera gracias a la experiencia de tantos años y al feedback recibido. Esto unido a las enormes posibilidades que brindan las nuevas tecnologías me permitió crear materiales que, a juzgar por los resultados de tantos chicos y chicas en las pruebas de acceso a la universidad, funcionan. </p>
                    <p>Echo de menos el “feedback” de la docencia, ese enriquecimiento recíproco que siempre me resultó tan gratificante. Sé que son múltiples los recursos, hay muchos materiales versátiles, interactivos y “nutritivos”; aún así no me resisto a realizar mi contribución. Si os sirve de algo, estoy aquí, dispuesta a continuar aunque de otra manera. Podréis preguntarme las dudas que se os presenten, plantearme las dificultades, compartir experiencias...  y -aunque no haré vuestros trabajos- contar conmigo como apoyo en el aprendizaje de la más transversal de todas las materias. </p>
                    <p>Esta página, elaborada por mi hijo, permite que nos pongamos en <a href="contacto.php">contacto</a>, que haya intercambio de ideas. No lo dudéis.</p>                    <p>Ana.</p>
                 </div>

            </div>
        </div>
    </article>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
