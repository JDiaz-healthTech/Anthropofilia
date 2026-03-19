<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$showSidebar = false;
$page_title = 'Aviso Legal';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="legal-page">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Aviso Legal</span>
    </nav>

    <header class="legal-page__header">
        <h1>Aviso Legal</h1>
        <h2 class="legal-page__updated">Última actualización: marzo 2026</h2>
    </header>

    <section class="legal-section">
        <h2>Titular del sitio</h2>
        <p><strong>Anthropofilia</strong> es un sitio web de divulgación educativa y cultural editado por Ana López Sampedro, con domicilio en España.</p>
    </section>

    <section class="legal-section">
        <h2>Propiedad intelectual</h2>
        <p>Los textos, imágenes, diseños y demás contenidos publicados en este sitio son propiedad de la editora o de sus respectivos autores, salvo que se indique expresamente lo contrario. Queda prohibida su reproducción, distribución o comunicación pública sin autorización previa y por escrito.</p>
        <p>Los materiales didácticos compartidos tienen finalidad exclusivamente educativa. Si eres docente y deseas reutilizar algún recurso, no dudes en ponerte en contacto.</p>
    </section>

    <section class="legal-section">
        <h2>Exención de responsabilidad</h2>
        <p>El contenido de este sitio tiene carácter divulgativo. Aunque se procura la máxima exactitud, no se garantiza que la información sea exhaustiva ni esté libre de errores. El uso que el visitante haga de los contenidos es de su exclusiva responsabilidad.</p>
    </section>

    <section class="legal-section">
        <h2>Enlaces a terceros</h2>
        <p>Este sitio puede contener enlaces a recursos externos (YouTube, Genially, Calameo, entre otros). Anthropofilia no controla ni se responsabiliza del contenido, las políticas de privacidad o la disponibilidad de dichos sitios.</p>
    </section>

    <div class="legal-contact">
        <p>¿Tienes alguna consulta? Escríbenos a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a></p>
    </div>

    <nav class="legal-nav" aria-label="Otras páginas legales">
        <a href="<?= url('privacidad.php') ?>">Política de Privacidad</a>
        <a href="<?= url('cookies.php') ?>">Política de Cookies</a>
    </nav>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
