<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';

use App\Models\Sites;

// Verificar login
if (empty($_SESSION['id_usuario'])) {
    header('Location: ' . url('login.php'));
    exit();
}

// Procesar acciones (crear, editar, eliminar)
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security->csrfValidate($_POST['csrf_token'] ?? '');

    $action = $_POST['action'] ?? '';

try {
        switch ($action) {
            case 'create':
                $nombre = trim($_POST['nombre'] ?? '');
                $url = trim($_POST['url'] ?? '');
                $orden = (int)($_POST['orden'] ?? 0);
                if (!empty($nombre) && !empty($url)) {
                    Sites::create($nombre, $url, $orden);
                    $message = 'Sitio creado correctamente.';
                    $messageType = 'success';
                }
                break;

            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $nombre = trim($_POST['nombre'] ?? '');
                $url = trim($_POST['url'] ?? '');
                $orden = (int)($_POST['orden'] ?? 0);
                $activo = (int)($_POST['activo'] ?? 1);
                if ($id > 0 && !empty($nombre) && !empty($url)) {
                    Sites::update($id, $nombre, $url, $orden, $activo);
                    $message = 'Sitio actualizado correctamente.';
                    $messageType = 'success';
                }
                break;

            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    Sites::delete($id);
                    $message = 'Sitio eliminado correctamente.';
                    $messageType = 'success';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Cargar todos los sitios
try {
    $sitios = Sites::getAll();
} catch (Exception $e) {
    error_log("Error cargando sitios: " . $e->getMessage());
    $sitios = [];
}

$page_title = 'Gestionar Sitios de Interés';
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container">
    <div class="section-header">
        <h1>Gestionar Sitios de Interés</h1>
        <a href="<?= url('dashboard.php') ?>" class="btn">← Volver al Panel de Control</a>
    </div>

    <?php if ($message): ?>
        <div class="alert <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO CREAR NUEVO SITIO -->
    <section class="admin-card">
        <h2>Nuevo Sitio</h2>
        <form method="POST" class="admin-form-inline">
            <?= $security->csrfField() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form-inline__field admin-form-inline__field--2x">
                <label for="nombre">Nombre:</label>
                <input type="text"
                    id="nombre"
                    name="nombre"
                    required
                    placeholder="Ej: Real Academia Española">
            </div>
            <div class="admin-form-inline__field admin-form-inline__field--3x">
                <label for="url">URL:</label>
                <input type="url"
                    id="url"
                    name="url"
                    required
                    placeholder="https://www.rae.es">
            </div>
            <div class="admin-form-inline__field admin-form-inline__field--narrow">
                <label for="orden">Orden:</label>
                <input type="number"
                    id="orden"
                    name="orden"
                    value="0"
                    min="0">
            </div>
            <button type="submit" class="btn btn-primary">
                Crear Sitio
            </button>
        </form>
    </section>

    <!-- LISTA DE SITIOS EXISTENTES -->
    <section>
        <h2>Sitios Existentes (<?= count($sitios) ?>)</h2>

        <?php if (!empty($sitios)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-center">Icono</th>
                            <th>Nombre</th>
                            <th>URL</th>
                            <th class="text-center">Orden</th>
                            <th class="text-center">Activo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sitios as $sitio): ?>
                            <tr<?= !$sitio['activo'] ? ' class="is-inactive"' : '' ?>>
                                <td class="text-center">
                                    <img src="<?= Sites::getFaviconUrl($sitio['url']) ?>"
                                        alt=""
                                        class="admin-table__favicon">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($sitio['nombre']) ?></strong>
                                </td>
                                <td>
                                    <a href="<?= htmlspecialchars($sitio['url']) ?>" target="_blank" rel="noopener">
                                        <?= htmlspecialchars($sitio['url']) ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?= $sitio['orden'] ?>
                                </td>
                                <td class="text-center">
                                    <?= $sitio['activo'] ? '✅' : '❌' ?>
                                </td>
                                <td>
                                    <div class="admin-table__actions">
                                        <button onclick="toggleEdit(<?= $sitio['id'] ?>)"
                                                class="btn-sm btn-sm--edit">
                                            ✏️ Editar
                                        </button>

                                        <form method="POST"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este sitio?');">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $sitio['id'] ?>">
                                            <button type="submit" class="btn-sm btn-sm--delete">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Formulario de edición (oculto por defecto) -->
                                    <div id="edit-form-<?= $sitio['id'] ?>" class="admin-table__edit-panel">
                                        <form method="POST" class="admin-form-inline">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= $sitio['id'] ?>">
                                            <div class="admin-form-inline__field admin-form-inline__field--2x">
                                                <label>Nombre:</label>
                                                <input type="text"
                                                    name="nombre"
                                                    value="<?= htmlspecialchars($sitio['nombre']) ?>"
                                                    required>
                                            </div>
                                            <div class="admin-form-inline__field admin-form-inline__field--3x">
                                                <label>URL:</label>
                                                <input type="url"
                                                    name="url"
                                                    value="<?= htmlspecialchars($sitio['url']) ?>"
                                                    required>
                                            </div>
                                            <div class="admin-form-inline__field admin-form-inline__field--narrow">
                                                <label>Orden:</label>
                                                <input type="number"
                                                    name="orden"
                                                    value="<?= $sitio['orden'] ?>"
                                                    min="0">
                                            </div>
                                            <div class="admin-form-inline__field admin-form-inline__field--narrow">
                                                <label>Activo:</label>
                                                <select name="activo">
                                                    <option value="1" <?= $sitio['activo'] ? 'selected' : '' ?>>Sí</option>
                                                    <option value="0" <?= !$sitio['activo'] ? 'selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn-sm btn-sm--save">
                                                Guardar
                                            </button>
                                            <button type="button"
                                                    onclick="toggleEdit(<?= $sitio['id'] ?>)"
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
                No hay sitios creados todavía. ¡Crea tu primer sitio de interés!
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
