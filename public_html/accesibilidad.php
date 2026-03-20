<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
$showSidebar = false;
$page_title = 'Accesibilidad';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="legal-page">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Accesibilidad</span>
    </nav>

    <header class="legal-page__header">
        <h1>Accesibilidad</h1>
        <h2 class="legal-page__updated">Nuestro compromiso con una web para todos</h2>
    </header>

    <section class="legal-section">
        <h2>Compromiso</h2>
        <p><strong>Anthropofilia</strong> nace como un espacio educativo y cultural. Creemos que el acceso al conocimiento no debe tener barreras. Por eso trabajamos para que este sitio sea accesible para todas las personas, independientemente de sus capacidades o del dispositivo que utilicen.</p>
        <p>Nos guiamos por las <strong>Web Content Accessibility Guidelines (WCAG) 2.1</strong> del W3C, con el objetivo de cumplir el nivel AA y acercarnos al AAA en la medida de lo posible.</p>
    </section>

    <section class="legal-section">
        <h2>Funciones de accesibilidad disponibles</h2>
        <p>En la esquina inferior derecha de cada página encontrarás el botón de accesibilidad (icono de persona). Al pulsarlo se abre un panel con las siguientes opciones:</p>
        <ul>
            <li><strong>Modo oscuro:</strong> reduce el brillo de la pantalla y el cansancio visual en entornos con poca luz.</li>
            <li><strong>Alto contraste:</strong> aumenta la diferencia entre texto y fondo para personas con baja visión.</li>
            <li><strong>Aumentar / reducir texto:</strong> permite ajustar el tamaño de la fuente sin perder la estructura de la página.</li>
            <li><strong>Interlineado amplio:</strong> incrementa el espacio entre líneas, facilitando la lectura a personas con dislexia u otras dificultades de lectura.</li>
        </ul>
        <p>Todas las preferencias se guardan automáticamente y se mantienen entre visitas.</p>
    </section>

    <section class="legal-section">
        <h2>Atajos de teclado</h2>
        <p>Para quienes prefieran no usar el ratón, ofrecemos los siguientes atajos:</p>
        <ul>
            <li><strong>Tab:</strong> al pulsar Tab aparece un enlace "Saltar al contenido" que permite ir directamente al contenido principal sin recorrer el menú.</li>
            <li><strong>Alt + D:</strong> activar o desactivar el modo oscuro.</li>
            <li><strong>Alt + H:</strong> activar o desactivar el alto contraste.</li>
            <li><strong>Alt + (tecla más):</strong> aumentar el tamaño del texto.</li>
            <li><strong>Alt + (tecla menos):</strong> reducir el tamaño del texto.</li>
        </ul>
    </section>

    <section class="legal-section">
        <h2>Navegación por teclado</h2>
        <p>Todo el sitio es navegable mediante teclado. Los elementos interactivos (enlaces, botones, formularios) son accesibles con la tecla Tab y activables con Enter o Espacio. Los elementos enfocados muestran un anillo visual para facilitar la orientación.</p>
    </section>

    <section class="legal-section">
        <h2>Respeto a las preferencias del sistema</h2>
        <p>Si tu sistema operativo tiene activada la opción de <strong>reducir movimiento</strong>, las animaciones y transiciones del sitio se desactivan automáticamente. Del mismo modo, si tu sistema usa tema oscuro, el sitio lo adoptará por defecto.</p>
    </section>

    <section class="legal-section">
        <h2>Contenido multimedia</h2>
        <p>Algunos recursos embebidos (vídeos de YouTube, presentaciones de Genially o Calameo) dependen de la accesibilidad proporcionada por sus respectivas plataformas. Procuramos elegir contenidos que ofrezcan subtítulos o alternativas textuales siempre que sea posible.</p>
    </section>

    <section class="legal-section">
        <h2>Mejora continua</h2>
        <p>La accesibilidad es un camino, no un destino. Seguimos trabajando para mejorar la experiencia de todos los visitantes. Si encuentras alguna barrera o tienes sugerencias, te agradeceremos que nos lo comuniques.</p>
    </section>

    <div class="legal-contact">
        <p>¿Has encontrado una barrera de accesibilidad? Escríbenos a <a href="mailto:analosampedro@gmail.com">analosampedro@gmail.com</a></p>
    </div>

    <nav class="legal-nav" aria-label="Páginas relacionadas">
        <a href="<?= url('aviso-legal.php') ?>">Aviso Legal</a>
        <a href="<?= url('privacidad.php') ?>">Privacidad</a>
        <a href="<?= url('cookies.php') ?>">Cookies</a>
    </nav>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
