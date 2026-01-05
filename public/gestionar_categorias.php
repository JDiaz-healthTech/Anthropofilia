<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';

use App\Models\Category;

// Verificar login
if (empty($_SESSION['id_usuario'])) {
    header('Location: ' . url('login.php'));
    exit();
}

// Procesar acciones (crear, editar, eliminar)
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $security->validateCSRF($_POST['csrf_token'] ?? '');
    
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

<main class="container" style="max-width: 900px; margin: 2rem auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Gestionar Categorías</h1>
        <a href="<?= url('dashboard.php') ?>" class="btn">← Volver al Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?>" style="padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--radius); background: <?= $messageType === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $messageType === 'success' ? '#155724' : '#721c24' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO CREAR NUEVA CATEGORÍA -->
    <section style="background: var(--card-bg); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; border: 1px solid var(--border);">
        <h2 style="margin-top: 0;">Nueva Categoría</h2>
        <form method="POST" style="display: flex; gap: 1rem; align-items: end;">
            <?= $security->csrfField() ?>
            <input type="hidden" name="action" value="create">
            <div style="flex: 1;">
                <label for="nombre_categoria" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre de la categoría:</label>
                <input type="text" 
                       id="nombre_categoria" 
                       name="nombre_categoria" 
                       required 
                       placeholder="Ej: Antropología Biológica"
                       style="width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: var(--radius);">
            </div>
            <button type="submit" 
                    class="btn" 
                    style="padding: 0.6rem 1.5rem; background: var(--brand); color: white; border: none; border-radius: var(--radius); font-weight: 600; cursor: pointer;">
                Crear Categoría
            </button>
        </form>
    </section>

    <!-- LISTA DE CATEGORÍAS EXISTENTES -->
    <section>
        <h2>Categorías Existentes (<?= count($categorias) ?>)</h2>
        
        <?php if (!empty($categorias)): ?>
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: var(--radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <thead style="background: var(--card-bg);">
                        <tr>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border);">Nombre</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border);">Slug (URL)</th>
                            <th style="padding: 1rem; text-align: center; border-bottom: 2px solid var(--border);">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 1rem;">
                                    <strong><?= htmlspecialchars($cat['nombre_categoria']) ?></strong>
                                </td>
                                <td style="padding: 1rem;">
                                    <code style="background: var(--card-bg); padding: 0.2rem 0.5rem; border-radius: 3px;">
                                        <?= htmlspecialchars($cat['slug']) ?>
                                    </code>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <!-- Botón Editar (toggle form inline) -->
                                        <button onclick="toggleEdit(<?= $cat['id_categoria'] ?>)" 
                                                class="btn btn-sm" 
                                                style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #ffc107; color: #000; border: none; border-radius: var(--radius); cursor: pointer;">
                                            ✏️ Editar
                                        </button>
                                        
                                        <!-- Botón Eliminar -->
                                        <form method="POST" 
                                              style="display: inline; margin: 0;"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría? Los posts asociados quedarán sin categoría.');">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    style="padding: 0.4rem 0.8rem; font-size: 0.9rem; background: #dc3545; color: white; border: none; border-radius: var(--radius); cursor: pointer;">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Formulario de edición (oculto por defecto) -->
                                    <div id="edit-form-<?= $cat['id_categoria'] ?>" style="display: none; margin-top: 1rem; padding: 1rem; background: var(--card-bg); border-radius: var(--radius);">
                                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: end;">
                                            <?= $security->csrfField() ?>
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id_categoria" value="<?= $cat['id_categoria'] ?>">
                                            <div style="flex: 1;">
                                                <input type="text" 
                                                       name="nombre_categoria" 
                                                       value="<?= htmlspecialchars($cat['nombre_categoria']) ?>" 
                                                       required
                                                       style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius);">
                                            </div>
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    style="padding: 0.5rem 1rem; background: var(--brand); color: white; border: none; border-radius: var(--radius); cursor: pointer;">
                                                Guardar
                                            </button>
                                            <button type="button" 
                                                    onclick="toggleEdit(<?= $cat['id_categoria'] ?>)" 
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
                No hay categorías creadas todavía. ¡Crea tu primera categoría!
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