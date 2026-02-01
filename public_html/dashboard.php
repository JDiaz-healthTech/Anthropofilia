<?php
// public/dashboard.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\Post;
use App\Models\Page;
use App\Models\Category;

// Verificar si el usuario está logueado
$isLoggedIn = !empty($_SESSION['id_usuario']);
$userName = $_SESSION['nombre_usuario'] ?? null;

// ============================================================
// SI NO ESTÁ LOGUEADO: Mostrar mensaje y botón de login
// ============================================================
if (!$isLoggedIn) {
    $page_title = 'Dashboard - Acceso Requerido';
    require_once BASE_PATH . '/resources/views/partials/header.php';
    ?>

<main class="access-denied">
    <div class="access-denied__card">
        <h1>🔒 Dashboard de Administración</h1>
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

    // Posts paginados para la tabla
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $posts = Post::getPaginated($perPage, $offset);
    $totalPages = max(1, (int)ceil($totalPosts / $perPage)); // ← CORREGIDO

} catch (Exception $e) {
    error_log("Error cargando dashboard: " . $e->getMessage());
    $totalPosts = 0;
    $totalPages = 1; // ← CORREGIDO
    $totalPagesCount = 0;
    $totalCategories = 0;
    $posts = [];
}

$page_title = 'Dashboard - Panel de Administración';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="admin-dashboard">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <p>¡Bienvenido, <?= htmlspecialchars($userName) ?>!</p>
        </div>
        <?php if ($flash): ?>
            <div class="alert success">
                <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

    <!-- ESTADÍSTICAS BÁSICAS -->
    <section class="stats-cards">

        <div class="stat-card stat-card--posts">
            <div class="stat-card__value"><?= $totalPosts ?></div>
            <div class="stat-card__label">Entradas publicadas</div>
        </div>

        <div class="stat-card stat-card--pages">
            <div class="stat-card__value"><?= $totalPagesCount ?></div>
            <div class="stat-card__label">Páginas creadas</div>
        </div>

        <div class="stat-card stat-card--categories">
            <div class="stat-card__value"><?= $totalCategories ?></div>
            <div class="stat-card__label">Categorías activas</div>
        </div>

    </section>

<!-- ACCIONES RÁPIDAS -->
<section class="quick-actions">
    <h2>Acciones Rápidas</h2>
    <div class="quick-actions__grid">
        <a href="<?= url('crear_post.php') ?>" class="action-btn">
            <span class="action-btn__icon">+</span>
            <span>Crear Nueva Entrada</span>
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

<!-- GESTIÓN DE ENTRADAS -->
 <section class="manage-posts">

<div class="section-header">
    <h2>Gestionar Entradas</h2>
    <a href="<?= url('crear_post.php') ?>" class="action-btn">
        <span class="action-btn__icon">+</span> Nueva Entrada
    </a>
</div>
  <?php if (!empty($posts)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha de Publicación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($post['titulo']) ?></strong>
                            </td>
                            <td class="admin-table__date">
                                <?php
                                    $fecha = strtotime($post['fecha_publicacion']);
                                    echo $fecha ? date('d/m/Y', $fecha) : 'N/A';
                                ?>
                            </td>
                            <td>
                                <div class="admin-table__actions">
                                    <a href="<?= url('editar_post.php?id=' . $post['id_post']) ?>"
                                    class="btn-sm btn-sm--edit">
                                        ✏️ Editar
                                    </a>
                                        <form method="POST" action="<?= url('eliminar_post.php') ?>"
                                            style="display: inline;"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este post?');">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="id" value="<?= (int)$post['id_post'] ?>">
                                            <input type="hidden" name="origen" value="dashboard"> <!-- o "gestionar_posts" -->
                                            <button type="submit" class="btn btn-sm" style="background: #dc3545; color: white;">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

            <!-- PAGINACIÓN -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= url('dashboard.php?page=' . ($page - 1)) ?>" class="btn">« Anterior</a>
                    <?php endif; ?>

                    <span class="pagination__info">
                        Página <?= $page ?> de <?= $totalPages ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= url('dashboard.php?page=' . ($page + 1)) ?>" class="btn">Siguiente »</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
        <p class="empty-state">
            No hay entradas publicadas todavía. ¡Crea tu primera entrada!
        </p>
        <?php endif; ?>
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
        <a href="<?= url('gestionar_categorias.php') ?>" class="btn-sm">
            Ver todas las categorías →
        </a>
    </div>
</section>

</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
