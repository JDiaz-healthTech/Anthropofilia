<?php
// editar_pagina.php

// 1. INICIALIZACIÓN: Requerimos el archivo central que maneja todo.
// Esto nos da acceso a $pdo y $security.
require_once 'init.php';

// 2. BARRERA DE SEGURIDAD: Validamos si el usuario está logueado.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// 3. OBTENER DATOS DE LA PÁGINA (usando PDO)
// Obtenemos y validamos el ID de la página desde la URL.
$pagina_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($pagina_id <= 0) {
    // Es una buena práctica redirigir en lugar de usar die()
    header("Location: gestionar_paginas.php?error=id_invalido");
    exit();
}

// Preparamos y ejecutamos la consulta de forma segura con PDO.
$sql_pagina = "SELECT * FROM paginas WHERE id_pagina = ?";
$stmt = $pdo->prepare($sql_pagina);
$stmt->execute([$pagina_id]);
$pagina = $stmt->fetch();

if (!$pagina) {
    header("Location: gestionar_paginas.php?error=not_found");
    exit();
}

$page_title = 'Editar Página';
require_once 'header.php';
?>

<main>
    <h2>Editar Página Estática</h2>
    <form action="actualizar_pagina.php" method="POST" class="form-container">
        
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($security->csrfToken()); ?>">
        
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
// Ya no es necesario cerrar la conexión de PDO.
require_once 'footer.php';
?>