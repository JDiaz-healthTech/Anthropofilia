<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Anthropofilia Blog'; ?></title>
    <link rel="stylesheet" href="style.css">

    <?php

    // Obtenemos el nombre del script actual que se está ejecutando
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Si la página es crear_post.php o editar_post.php, incluyo el script de TinyMCE
    if (in_array($current_page, ['crear_post.php', 'editar_post.php'])):
    ?>
        <script src="https://cdn.tiny.cloud/1/55d1aaf8txhz0grsfs3s9dqm214nb4tk0p06h6ydeby0vta1/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?php endif; ?>
    
</head>

<body>
    <div class="container">
        <header class="main-header">
            <h1>ANTHROPOFILIA</h1>
            <p>El blog de Ana López Sampedro</p>
        </header>
        <nav class="main-nav">
            <a href="index.php">Inicio</a>
            <a href="pagina.php?slug=historia-da-filosofia">Historia da Filosofía</a>
            <a href="categoria.php?slug=lecturas-e-peliculas">Lecturas e Películas</a>
            <a href="pagina.php?slug=etica">Ética</a>
            <a href="acerca_de_mi.php">Acerca de mí</a>
            <a href="contacto.php">Contacto</a>
        </nav>
        <div class="main-content-area">