<?php
declare(strict_types=1);
/* Error 403 — Acceso denegado. El usuario no tiene permisos o la verificación CSRF falló. */
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso denegado - 403 | Anthropofilia</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <main class="container error-page">
        <div class="error-content">
            <div class="error-code">403</div>
            <h1>Acceso denegado</h1>
            <p class="error-message">
                No tienes permiso para acceder a este recurso, o la solicitud no pudo ser verificada.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn--primary">← Volver al inicio</a>
            </div>
        </div>
    </main>
</body>
</html>
