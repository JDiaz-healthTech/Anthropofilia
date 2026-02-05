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

<main class="container" style="max-width: 900px; margin: 2rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Gestionar Sitios de Interés</h1>
        <a href="<?= url('dashboard.php') ?>" class="btn">← Volver al Panel de Control</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>" style="padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--radius); background: <?= $messageType === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $messageType === 'success' ? '#155724' : '#721c24' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO CREAR NUEVO SITIO -->
    <section style="background: var(--card-bg); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; border: 1px solid var(--border);">
        <h2 style="margin-top: 0;">Nuevo Sitio</h2>
        <form method="POST" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
            <?= $security->csrfField() ?>
            <input type="hidden" name="action" value="create">
            <div style="flex: 2; min-width: 200px;">
                <label for="nombre" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre:</label>
                <input type="text"
                    id="nombre"
                    name="nombre"
                    required
                    placeholder="Ej: Real Academia Española"
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: var(--radius);">
            </div>
            <div style="flex: 3; min-width: 250px;">
                <label for="url" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">URL:</label>
                <input type="url"
                    id="url"
                    name="url"
                    required
                    placeholder="https://www.rae.es"
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: var(--radius);">
            </div>
            <div style="flex: 1; min-width: 80px;">
                <label for="orden" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Orden:</label>
                <input type="number"
                    id="orden"
                    name="orden"
                    value="0"
                    min="0"
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: var(--radius);">
            </div>
            <button type="submit"
                    class="btn"
                    style="padding: 0.6rem 1.5rem; background: var(--brand); color: white; border: none; border-radius: var(--radius); font-weight: 600; cursor: pointer;">
                Crear Sitio
            </button>
        </form>
    </section>

    <!-- LISTA DE SITIOS EXISTENTES -->
    <section>
        <h2>Sitios Existentes (<?= count($sitios) ?>)</h2>

        <?php if (!empty($sitios)): ?>
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: var(--radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <thead style="background: var(--card-bg);">
                        <tr>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border);">Icono</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border);">Nombre</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border);">URL</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border);">Orden</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border);">Activo</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border);">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sitios as $sitio): ?>
                            <tr style="border-bottom: 1px solid var(--border); <?= !$sitio['activo'] ? 'opacity: 0.5;' : '' ?>">
                                <td style="padding: 1rem; text-align: center;">
                                    <img src="<?= Sites::getFaviconUrl($sitio['url']) ?>"
                                        alt=""
                                        style="width: 24px; height: 24px; vertical-align: middle;">
                                </td>
                                <td style="padding: 1rem;">
                                    <strong><?= htmlspecialchars($sitio['nombre']) ?></strong>
                                </td>
                                <td style="padding: 1rem;">
                                    <a href="<?= htmlspecialchars($sitio['url']) ?>" target="_blank" rel="noopener">
                                        <?= htmlspecialchars($sitio['url']) ?>
                                    </a>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?= $sitio['orden'] ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <?= $sitio['activo'] ? '✅' : '❌' ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <button onclick="toggleEdit(<?= $sitio['id'] ?>)"
                                                class="btn btn-sm"
                                                style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #ffc107; color: #000; border: none; border-radius: var(--radius); cursor: pointer;">
                                            ✏️ Editar
                                        </button>

                                        <form method="POST"
                                            style="display: inline; margin: 0;"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este sitio?');">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $sitio['id'] ?>">
                                            <button type="submit"
                                                    class="btn btn-sm"
                                                    style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #dc3545; color: white; border: none; border-radius: var(--radius); cursor: pointer;">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Formulario de edición (oculto por defecto) -->
                                    <div id="edit-form-<?= $sitio['id'] ?>" style="display: none; margin-top: 1rem; padding: 1rem; background: var(--card-bg); border-radius: var(--radius);">
                                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: end; flex-wrap: wrap;">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?= $sitio['id'] ?>">
                                            <div style="flex: 2; min-width: 150px;">
                                                <label style="font-size: 0.8rem;">Nombre:</label>
                                                <input type="text"
                                                    name="nombre"
                                                    value="<?= htmlspecialchars($sitio['nombre']) ?>"
                                                    required
                                                    style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius);">
                                            </div>
                                            <div style="flex: 3; min-width: 200px;">
                                                <label style="font-size: 0.8rem;">URL:</label>
                                                <input type="url"
                                                    name="url"
                                                    value="<?= htmlspecialchars($sitio['url']) ?>"
                                                    required
                                                    style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius);">
                                            </div>
                                            <div style="flex: 1; min-width: 60px;">
                                                <label style="font-size: 0.8rem;">Orden:</label>
                                                <input type="number"
                                                    name="orden"
                                                    value="<?= $sitio['orden'] ?>"
                                                    min="0"
                                                    style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius);">
                                            </div>
                                            <div style="flex: 1; min-width: 60px;">
                                                <label style="font-size: 0.8rem;">Activo:</label>
                                                <select name="activo" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius);">
                                                    <option value="1" <?= $sitio['activo'] ? 'selected' : '' ?>>Sí</option>
                                                    <option value="0" <?= !$sitio['activo'] ? 'selected' : '' ?>>No</option>
                                                </select>
                                            </div>
                                            <button type="submit"
                                                    class="btn btn-sm"
                                                    style="padding: 0.5rem 1rem; background: var(--brand); color: white; border: none; border-radius: var(--radius); cursor: pointer;">
                                                Guardar
                                            </button>
                                            <button type="button"
                                                    onclick="toggleEdit(<?= $sitio['id'] ?>)"
                                                    class="btn btn-sm"
                                                    style="padding: 0.5rem 1rem; background: #6c757d; color: white; border: none; border-radius: var(--radius); cursor: pointer;">
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
            <p style="padding: 2rem; text-align: center; color: #666; background: var(--card-bg); border-radius: var(--radius);">
                No hay sitios creados todavía. ¡Crea tu primer sitio de interés!
            </p>
        <?php endif; ?>
    </section>
</main>

<script>
function toggleEdit(id) {
    const form = document.getElementById('edit-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
