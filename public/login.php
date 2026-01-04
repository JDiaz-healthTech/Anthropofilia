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
];
  $categoria = null; // para migas condicionales
  require_once __DIR__.'/header.php';
?>
<main class="container" id="content">
  <h2>Acceso al Panel</h2>
  <!-- Mensaje adicional mientras el registro no es global -->
    <p class="help-text">Acceso restringido. Si necesitas una cuenta, contacta con la editora.</p>

  <?php if (isset($messages[$status])): ?>
    <p class="status-<?php echo $status === 'logged_out' ? 'success' : 'error'; ?>">
      <?php echo htmlspecialchars($messages[$status], ENT_QUOTES, 'UTF-8'); ?>
    </p>
  <?php endif; ?>

  <form action="<?= url('procesar_login.php') ?>" method="POST" class="form-container" novalidate autocomplete="on">
    <?php echo $security->csrfField(); ?>

    <div>
      <label for="nombre_usuario">Usuario</label>
      <input
        type="text"
        id="nombre_usuario"
        name="nombre_usuario"
        required
        autocomplete="username"
        autofocus
        value="<?php echo htmlspecialchars($form['nombre_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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

    <div class="form-row">
      <label>
        <input type="checkbox" name="remember" value="1">
        Mantener sesión iniciada
      </label>
    </div>

    <button type="submit">Entrar</button>
  </form>

  <p class="help-text">¿Problemas para acceder? Contacta con el administrador.</p>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
