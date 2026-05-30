<?php
// login.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

$page_title = 'Login de Administrador';
$meta_description = 'Acceso al panel de administración.';

// Recuperar datos del intento anterior (patrón PRG)
$form = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Mensajes por estado
$status = $_GET['status'] ?? '';
$messages = [
    'invalid'         => 'Por favor, completa usuario y contraseña.',
    'bad_credentials' => 'Usuario o contraseña incorrectos.',
    'logged_out'      => 'Has cerrado sesión correctamente.',
    'blocked'         => 'Demasiados intentos. Inténtalo más tarde.',
    'registered'      => '¡Cuenta creada correctamente! Ya puedes iniciar sesión.',
    'error'           => 'Ha ocurrido un error inesperado. Inténtalo de nuevo más tarde.',
];
  $categoria = null; // para migas condicionales
require_once BASE_PATH . '/resources/views/partials/header.php';
?>
<main class="container" id="content">
  <h2>Iniciar sesión</h2>

  <?php if (isset($messages[$status])): ?>
    <p class="status-<?php echo in_array($status, ['logged_out', 'registered']) ? 'success' : 'error'; ?>">
      <?php echo htmlspecialchars($messages[$status], ENT_QUOTES, 'UTF-8'); ?>
    </p>
  <?php endif; ?>

  <form action="<?= url('procesar_login.php') ?>" method="POST" class="form-container" novalidate autocomplete="on">
    <?php echo $security->csrfField(); ?>

    <div>
      <label for="email">Email</label>
      <input
        type="email"
        id="email"
        name="email"
        required
        autocomplete="email"
        autofocus
        value="<?php echo htmlspecialchars($form['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div>
      <label for="contrasena">Contraseña</label>
      <input
        type="password"
        id="contrasena"
        name="contrasena"
        required
        autocomplete="current-password">
    </div>
    <!--
    <div class="form-row">
      <label>
        <input type="checkbox" name="remember" value="1">
        Mantener sesión iniciada
      </label>
    </div> -->

    <button type="submit">Entrar</button>
  </form>

  <p class="help-text">¿No tienes cuenta? <a href="<?= url('registro.php') ?>">Regístrate aquí</a></p>
</main>

<?php require_once BASE_PATH . '/resources/views/partials/footer.php'; ?>
