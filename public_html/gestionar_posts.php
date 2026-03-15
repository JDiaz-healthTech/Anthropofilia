<?php
// public/gestionar_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();

// Parámetros de búsqueda/paginación
$q       = trim((string)($_GET['q'] ?? ''));
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

// Construir WHERE
$where   = [];
$params  = [];

if ($q !== '') {
    $where[]   = 'p.titulo LIKE :q';
    $params[':q'] = '%' . $q . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Total para paginación
$sqlCount = "SELECT COUNT(*) FROM posts p $whereSql";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

// Listado con categoría
$sqlList = "SELECT p.id_post, p.slug, p.titulo, p.fecha_publicacion, c.nombre_categoria
            FROM posts p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            $whereSql
            ORDER BY p.fecha_publicacion DESC
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
        'deleted' => 'Post eliminado correctamente.',
        'saved'   => 'Cambios guardados.',
        'created' => 'Post creado.',
    ];
    $key = (string)$_GET['msg'];
    $flash = $flashMap[$key] ?? '';
}

$page_title = 'Gestionar Posts';

require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <a href="<?= url('dashboard.php') ?>">Panel de Control</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Gestionar Posts</span>
    </nav>

    <div class="section-header">
        <h2>Gestionar Posts</h2>
        <a href="<?= url('crear_post.php') ?>" class="btn btn-primary">➕ Nuevo Post</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert success">
            <?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Buscador -->
    <form method="get" class="admin-search">
        <input
            type="text"
            name="q"
            placeholder="Buscar por título"
            value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
            class="admin-search__input"
        >
        <button type="submit" class="btn admin-search__btn">🔍 Buscar</button>
        <?php if ($q !== ''): ?>
            <a href="<?= url('gestionar_post.php') ?>" class="btn admin-search__btn">✖ Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if (!$rows): ?>
        <p class="empty-state">
            No hay posts para mostrar. <?php if ($q !== ''): ?>Intenta con otra búsqueda.<?php endif; ?>
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($r['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>
                        <td class="admin-table__date">
                            <?= htmlspecialchars($r['nombre_categoria'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <?php
                                $fecha = strtotime($r['fecha_publicacion']);
                                echo $fecha ? date('d/m/Y', $fecha) : 'N/A';
                            ?>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <a href="<?= url('editar_post.php?id=' . (int)$r['id_post']) ?>"
                                   class="btn-sm btn-sm--edit">
                                    ✏️ Editar
                                </a>

                                <form method="POST"
                                      action="<?= url('eliminar_post.php') ?>"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este post?');">
                                    <?= $security->csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int)$r['id_post'] ?>">
                                    <input type="hidden" name="origen" value="gestionar_posts">
                                    <button type="submit" class="btn-sm btn-sm--delete">
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
            <nav class="pagination">
                <?php
                $base = url('gestionar_post.php') . '?';
                if ($q !== '') $base .= 'q=' . urlencode($q) . '&';
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?= $base . 'page=' . ($page - 1) ?>" class="btn">« Anterior</a>
                <?php endif; ?>

                <span class="pagination__info">
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
