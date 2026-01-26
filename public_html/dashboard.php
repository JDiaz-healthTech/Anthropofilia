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
    
    <main class="container" style="max-width: 600px; margin: 4rem auto; text-align: center;">
        <div style="padding: 2rem; border: 2px solid #f0f0f0; border-radius: 8px; background: #fafafa;">
            <h1 style="margin-bottom: 1rem;">🔒 Dashboard de Administración</h1>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; color: #666;">
                Para acceder al panel de administración necesitas iniciar sesión.
            </p>
            
            <a href="<?= url('login.php') ?>" class="btn" style="display: inline-block; padding: 0.75rem 2rem; background: #0645ad; color: white; text-decoration: none; border-radius: 4px; font-size: 1rem;">
                🔑 Iniciar Sesión
            </a>
            
            <p style="margin-top: 2rem; font-size: 0.9rem; color: #999;">
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

try {
    // Estadísticas básicas
    $totalPosts = Post::countAll();
    $totalPages = Page::countAll(); // Método que crearemos
    $totalCategories = Category::countAll(); // Método que crearemos
    
    // Posts paginados para la tabla
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $posts = Post::getPaginated($perPage, $offset);
    $totalPages = max(1, (int)ceil($totalPosts / $perPage));
    
} catch (Exception $e) {
    error_log("Error cargando dashboard: " . $e->getMessage());
    $totalPosts = 0;
    $totalPages = 0;
    $totalCategories = 0;
    $posts = [];
}

$page_title = 'Dashboard - Panel de Administración';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="admin-dashboard">
    <div class="admin-header" style="margin-bottom: 2rem;">
        <h1>Dashboard</h1>
        <p style="color: #666;">¡Bienvenido, <?= htmlspecialchars($userName) ?>!</p>
    </div>

    <!-- ESTADÍSTICAS BÁSICAS -->
    <section class="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        
        <div class="stat-card" style="padding: 1.5rem; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: bold;"><?= $totalPosts ?></div>
            <div style="opacity: 0.9;">Entradas publicadas</div>
        </div>
        
        <div class="stat-card" style="padding: 1.5rem; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: bold;"><?= $totalPages ?></div>
            <div style="opacity: 0.9;">Páginas creadas</div>
        </div>
        
        <div class="stat-card" style="padding: 1.5rem; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 2rem; font-weight: bold;"><?= $totalCategories ?></div>
            <div style="opacity: 0.9;">Categorías activas</div>
        </div>
        
    </section>

<!-- ACCIONES RÁPIDAS -->
<section class="quick-actions" style="margin-bottom: 3rem;">
    <h2 style="margin-bottom: 1rem;">Acciones Rápidas</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= url('crear_post.php') ?>" 
           class="btn" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
            <span style="font-size: 1.2rem;">+</span>
            <span>Crear Nueva Entrada</span>
        </a>
        <a href="<?= url('crear_pagina.php') ?>" 
           class="btn" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
            <span style="font-size: 1.2rem;">📄</span>
            <span>Crear Nueva Página</span>
        </a>
        <a href="<?= url('personalizar.php') ?>" 
           class="btn" 
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
            <span style="font-size: 1.2rem;">🎨</span>
            <span>Personalizar Diseño</span>
        </a>
    </div>
</section>

<!-- GESTIÓN DE ENTRADAS -->
<section class="manage-posts">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Gestionar Entradas</h2>
        <a href="<?= url('crear_post.php') ?>" 
           class="btn" 
           style="padding: 0.6rem 1.2rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
            <span style="font-size: 1.1rem;">+</span> Nueva Entrada
        </a>
    </div>

  <?php if (!empty($posts)): ?>
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);">
            <thead style="background: var(--page-bg); border-bottom: 2px solid var(--border);">
                <tr>
                    <th style="padding: 1rem; text-align: left; color: var(--fg); font-weight: 600;">Título</th>
                    <th style="padding: 1rem; text-align: left; color: var(--fg); font-weight: 600;">Fecha de Publicación</th>
                    <th style="padding: 1rem; text-align: center; color: var(--fg); font-weight: 600;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1rem; color: var(--fg);">
                            <strong><?= htmlspecialchars($post['titulo']) ?></strong>
                        </td>
                        <td style="padding: 1rem; color: var(--muted);">
                            <?php 
                                $fecha = strtotime($post['fecha_publicacion']);
                                echo $fecha ? date('d/m/Y', $fecha) : 'N/A'; 
                            ?>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                                <a href="<?= url('editar_post.php?id=' . $post['id_post']) ?>" 
                                   class="btn btn-sm" 
                                   style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #ffc107; color: #000; text-decoration: none; border-radius: var(--radius); font-weight: 600; white-space: nowrap;">
                                    ✏️ Editar
                                </a>
                                <form method="POST" 
                                      action="<?= url('eliminar_post.php') ?>" 
                                      style="display: inline; margin: 0;"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta entrada?');">
                                    <?= $security->csrfField() ?>
                                    <input type="hidden" name="id_post" value="<?= $post['id_post'] ?>">
                                    <button type="submit" 
                                            class="btn btn-sm" 
                                            style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #dc3545; color: white; border: none; cursor: pointer; border-radius: var(--radius); font-weight: 600; white-space: nowrap;">
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
                <nav class="pagination" style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: center;">
                    <?php if ($page > 1): ?>
                        <a href="<?= url('dashboard.php?page=' . ($page - 1)) ?>" class="btn">« Anterior</a>
                    <?php endif; ?>
                    
                    <span style="align-self: center; padding: 0 1rem;">
                        Página <?= $page ?> de <?= $totalPages ?>
                    </span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="<?= url('dashboard.php?page=' . ($page + 1)) ?>" class="btn">Siguiente »</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <p style="padding: 2rem; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
                No hay entradas publicadas todavía. ¡Crea tu primera entrada!
            </p>
        <?php endif; ?>
    </section>

    <!-- GESTIÓN DE PÁGINAS (Sección simplificada) -->
    <section class="manage-pages" style="margin-top: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2>Gestionar Páginas</h2>
            <a href="<?= url('crear_pagina.php') ?>" 
            class="btn" 
            style="padding: 0.6rem 1.2rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
                <span style="font-size: 1.1rem;">+</span> Nueva Página
            </a>
        </div>
        <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; text-align: center;">
            <p style="color: #666; margin-bottom: 0.5rem;">
                📄 Tienes <strong><?= $totalPages ?> páginas</strong> creadas
            </p>
            <a href="<?= url('gestionar_paginas.php') ?>" 
            class="btn btn-sm" 
            style="padding: 0.5rem 1rem; font-size: 0.9rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
                Ver todas las páginas →
            </a>
        </div>
    </section>

    <!-- GESTIÓN DE CATEGORÍAS -->
<section class="manage-categories" style="margin-top: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Gestionar Categorías</h2>
        <a href="<?= url('gestionar_categorias.php') ?>" 
           class="btn" 
           style="padding: 0.6rem 1.2rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius); font-weight: 600;">
            + Nueva Categoría
        </a>
    </div>
    <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; text-align: center;">
        <p style="color: #666; margin-bottom: 0.5rem;">
            📁 Tienes <strong><?= $totalCategories ?> categorías</strong> activas
        </p>
        <a href="<?= url('gestionar_categorias.php') ?>" 
           class="btn btn-sm" 
           style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: var(--brand); color: white; text-decoration: none; border-radius: var(--radius);">
            Ver todas las categorías →
        </a>
    </div>
</section>

</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>