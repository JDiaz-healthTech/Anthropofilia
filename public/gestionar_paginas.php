<?php
// gestionar_paginas.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();
$currentUserId = $security->userId();

// Parámetros de búsqueda/paginación
$q       = trim((string)($_GET['q'] ?? ''));
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

// Construir WHERE: si no es admin, sólo ve sus páginas
$where   = [];
$params  = [];

// Filtro por propiedad (no admin ⇒ sólo sus páginas)
if (!$security->isAdmin()) {
    $where[]   = 'p.id_usuario = :uid';
    $params[':uid'] = $currentUserId;
}

// Filtro por búsqueda en título (si hay q)
if ($q !== '') {
    $where[]   = 'p.titulo LIKE :q';
    $params[':q'] = '%' . $q . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Total para paginación
$sqlCount = "SELECT COUNT(*) FROM paginas p $whereSql";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

// Listado
$sqlList = "SELECT p.id_pagina, p.id_usuario, p.titulo
            FROM paginas p
            $whereSql
            ORDER BY p.id_pagina DESC
            LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sqlList);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensaje flash simple vía querystring
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

$page_title = 'Gestionar páginas';
  $categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
?>
<main>

    <nav class="breadcrumbs" aria-label="Breadcrumbs">
    <a href="<?= url('index.php') ?>">Inicio</a> <span aria-hidden="true">›</span>
    <?php if (!empty($categoria)): ?>
      <a href="<?= url('categoria.php?slug=' . urlencode($categoria['slug'])) ?>">
        <?= htmlspecialchars($categoria['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
      </a> <span aria-hidden="true">›</span>
    <?php endif; ?>
    <span aria-current="page"><?= htmlspecialchars($page_title ?? 'Actual', ENT_QUOTES, 'UTF-8') ?></span>
  </nav>
 
    <h2>Gestionar páginas</h2>

    <?php if ($flash): ?>
        <div class="alert success"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="get" class="form-inline" style="margin-bottom:1rem;">
        <input type="text" name="q" placeholder="Buscar por título"
               value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Buscar</button>
        <?php if ($q !== ''): ?>
            <a href="gestionar_paginas.php">Limpiar</a>
        <?php endif; ?>
    </form>

    <div style="margin:0 0 1rem 0;">
        <a class="button" href="crear_pagina.php">+ Nueva página</a>
    </div>

    <?php if (!$rows): ?>
        <p>No hay páginas para mostrar.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Propietario (id_usuario)</th>
                    <th style="width:220px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td>#<?php echo (int)$r['id_pagina']; ?></td>
                    <td><?php echo htmlspecialchars($r['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo (int)$r['id_usuario']; ?></td>
                    <td>
                        <!-- Enlace de edición (si tienes editar_pagina.php); si no, cámbialo por ver_pagina.php -->
                        <a class="button" href="editar_pagina.php?id=<?php echo (int)$r['id_pagina']; ?>">Editar</a>

                        <!-- Eliminar por POST con CSRF -->
                        <form action="eliminar_pagina.php" method="POST"
                              style="display:inline"
                              onsubmit="return confirm('¿Seguro que quieres eliminar esta página?');">
                            <?php
                            // Usa helper si existe; si no, token manual
                            if (method_exists($security, 'csrfField')) {
                                echo $security->csrfField();
                            } else {
                                echo '<input type="hidden" name="csrf_token" value="' .
                                     htmlspecialchars($security->csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
                            }
                            ?>
                            <input type="hidden" name="id" value="<?php echo (int)$r['id_pagina']; ?>">
                            <button type="submit" class="button danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($pages > 1): ?>
            <nav class="pagination" style="margin-top:1rem;">
                <?php
                // Simple paginador
                $base = 'gestionar_paginas.php?';
                if ($q !== '') $base .= 'q=' . urlencode($q) . '&';
                ?>
                <?php if ($page > 1): ?>
                    <a href="<?php echo $base . 'page=' . ($page - 1); ?>">&laquo; Anterior</a>
                <?php endif; ?>
                <span>Página <?php echo $page; ?> de <?php echo $pages; ?></span>
                <?php if ($page < $pages): ?>
                    <a href="<?php echo $base . 'page=' . ($page + 1); ?>">Siguiente &raquo;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

    <?php endif; ?>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
