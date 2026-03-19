<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$showSidebar = false;
$page_title = 'Política de Privacidad';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="legal-page">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Política de Privacidad</span>
    </nav>

    <header class="legal-page__header">
        <h1>Política de Privacidad</h1>
        <h2 class="legal-page__updated">Última actualización: marzo 2026</h2>
    </header>

    <section class="legal-section">
        <h2>Responsable del tratamiento</h2>
        <p><strong>Anthropofilia</strong> — editora: Ana López Sampedro. Contacto: <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a>.</p>
    </section>

    <section class="legal-section">
        <h2>Datos que recopilamos</h2>
        <ul>
            <li><strong>Formulario de contacto:</strong> nombre, correo electrónico y mensaje, proporcionados voluntariamente por el usuario.</li>
            <li><strong>Datos técnicos:</strong> dirección IP (anonimizada para seguridad), agente de usuario y cookies de sesión estrictamente necesarias.</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>Finalidad</h2>
        <p>Los datos recogidos se utilizan exclusivamente para responder a las consultas recibidas, garantizar la seguridad del sitio y mejorar la experiencia de navegación. En ningún caso se ceden a terceros ni se utilizan con fines comerciales.</p>
    </section>

    <section class="legal-section">
        <h2>Conservación</h2>
        <p>Los datos personales se conservan únicamente durante el tiempo necesario para atender la solicitud y se eliminan de forma periódica. Los registros técnicos de seguridad se depuran automáticamente.</p>
    </section>

    <section class="legal-section">
        <h2>Tus derechos</h2>
        <p>Puedes ejercer en cualquier momento los derechos de acceso, rectificación, supresión y oposición sobre tus datos personales escribiendo a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a>.</p>
    </section>

    <section class="legal-section">
        <h2>Base legal</h2>
        <p>El tratamiento de los datos se basa en el consentimiento del usuario al enviar el formulario de contacto, de conformidad con el Reglamento General de Protección de Datos (RGPD).</p>
    </section>

    <div class="legal-contact">
        <p>¿Tienes alguna consulta sobre tus datos? Escríbenos a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a></p>
    </div>

    <nav class="legal-nav" aria-label="Otras páginas legales">
        <a href="<?= url('aviso-legal.php') ?>">Aviso Legal</a>
        <a href="<?= url('cookies.php') ?>">Política de Cookies</a>
    </nav>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
