<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// Si ya está logueado, redirigir
if ($security->userId()) {
    header('Location: index.php');
    exit();
}

// PRG: datos previos si hubo error
$old = $_SESSION['form_registro'] ?? [];
$registroError = $_SESSION['registro_error'] ?? null;
unset($_SESSION['form_registro'], $_SESSION['registro_error']);

// Mensajes por estado
$status = $_GET['status'] ?? '';
$messages = [
    'invalid'        => 'Por favor, completa todos los campos obligatorios correctamente.',
    'invalid_email'  => 'El formato del email no es válido.',
    'invalid_name'   => 'Nombre y apellidos deben tener entre 1 y 3 palabras, solo letras.',
    'password_short' => 'La contraseña debe tener al menos 8 caracteres.',
    'password_match' => 'Las contraseñas no coinciden.',
    'email_exists'   => 'Ya existe una cuenta con ese email.',
    'rgpd'           => 'Debes aceptar la política de privacidad para registrarte.',
    'db_error'       => $registroError ?? 'Error al crear la cuenta. Inténtalo de nuevo.',
    'blocked'        => 'Demasiados intentos. Inténtalo más tarde.',
];

$page_title = 'Crear cuenta';
$categoria = null;
require_once BASE_PATH . '/resources/views/partials/header.php';
?>

<main class="container" id="content">
    <nav class="breadcrumbs" aria-label="Breadcrumbs">
        <a href="<?= url('index.php') ?>">Inicio</a>
        <span aria-hidden="true">›</span>
        <span aria-current="page">Crear cuenta</span>
    </nav>

    <h2>Crear cuenta</h2>

    <?php if (isset($messages[$status])): ?>
        <p class="status-error">
            <?= e($messages[$status]) ?>
        </p>
    <?php endif; ?>

    <form action="<?= url('procesar_registro.php') ?>" method="POST" class="form-container" novalidate autocomplete="on">
        <?= $security->csrfField() ?>

        <div>
            <label for="nombre">Nombre <span style="color: red;">*</span></label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                required
                maxlength="60"
                autocomplete="given-name"
                autofocus
                placeholder="Ej: María del Carmen"
                value="<?= e($old['nombre'] ?? '') ?>">
            <small>Máximo 3 palabras</small>
        </div>

        <div>
            <label for="apellido1">Primer apellido <span style="color: red;">*</span></label>
            <input
                type="text"
                id="apellido1"
                name="apellido1"
                required
                maxlength="60"
                autocomplete="additional-name"
                placeholder="Ej: García"
                value="<?= e($old['apellido1'] ?? '') ?>">
            <small>Máximo 3 palabras</small>
        </div>

        <div>
            <label for="apellido2">Segundo apellido</label>
            <input
                type="text"
                id="apellido2"
                name="apellido2"
                maxlength="60"
                autocomplete="family-name"
                placeholder="Ej: López"
                value="<?= e($old['apellido2'] ?? '') ?>">
            <small>Opcional. Máximo 3 palabras</small>
        </div>

        <div>
            <label for="email">Email <span style="color: red;">*</span></label>
            <input
                type="email"
                id="email"
                name="email"
                required
                maxlength="100"
                autocomplete="email"
                placeholder="tu@email.com"
                value="<?= e($old['email'] ?? '') ?>">
        </div>

        <div>
            <label for="contrasena">Contraseña <span style="color: red;">*</span></label>
            <input
                type="password"
                id="contrasena"
                name="contrasena"
                required
                minlength="8"
                autocomplete="new-password">
            <small>Mínimo 8 caracteres</small>
        </div>

        <div>
            <label for="contrasena_confirm">Repetir contraseña <span style="color: red;">*</span></label>
            <input
                type="password"
                id="contrasena_confirm"
                name="contrasena_confirm"
                required
                minlength="8"
                autocomplete="new-password">
        </div>

        <div>
            <label>
                <input type="checkbox" name="rgpd" value="1" required>
                He leído y acepto la <a href="<?= url('privacidad.php') ?>" target="_blank">política de privacidad</a>.
            </label>
        </div>

        <button type="submit">Crear cuenta</button>
    </form>

    <p class="help-text">¿Ya tienes cuenta? <a href="<?= url('login.php') ?>">Inicia sesión</a></p>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
