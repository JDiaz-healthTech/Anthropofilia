<?php
// header.php
declare(strict_types=1);

// =================================================================
// SECCIÓN 1: CONFIGURACIÓN INICIAL
// =================================================================

$current_page = basename($_SERVER['SCRIPT_NAME']);
$nonce = (isset($security) && method_exists($security, 'cspNonce'))
    ? $security->cspNonce()
    : null;
$isLogged = isset($security) && method_exists($security, 'userId')
    ? (bool)$security->userId()
    : false;

// =================================================================
// SECCIÓN 2: URLs Y CANONICAL
// =================================================================

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$baseUrl = $_ENV['APP_URL'] ?? ($scheme . '://' . $host);
$canonical = rtrim($baseUrl, '/') . '/' . ltrim($path, '/');

// =================================================================
// SECCIÓN 3: CONFIGURACIÓN DE TEMA
// =================================================================

$themeConfig = [
    'primary_color' => get_setting($pdo, 'theme_primary_color', '#0645ad'),
    'bg_color' => get_setting($pdo, 'theme_bg_color', '#ffffff'),
    'header_bg_url' => get_setting($pdo, 'header_bg_url', ''),
];

// Validar colores
if (!preg_match('/^#[a-fA-F0-9]{6}$/', $themeConfig['primary_color'])) {
    $themeConfig['primary_color'] = '#0645ad';
}
if (!preg_match('/^#[a-fA-F0-9]{6}$/', $themeConfig['bg_color'])) {
    $themeConfig['bg_color'] = '#ffffff';
}

// Normalizar URL de imagen de cabecera
$headerPath = trim($themeConfig['header_bg_url']);
if ($headerPath !== '') {
    // Si es una URL absoluta con http/https, reemplazar el host
    if (preg_match('#^https?://([^/]+)(.*)$#i', $headerPath, $matches)) {
        $currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $imagePath = $matches[2]; // La parte después del dominio (ej: /uploads/header.jpg)
        $themeConfig['header_bg_url'] = $scheme . '://' . $currentHost . $imagePath;
    }
    // Si es una ruta relativa, convertir a absoluta
    elseif (!preg_match('#^/#', $headerPath)) {
        $themeConfig['header_bg_url'] = $scheme . '://' . $host . '/' . ltrim($headerPath, '/');
    }
    // Si ya empieza con /, está bien (ruta absoluta del servidor)
    else {
        $themeConfig['header_bg_url'] = $scheme . '://' . $host . $headerPath;
    }
}
// =================================================================
// SECCIÓN 4: LÓGICA DEL HEADER (ANTES DEL HTML)
// =================================================================

$hasHeaderBg = !empty($themeConfig['header_bg_url']);
$headerClass = $hasHeaderBg ? 'main-header has-bg-image' : 'main-header';
$headerStyle = '';

if ($hasHeaderBg) {
    $headerStyle = sprintf(
        ' style="background-image: url(\'%s\');"',
        htmlspecialchars($themeConfig['header_bg_url'], ENT_QUOTES, 'UTF-8')
    );
}

// =================================================================
// SECCIÓN 5: CONFIGURACIÓN DE PÁGINAS
// =================================================================

// Páginas que requieren TinyMCE
$needsTinymce = in_array($current_page, [
    'crear_post.php',
    'editar_post.php',
    'crear_pagina.php',
    'editar_pagina.php'
], true);

// Páginas que no deben indexarse
$noindexPages = [
    'login.php', 'admin.php', 'dashboard.php', 'gestionar_paginas.php',
    'crear_post.php', 'editar_post.php', 'eliminar_post.php',
    'crear_pagina.php', 'editar_pagina.php', 'eliminar_pagina.php'
];

// Cargar páginas para el menú
$menuPages = [];
try {
    $stmtPages = $pdo->query("SELECT slug, titulo FROM paginas ORDER BY orden ASC, id_pagina ASC");
    $menuPages = $stmtPages->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error cargando páginas para menú: " . $e->getMessage());
}

// =================================================================
// HELPER FUNCTIONS
// =================================================================

function nav_active(string $file, ?string $slug = null): string {
    $currFile = basename($_SERVER['SCRIPT_NAME']);
    if ($slug !== null) {
        return ($currFile === 'pagina.php' && (($_GET['slug'] ?? null) === $slug))
            ? ' class="active"'
            : '';
    }
    return ($currFile === $file) ? ' class="active"' : '';
}

?>
<!DOCTYPE html>
<html lang="es"
      data-primary-color="<?= htmlspecialchars($themeConfig['primary_color'], ENT_QUOTES, 'UTF-8') ?>"
      data-bg-color="<?= htmlspecialchars($themeConfig['bg_color'], ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#111111" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="color-scheme" content="light dark">

    <title><?= htmlspecialchars($page_title ?? 'Anthropofilia Blog', ENT_QUOTES, 'UTF-8') ?></title>

    <?php if (!empty($meta_description)): ?>
    <meta name="description" content="<?= htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">

    <?php if (in_array($current_page, $noindexPages, true)): ?>
    <meta name="robots" content="noindex, nofollow">
    <?php endif; ?>

    <!-- Google Fonts: Nunito Sans (body) + Playfair Display (headers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,400&display=swap">

    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">

    <!-- CSS específico por página -->
    <?php if (in_array($current_page, ['post.php'], true)): ?>
    <link rel="stylesheet" href="<?= url('css/pages/post.css') ?>">
    <?php endif; ?>

    <?php if ($current_page === 'pagina.php'): ?>
    <link rel="stylesheet" href="<?= url('css/components/toc.css') ?>">
    <?php endif; ?>

    <?php if ($needsTinymce): ?>
    <!-- TinyMCE Self-Hosted -->
    <script src="<?= url('js/tinymce/tinymce.min.js') ?>"
            <?= $nonce ? ' nonce="'.htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8').'"' : '' ?>></script>
    <?php endif; ?>

    <!-- JavaScript -->
    <script src="<?= url('js/theme-init.js') ?>" defer></script>

    <!-- Script para modo oscuro (antes de cargar la página) -->
    <script>
    (function(){
        try {
            const dark = localStorage.getItem('a11y_dark');
            if (dark === '1' || dark === 'true') {
                document.documentElement.classList.add('theme-dark');
                document.documentElement.classList.remove('theme-light');
            } else {
                document.documentElement.classList.remove('theme-dark');
                document.documentElement.classList.add('theme-light');
            }
        } catch (e) { /* noop */ }
    })();
    </script>
</head>
<body id="top">
    <div class="container">

        <!-- =================================================
             HEADER CON IMAGEN DE FONDO
             ================================================= -->
        <header class="<?= $headerClass ?>" role="banner"<?= $headerStyle ?>>
            <div class="header-overlay"></div>
            <div class="header-content">
                <h1>ANTHROPOFILIA</h1>
                <h4>Ana López Sampedro</h4>
                <p><i>Por un pensamento propio.</i></p>
            </div>
        </header>

        <!-- =================================================
             NAVEGACIÓN
             ================================================= -->
        <nav class="main-nav" role="navigation" aria-label="Principal">
            <button class="mobile-nav-toggle" aria-controls="main-nav-links" aria-expanded="false">
                <span class="visually-hidden">Menú</span>
                <span class="hamburger-icon"><span></span></span>
            </button>

            <div class="main-nav-links" id="main-nav-links">
                <!-- Enlace fijo: Inicio -->
                <a href="<?= url('index.php') ?>"<?= nav_active('index.php') ?>>Inicio</a>

                <!-- Páginas dinámicas desde BD -->
                <?php foreach ($menuPages as $page): ?>
                    <a href="<?= url('pagina.php?slug=' . urlencode($page['slug'])) ?>"<?= nav_active('pagina.php', $page['slug']) ?>>
                        <?= htmlspecialchars($page['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>

                <!-- Enlaces fijos adicionales -->
                <a href="<?= url('acerca_de_mi.php') ?>"<?= nav_active('acerca_de_mi.php') ?>>Acerca de mí</a>
                <a href="<?= url('contacto.php') ?>"<?= nav_active('contacto.php') ?>>Contacto</a>

                <span class="spacer"></span>

                <!-- Dashboard -->
                <!-- Acceso / Panel de Control -->
                <?php if ($isLogged): ?>
                    <?php
                    $userRole = $_SESSION['rol'] ?? 'usuario';
                    $isAdmin = in_array($userRole, ['administrador', 'autor']);
                    ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= url('dashboard.php') ?>"<?= nav_active('dashboard.php') ?>>Panel de Control</a>
                    <?php else: ?>
                        <a href="<?= url('mi_cuenta.php') ?>"<?= nav_active('mi_cuenta.php') ?>>Mi Cuenta</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= url('login.php') ?>"<?= nav_active('login.php') ?> rel="nofollow">Acceder</a>
                <?php endif; ?>
            </div>

            <!-- Controles de accesibilidad -->
            <div class="accessibility-controls">
                <button id="toggle-dark" title="Alternar claro/oscuro" class="accessibility-button" aria-pressed="false">🌗</button>
                <button id="toggle-high-contrast" title="Alternar alto contraste" class="accessibility-button" aria-pressed="false">HC</button>
                <button id="increase-font-size" title="Aumentar tamaño de fuente" class="accessibility-button">A+</button>
                <button id="decrease-font-size" title="Disminuir tamaño de fuente" class="accessibility-button">A−</button>
            </div>
        </nav>

        <!-- =================================================
             INICIO DEL CONTENIDO PRINCIPAL
             ================================================= -->
        <div class="main-content-area">
<?php
