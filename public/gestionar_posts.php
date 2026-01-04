<?php
// gestionar_posts.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$security->requireLogin();
$isAdmin = $security->isAdmin();
$userId  = $security->userId();

// --- Parámetros (búsqueda + paginación sencilla) ---
$q       = trim((string)($_GET['q'] ?? ''));
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

// --- Filtros ---
$where  = [];
$params = [];

if (!$isAdmin) {
    $where[]        = 'p.id_usuario = :uid';
    $params[':uid'] = $userId;
}
if ($q !== '') {
    $where[]        = 'p.titulo LIKE :q';
    $params[':q']   = '%' . $q . '%';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// --- Total para paginación ---
$sqlCount = "SELECT COUNT(*) FROM posts p $whereSql";
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

// --- Listado ---
$sql = "SELECT p.id_post, p.titulo, p.fecha_publicacion, p.id_usuario
        FROM posts p
        $whereSql
        ORDER BY p.fecha_publicacion DESC, p.id_post DESC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensajes tipo flash por querystring
$flash = '';
if (isset($_GET['msg'])) {
    $flash = match ((string)$_GET['msg']) {
        'deleted' => 'Post eliminado correctamente.',
        'saved'   => 'Post guardado.',
        'created' => 'Post creado.',
        default   => ''
    };
}

$page_title = 'Gestionar Posts';
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
 
    <h2>Gestionar Posts</h2>

    <?php if ($flash): ?>
        <div class="alert success"><?php echo htmlspecialchars($flash, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="get" class="form-inline" style="margin-bottom:1rem;">
        <input type="text" name="q" placeholder="Buscar por título"
               value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Buscar</button>
        <?php if ($q !== ''): ?>
            <a href="gestionar_posts.php">Limpiar</a>
        <?php endif; ?>
    </form>

    <div style="margin:0 0 1rem 0;">
        <a class="button" href="crear_post.php">+ Nuevo post</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Fecha de Publicación</th>
                <?php if ($isAdmin): ?><th>Autor (id_usuario)</th><?php endif; ?>
                <th style="width:220px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($posts): ?>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['titulo'] ?? '(sin título)', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php
                        $ts = $post['fecha_publicacion'] ? strtotime((string)$post['fecha_publicacion']) : null;
                        echo $ts ? date('d/m/Y', $ts) : '-';
                        ?>
                    </td>
                    <?php if ($isAdmin): ?>
                        <td><?php echo (int)$post['id_usuario']; ?></td>
                    <?php endif; ?>
<td class="actions">
  <a class="button small"  href="editar_post.php?id=<?= (int)$post['id_post'] ?>">Editar</a>

  <form action="eliminar_post.php" method="POST" class="inline-form"
        onsubmit="return confirm('¿Seguro que quieres eliminar este post?');">
      <?= $security->csrfField(); ?>
      <input type="hidden" name="id" value="<?= (int)$post['id_post']; ?>">
      <button type="submit" class="button danger small">Eliminar</button>
  </form>
</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="<?php echo $isAdmin ? 4 : 3; ?>">No hay posts para mostrar.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <nav class="pagination" style="margin-top:1rem;">
            <?php
            $base = 'gestionar_posts.php?';
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
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
