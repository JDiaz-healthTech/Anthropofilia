<?php
require_once 'config.php';

// 1. OBTENER Y VALIDAR EL SLUG
$slug = isset($_GET['slug']) ? htmlspecialchars($_GET['slug']) : '';
if (empty($slug)) {
    header("Location: index.php");
    exit();
}

// 2. CONSULTAR LA TABLA 'paginas'
$sql = "SELECT titulo, contenido FROM paginas WHERE slug = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$resultado = $stmt->get_result();
$pagina = $resultado->fetch_assoc();
$stmt->close();

if (!$pagina) {
    die('Página no encontrada.');
}

$page_title = $pagina['titulo'];
require_once 'header.php';
?>

<main>
    <article>
        <h1><?php echo htmlspecialchars($pagina['titulo']); ?></h1>
        <hr>
        <div>
            <?php echo nl2br($pagina['contenido']); // Usamos nl2br para respetar los saltos de línea ?>
        </div>
    </article>
</main>

<?php require_once 'sidebar.php'; ?>

<?php
$conn->close();
require_once 'footer.php';
?>