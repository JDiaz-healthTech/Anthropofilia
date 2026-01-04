<?php
// public/gestionar_paginas.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Parámetros de búsqueda/paginación
$q       = trim((string)($_GET['q'] ?? ''));
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

// Construir WHERE (solo búsqueda por título, SIN filtro de usuario)
$where   = [];
$params  = [];

// Filtro por búsqueda en título (si hay q)
if ($q !== '') {
    $where[]   = 'titulo LIKE :q';
    $params[':q'] = '%' . $q . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Total para paginación
$sqlCount = "SELECT COUNT(*) FROM paginas $whereSql";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

// Listado
$sqlList = "SELECT id_pagina, slug, titulo, fecha_creacion
            FROM paginas
            $whereSql
            ORDER BY fecha_creacion DESC
            LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sqlList);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje flash
$flash = '';
if (isset($_GET['msg'])) {
    $flashMap = [
        'deleted' => 'Página eliminada correctamente.',
        'saved'   => 'Cambios guardados.',
        'created' => 'Página creada.',
    ];
    $key = (string)$_GET['msg'];
    $flash = $flashMap[$key] ?? '';
}

$page_title = 'Gestionar Páginas';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a> 
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Dashboard</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Gestionar Páginas</span>
    </nav>
 
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Gestionar Páginas</h2>
        <a href="<?= url('crear_pagina.php') ?>" class="btn btn-primary">➕ Nueva Página</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert success" style="padding: 1rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Buscador -->
    <form method="get" style="margin-bottom: 2rem; display: flex; gap: 0.5rem; align-items: center;">
        <input 
            type="text" 
            name="q" 
            placeholder="Buscar por título"
            value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
            style="flex: 1; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; min-width: 200px;"
        >
        <button type="submit" class="btn" style="padding: 0.75rem 1.5rem; white-space: nowrap;">🔍 Buscar</button>
        <?php if ($q !== ''): ?>
            <a href="<?= url('gestionar_paginas.php') ?>" class="btn" style="padding: 0.75rem 1.5rem; white-space: nowrap;">✖ Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if (!$rows): ?>
        <p style="padding: 2rem; text-align: center; color: #666; background: #f8f9fa; border-radius: 8px;">
            No hay páginas para mostrar. <?php if ($q !== ''): ?>Intenta con otra búsqueda.<?php endif; ?>
        </p>
    <?php else: ?>
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Título</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Slug</th>
                        <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #dee2e6;">Fecha Creación</th>
                        <th style="padding: 1rem; text-align: center; border-bottom: 2px solid #dee2e6;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 1rem;">
                            <strong><?= htmlspecialchars($r['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td style="padding: 1rem; color: #666; font-size: 0.9rem;">
                            <?= htmlspecialchars($r['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td style="padding: 1rem;">
                            <?php 
                                $fecha = strtotime($r['fecha_creacion']);
                                echo $fecha ? date('d/m/Y', $fecha) : 'N/A'; 
                            ?>
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <div style="display: inline-flex; gap: 0.5rem;">
                                <a href="<?= url('editar_pagina.php?id=' . (int)$r['id_pagina']) ?>" 
                                   class="btn btn-sm" 
                                   style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #ffc107; color: #000;">
                                    ✏️ Editar
                                </a>
                                
                                <form method="POST" 
                                      action="<?= url('eliminar_pagina.php') ?>" 
                                      style="display: inline;"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta página?');">
                                    <?= $security->csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$r['id_pagina'] ?>">
                                    <button type="submit" 
                                            class="btn btn-sm" 
                                            style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #dc3545; color: white; border: none; cursor: pointer;">
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

        <!-- Paginación -->
        <?php if ($pages > 1): ?>
            <nav class="pagination" style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: center;">
                <?php
                $base = url('gestionar_paginas.php') . '?';
                if ($q !== '') $base .= 'q=' . urlencode($q) . '&';
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="<?= $base . 'page=' . ($page - 1) ?>" class="btn">« Anterior</a>
                <?php endif; ?>
                
                <span style="align-self: center; padding: 0 1rem;">
                    Página <?= $page ?> de <?= $pages ?>
                </span>
                
                <?php if ($page < $pages): ?>
                    <a href="<?= $base . 'page=' . ($page + 1) ?>" class="btn">Siguiente »</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>