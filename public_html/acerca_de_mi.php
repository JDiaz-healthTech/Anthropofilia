<?php 
$page_title = 'Acerca de mí';
$meta_description = 'Perfil de Ana López Sampedro: filosofía, biología y líneas de investigación en evolución del comportamiento.'; 
require_once __DIR__ . '/init.php';   // define $baseUrl y url()
$categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
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
 
        <article class="about-article" aria-labelledby="about-title">
            
            <header class="about-header">
                <h1 id="about-title">Acerca de mí</h1>
            </header>

            <div class="about-grid">
                
                <figure class="about__photo">
                    <img
                        src="<?= url('assets/img/MiFoto.jpg') ?>" 
                        alt="Retrato de Ana Sampedro"
                        loading="lazy" 
                        decoding="async">
                    <figcaption>Ana López Sampedro</figcaption>
                </figure>

                <section class="about__bio">
                    <p class="lead-text">Mi nombre es Ana y soy licenciada en Filosofía por la Universidad de Santiago de Compostela.</p>
                    
                    <p>Mis intereses se centran en la evolución del comportamiento humano, la ecología del comportamiento y la evolución cultural...</p>
                    
                    </section>

            </div> </article>

    </main>
</div> <?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>