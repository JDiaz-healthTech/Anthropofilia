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
                    <p class="lead-text">
                        Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela.
                    </p>

                    <p>
                        Mis intereses se centran en la evolución del comportamiento humano, la ecología del comportamiento y la evolución cultural. A través de la intersección entre la biología y la filosofía, busco comprender los mecanismos que han moldeado nuestra naturaleza social.
                    </p>

                    <p>
                        Actualmente, mi investigación explora cómo los patrones culturales influyen en la adaptación biológica...
                    </p>
                </div>

            </div>
        </div>
    </article>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
