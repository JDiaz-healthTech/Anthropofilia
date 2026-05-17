<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';

use App\Models\Category;

// Verificar login
$security->requireLogin();
$security->requireRole(['administrador', 'autor']);

// Procesar acciones (crear, editar, eliminar)
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security->csrfValidate($_POST['csrf_token'] ?? '');

    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'create':
                $nombre = trim($_POST['nombre_categoria'] ?? '');
                if (!empty($nombre)) {
                    Category::create($nombre);
                    $message = 'Categoría creada correctamente.';
                    $messageType = 'success';
                }
                break;

            case 'update':
                $id = (int)($_POST['id_categoria'] ?? 0);
                $nombre = trim($_POST['nombre_categoria'] ?? '');
                if ($id > 0 && !empty($nombre)) {
                    Category::update($id, $nombre);
                    $message = 'Categoría actualizada correctamente.';
                    $messageType = 'success';
                }
                break;

            case 'delete':
                $id = (int)($_POST['id_categoria'] ?? 0);
                if ($id > 0) {
                    Category::delete($id);
                    $message = 'Categoría eliminada correctamente.';
                    $messageType = 'success';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Cargar todas las categorías
try {
    $categorias = Category::getAll();
} catch (Exception $e) {
    error_log("Error cargando categorías: " . $e->getMessage());
    $categorias = [];
}

$page_title = 'Gestionar Categorías';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <div class="section-header">
        <h1>Gestionar Categorías</h1>
        <a href="<?= url('dashboard.php') ?>" class="btn">← Volver al Panel de Control</a>
    </div>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO CREAR NUEVA CATEGORÍA -->
    <section class="admin-card">
        <h2>Nueva Categoría</h2>
        <form method="POST" class="admin-form-inline">
            <?= $security->csrfField() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form-inline__field">
                <label for="nombre_categoria">Nombre de la categoría:</label>
                <input type="text"
                       id="nombre_categoria"
                       name="nombre_categoria"
                       required
                       placeholder="Ej: Antropología Biológica">
            </div>
            <button type="submit" class="btn btn-primary">
                Crear Categoría
            </button>
        </form>
    </section>

    <!-- LISTA DE CATEGORÍAS EXISTENTES -->
    <section>
        <h2>Categorías Existentes (<?= count($categorias) ?>)</h2>

        <?php if (!empty($categorias)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Slug (URL)</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($cat['nombre_categoria']) ?></strong>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($cat['slug']) ?></code>
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <button onclick="toggleEdit(<?= $cat['id_categoria'] ?>)"
                                                class="btn-sm btn-sm--edit">
                                            ✏️ Editar
                                        </button>

                                        <form method="POST"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría? Los posts asociados quedarán sin categoría.');">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                            <button type="submit" class="btn-sm btn-sm--delete">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Formulario de edición (oculto por defecto) -->
                                    <div id="edit-form-<?= $cat['id_categoria'] ?>" class="admin-table__edit-panel">
                                        <form method="POST" class="admin-form-inline">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                            <div class="admin-form-inline__field">
                                                <input type="text"
                                                       name="nombre_categoria"
                                                       value="<?= htmlspecialchars($cat['nombre_categoria']) ?>"
                                                       required>
                                            </div>
                                            <button type="submit" class="btn-sm btn-sm--save">
                                                Guardar
                                            </button>
                                            <button type="button"
                                                    onclick="toggleEdit(<?= $cat['id_categoria'] ?>)"
                                                    class="btn-sm btn-sm--cancel">
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty-state">
                No hay categorías creadas todavía. ¡Crea tu primera categoría!
            </p>
        <?php endif; ?>
    </section>
</main>

<script>
function toggleEdit(id) {
    document.getElementById('edit-form-' + id).classList.toggle('is-visible');
}
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
