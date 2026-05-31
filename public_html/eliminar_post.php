<?php
// eliminar_post.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\PaginaPost;

$security->requireLogin();

// Forzar POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    $security->abort(405, 'Método no permitido.');
}

// CSRF
$security->requireValidCsrf();

// ID desde POST
$post_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$post_id || $post_id <= 0) {
    $security->abort(400, 'ID de post inválido.');
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1) Cargar el post
    $stmt = $pdo->prepare('SELECT id_post, id_usuario, titulo, imagen_destacada_url FROM posts WHERE id_post = ?');
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        $security->abort(404, 'Post no encontrado.');
    }

    // 2) Verificar ownership (solo el autor o admin pueden borrar)
    $security->requireOwnershipOrRole((int)$post['id_usuario'], ['admin']);

    // 3) Verificar si está en páginas
    $paginaPost = new PaginaPost($pdo);
    $paginasAfectadas = $paginaPost->getPaginasWithPost($post_id);

    // 4) Si hay confirmación, proceder con el borrado
    $confirmado = ($_POST['confirmado'] ?? '') === 'si';

    if (!$confirmado) {
        // Mostrar página de confirmación
        $page_title = 'Confirmar Eliminación';
        $categoria = null;
        require_once BASE_PATH . '/resources/views/partials/header.php';
        ?>

        <main class="container">
            <div class="confirmation-box" style="max-width: 600px; margin: 3rem auto; padding: 2rem; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px;">
                <h1 style="color: #856404; margin-bottom: 1rem;">⚠️ Confirmar Eliminación</h1>

                <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                    Vas a eliminar el post:
                </p>
                <p style="font-weight: bold; font-size: 1.2rem; margin-bottom: 1.5rem;">
                    "<?= htmlspecialchars($post['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                </p>

                <?php if (!empty($paginasAfectadas)): ?>
                    <div style="background: white; padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem;">
                        <h2 style="color: #dc3545; margin-bottom: 1rem;">⚠️ Este post aparece en <?= count($paginasAfectadas) ?> página(s):</h2>
                        <ul style="margin-left: 1.5rem; line-height: 1.8;">
                            <?php foreach ($paginasAfectadas as $pag): ?>
                                <li>
                                    📄 <strong><?= htmlspecialchars($pag['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <a href="<?= url('pagina.php?slug=' . urlencode($pag['slug'])) ?>" target="_blank" style="margin-left: 0.5rem; font-size: 0.9rem;">
                                        Ver página →
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div style="background: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                    <p style="margin-bottom: 0.5rem;"><strong>Si continúas:</strong></p>
                    <ul style="margin-left: 1.5rem; line-height: 1.8;">
                        <li>✓ El post se eliminará permanentemente</li>
                        <li>✓ Desaparecerá de estas páginas automáticamente</li>
                        <li>✓ Se perderá su contenido e imágenes</li>
                    </ul>
                </div>

                <form method="POST" style="display: flex; gap: 1rem; justify-content: center;">
                    <?= $security->csrfField() ?>
                    <input type="hidden" name="id" value="<?= $post_id ?>">
                    <input type="hidden" name="confirmado" value="si">
                    <input type="hidden" name="origen" value="<?= htmlspecialchars($_POST['origen'] ?? 'gestionar_posts', ENT_QUOTES, 'UTF-8') ?>">
                    <a href="<?= url('gestionar_posts.php') ?>" class="btn" style="background: #6c757d;">
                        Cancelar
                    </a>
                    <button type="submit" class="btn" style="background: #dc3545; color: white;">
                        Sí, Eliminar Post
                    </button>
                </form>
            </div>
        </main>

        <?php
        require_once BASE_PATH . '/resources/views/partials/footer.php';
        exit();
    }

    // 5) Proceder con el borrado (confirmado o sin páginas afectadas)
    $pdo->beginTransaction();

    // Eliminar relaciones con etiquetas
    $stmt = $pdo->prepare('DELETE FROM post_etiquetas WHERE id_post = ?');
    $stmt->execute([$post_id]);

    // Eliminar relaciones con páginas (ON DELETE CASCADE lo hace automáticamente, pero lo hacemos explícito)
    $stmt = $pdo->prepare('DELETE FROM pagina_posts WHERE id_post = ?');
    $stmt->execute([$post_id]);

    // Eliminar el post
    $stmt = $pdo->prepare('DELETE FROM posts WHERE id_post = ?');
    $stmt->execute([$post_id]);

    // Eliminar imagen física si existe
    if (!empty($post['imagen_destacada_url']) &&
        !filter_var($post['imagen_destacada_url'], FILTER_VALIDATE_URL)) {
        $imagen_path = __DIR__ . '/' . $post['imagen_destacada_url'];
        if (file_exists($imagen_path) && is_file($imagen_path)) {
            @unlink($imagen_path);
        }
    }

    $pdo->commit();

    // Log del evento
    $security->logEvent('info', 'post_deleted', [
        'post_id' => $post_id,
        'titulo' => $post['titulo'],
        'paginas_afectadas' => count($paginasAfectadas)
    ]);

    // Redirección con mensaje
    $origen = $_POST['origen'] ?? 'gestionar_posts';
    if ($origen === 'dashboard') {
        header('Location: ' . url('dashboard.php') . '?msg=deleted');
    } else {
        header('Location: ' . url('gestionar_posts.php') . '?msg=deleted');
    }
    exit();

} catch (\PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $security->logEvent('error', 'post_delete_failed', [
        'post_id' => $post_id,
        'error' => $e->getMessage(),
    ]);

    $security->abort(500, 'Error al eliminar el post.');
}
