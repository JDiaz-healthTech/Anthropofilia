<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\User;
use App\Models\UserPage;

$security->requireLogin();

$userId = (int) $security->userId();

// Datos propios del usuario
$usuario = User::findById($userId);

// Páginas que sigue (sus "páginas")
$paginasSeguidas = UserPage::getFollowedPages($userId);
$totalSeguidas   = count($paginasSeguidas);

// Nombre para el saludo
$userName = $_SESSION['nombre_usuario'] ?? '';
$nombre   = explode(' ', $userName)[0];

$page_title = 'Mi Cuenta';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container" id="content">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Mi Cuenta</span>
    </nav>

    <div class="admin-header">
        <h1>Mi Cuenta</h1>
        <p>¡Bienvenid@, <?= e($nombre) ?>!</p>
    </div>

    <!-- Resumen / stats -->
    <section class="stats-cards stats-cards--compact" aria-label="Resumen de tu cuenta">
        <div class="stat-card stat-card--pages">
            <div class="stat-card__value"><?= (int) $totalSeguidas ?></div>
            <div class="stat-card__label">
                <?= e(pluralize($totalSeguidas, 'asignatura seguida', 'asignaturas seguidas')) ?>
            </div>
        </div>
        <?php if (!empty($usuario['fecha_registro'])): ?>
        <div class="stat-card stat-card--date">
            <div class="stat-card__value"><?= e(format_date_es($usuario['fecha_registro'])) ?></div>
            <div class="stat-card__label">Miembro desde</div>
        </div>
        <?php endif; ?>
    </section>

    <!-- Mis paginas -->
    <section class="paginas">
        <h2>Mis páginas</h2>

        <?php if ($totalSeguidas === 0): ?>
            <p class="empty-state">
                Aún no sigues ninguna página. Explora el contenido y pulsa
                <strong>"Seguir esta página"</strong> para añadirla aquí.
            </p>
        <?php else: ?>
            <div class="paginas-grid">
                <?php foreach ($paginasSeguidas as $pag): ?>
                    <a class="paginas-card" href="<?= url('pagina.php?slug=' . urlencode($pag['slug'])) ?>">
                        <h3 class="paginas-card__titulo"><?= e($pag['titulo']) ?></h3>
                        <span class="paginas-card__meta">
                            Siguiendo desde <?= e(format_date_es($pag['seguida_desde'])) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Mis datos -->
    <section class="datos-perfil">
        <h2>Mis datos</h2>
        <dl class="datos-perfil__lista">
            <dt>Nombre</dt>
            <dd><?= e($usuario['nombre_usuario'] ?? $userName) ?></dd>
            <dt>Email</dt>
            <dd><?= e($usuario['email'] ?? '') ?></dd>
        </dl>
    </section>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
