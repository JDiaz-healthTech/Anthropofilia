<?php
// Barrera de seguridad e inicialización
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Obtener el ID de la página de la URL
$pagina_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pagina_id <= 0) {
    die('ID de página no válido.');
}

// Obtener los datos actuales de la página
$sql_pagina = "SELECT * FROM paginas WHERE id_pagina = ?";
$stmt = $conn->prepare($sql_pagina);
$stmt->bind_param("i", $pagina_id);
$stmt->execute();
$resultado = $stmt->get_result();
$pagina = $resultado->fetch_assoc();
$stmt->close();

if (!$pagina) {
    die('Página no encontrada.');
}

$page_title = 'Editar Página';
require_once 'header.php';
?>

<main>
    <h2>Editar Página Estática</h2>
    <form action="actualizar_pagina.php" method="POST" class="form-container">
        <input type="hidden" name="id_pagina" value="<?php echo $pagina['id_pagina']; ?>">
        
        <div>
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($pagina['titulo']); ?>" required>
        </div>
        <div>
            <label for="slug">Slug (URL amigable):</label>
            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($pagina['slug']); ?>" required>
        </div>
        <div>
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="20" required><?php echo htmlspecialchars($pagina['contenido']); ?></textarea>
        </div>
        <button type="submit">Actualizar Página</button>
    </form>
</main>

<?php
$conn->close();
require_once 'footer.php';
?>