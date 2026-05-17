<?php
// public/dashboard.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use App\Models\Sites;

// Verificar si el usuario está logueado
$isLoggedIn = !empty($_SESSION['id_usuario']);
$userName = $_SESSION['nombre_usuario'] ?? null;

// ============================================================
// SI NO ESTÁ LOGUEADO: Mostrar mensaje y botón de login
// ============================================================
if (!$isLoggedIn) {
    $page_title = 'Panel de Control - Acceso Requerido';
    require_once BASE_PATH . '/resources/views/partials/header.php';
    ?>

<main class="access-denied">
    <div class="access-denied__card">
        <h1>🔒 Panel de Control de Administración</h1>
        <p>Para acceder al panel de administración necesitas iniciar sesión.</p>

        <a href="<?= url('login.php') ?>" class="btn action-btn">
            🔑 Iniciar Sesión
        </a>

        <p class="access-denied__note">
            💡 <em>Próximamente: Registro de usuarios para funciones colaborativas</em>
        </p>
    </div>
</main>
    <?php
    require_once BASE_PATH . '/resources/views/partials/footer.php';
    exit();
}

// ============================================================
// SI ESTÁ LOGUEADO: Cargar estadísticas y mostrar dashboard
// ============================================================

// Usuarios estándar no acceden al panel de administración
$userRole = $_SESSION['rol'] ?? 'usuario';
if ($userRole === 'usuario') {
    header('Location: dashboard_usuario.php');
    exit();
}

// Mensaje flash
$flash = '';
if (isset($_GET['msg'])) {
    $flashMap = [
        'deleted' => 'Entrada eliminada correctamente.',
        'created' => 'Entrada creada correctamente.',
        'updated' => 'Entrada actualizada correctamente.',
    ];
    $key = (string)$_GET['msg'];
    $flash = $flashMap[$key] ?? '';
}

try {
    // Estadísticas básicas
    $totalPosts = Post::countAll();
    $totalPagesCount = Page::countAll();
    $totalCategories = Category::countAll();
    $totalSites = Sites::countAll();

} catch (Exception $e) {
    error_log("Error cargando Panel de Control: " . $e->getMessage());
    $totalPosts = 0;
    $totalPagesCount = 0;
    $totalCategories = 0;
    $totalSites = 0;
}
$page_title = 'Panel de Control - Administración';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="admin-dashboard">
        <div class="admin-header">
            <h1>Panel de Control</h1>
            <p>¡Bienvenid@, <?= htmlspecialchars($userName) ?>!</p>
        </div>
        <?php if ($flash): ?>
            <div class="alert success">
                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

    <!-- ESTADÍSTICAS BÁSICAS -->
    <section class="stats-cards stats-cards--compact">

        <div class="stat-card stat-card--posts">
            <div class="stat-card__value"><?= $totalPosts ?></div>
            <div class="stat-card__label">Posts</div>
        </div>

        <div class="stat-card stat-card--pages">
            <div class="stat-card__value"><?= $totalPagesCount ?></div>
            <div class="stat-card__label">Páginas</div>
        </div>

        <div class="stat-card stat-card--categories">
            <div class="stat-card__value"><?= $totalCategories ?></div>
            <div class="stat-card__label">Categorías</div>
        </div>

        <div class="stat-card stat-card--sites">
            <div class="stat-card__value"><?= $totalSites ?></div>
            <div class="stat-card__label">Sitios</div>
        </div>

    </section>

<!-- ACCIONES RÁPIDAS -->
<section class="quick-actions">
    <h2>Acciones Rápidas</h2>
    <div class="quick-actions__grid">
        <a href="<?= url('crear_post.php') ?>" class="action-btn">
            <span class="action-btn__icon">+</span>
            <span>Crear Nuevo Post</span>
        </a>
        <a href="<?= url('crear_pagina.php') ?>" class="action-btn">
            <span class="action-btn__icon">📄</span>
            <span>Crear Nueva Página</span>
        </a>
        <a href="<?= url('personalizar.php') ?>" class="action-btn">
            <span class="action-btn__icon">🎨</span>
            <span>Personalizar Diseño</span>
        </a>
    </div>
    </section>
    <!-- GESTIÓN DE POSTS -->
    <section class="manage-posts">
        <div class="section-header">
            <h2>Gestionar Posts</h2>
            <a href="<?= url('crear_post.php') ?>" class="action-btn">
                <span class="action-btn__icon">+</span> Nuevo Post
            </a>
        </div>
        <div class="info-card">
            <p>📝 Tienes <strong><?= $totalPosts ?> posts</strong> publicados</p>
            <a href="<?= url('gestionar_posts.php') ?>" class="btn-link">
                Ver todos los posts →
            </a>
        </div>
    </section>

    <!-- GESTIÓN DE PÁGINAS (Sección simplificada) -->
<section class="manage-pages">
    <div class="section-header">
        <h2>Gestionar Páginas</h2>
        <a href="<?= url('crear_pagina.php') ?>" class="action-btn">
            <span class="action-btn__icon">+</span> Nueva Página
        </a>
    </div>
        <div class="info-card">
            <p>📄 Tienes <strong><?= $totalPagesCount ?> páginas</strong> creadas</p>
            <a href="<?= url('gestionar_paginas.php') ?>" class="btn-sm">
                Ver todas las páginas →
            </a>
        </div>
    </section>

    <!-- GESTIÓN DE CATEGORÍAS -->
    <section class="manage-categories">
        <div class="section-header">
            <h2>Gestionar Categorías</h2>
            <a href="<?= url('gestionar_categorias.php') ?>" class="action-btn">
                <span class="action-btn__icon">+</span> Nueva Categoría
            </a>
        </div>
        <div class="info-card">
            <p>📁 Tienes <strong><?= $totalCategories ?> categorías</strong> activas</p>
            <a href="<?= url('gestionar_categorias.php') ?>" class="btn-link">
                Ver todas las categorías →
            </a>
        </div>
    </section>

    <!-- GESTIÓN DE SITIOS DE INTERÉS -->
    <section class="manage-sites">
        <div class="section-header">
            <h2>Gestionar Sitios de Interés</h2>
            <a href="<?= url('gestionar_sitios.php') ?>" class="action-btn">
                <span class="action-btn__icon">+</span> Nuevo Sitio
            </a>
        </div>
        <div class="info-card">
            <p>🔗 Enlaces externos en la sidebar</p>
            <a href="<?= url('gestionar_sitios.php') ?>" class="btn-link">
                Ver todos los sitios →
            </a>
        </div>
    </section>





</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
