<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$showSidebar = false;
$page_title = 'Política de Cookies';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="legal-page">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Política de Cookies</span>
    </nav>

    <header class="legal-page__header">
        <h1>Política de Cookies</h1>
        <h2 class="legal-page__updated">Última actualización: marzo 2026</h2>
    </header>

    <section class="legal-section">
        <h2>¿Qué son las cookies?</h2>
        <p>Las cookies son pequeños archivos de texto que los sitios web almacenan en tu navegador. Permiten recordar tus preferencias y garantizar el funcionamiento correcto del sitio.</p>
    </section>

    <section class="legal-section">
        <h2>Cookies que utilizamos</h2>
        <ul>
            <li><strong>Cookies de sesión:</strong> necesarias para el funcionamiento del sitio (autenticación, protección CSRF). Se eliminan al cerrar el navegador.</li>
            <li><strong>Cookies de preferencias:</strong> almacenan tus ajustes de accesibilidad (modo oscuro, tamaño de fuente, alto contraste). Permanecen hasta que las elimines.</li>
            <li><strong>Cookies de terceros:</strong> el contenido embebido de plataformas como YouTube, Genially o Calameo puede establecer sus propias cookies. Consulta las políticas de privacidad de cada servicio.</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>Gestión de cookies</h2>
        <p>Puedes configurar tu navegador para aceptar, rechazar o eliminar cookies en cualquier momento. Ten en cuenta que si desactivas las cookies de sesión, algunas funciones del sitio (como el acceso al panel de administración) podrían no estar disponibles.</p>
    </section>

    <section class="legal-section">
        <h2>Cómo desactivar cookies</h2>
        <ul>
            <li><strong>Chrome:</strong> Configuración → Privacidad y seguridad → Cookies</li>
            <li><strong>Firefox:</strong> Ajustes → Privacidad y seguridad → Cookies</li>
            <li><strong>Safari:</strong> Preferencias → Privacidad → Gestionar datos</li>
            <li><strong>Edge:</strong> Configuración → Privacidad → Cookies</li>
        </ul>
    </section>

    <div class="legal-contact">
        <p>¿Tienes dudas sobre las cookies? Escríbenos a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a></p>
    </div>

    <nav class="legal-nav" aria-label="Otras páginas legales">
        <a href="<?= url('aviso-legal.php') ?>">Aviso Legal</a>
        <a href="<?= url('privacidad.php') ?>">Política de Privacidad</a>
    </nav>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
