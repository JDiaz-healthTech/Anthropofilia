<?php
require_once __DIR__ . '/init.php';

// Recuperar datos del formulario si existen en la sesión (PRG)
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

// Whitelist del status
$status = $_GET['status'] ?? '';
$ok  = ($status === 'success');
$err = ($status === 'error' || $status === 'invalid');

// CSRF para el form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$page_title = 'Contacto';
$meta_description = 'Formulario de contacto para consultas y sugerencias.';
require_once __DIR__ . '/header.php';
?>
<main class="container">
  <h1>Contacto</h1>

  <div aria-live="polite">
    <?php if ($ok): ?>
      <p class="status-success">¡Gracias! Tu mensaje ha sido enviado.</p>
    <?php elseif ($err): ?>
      <p class="status-error">Hubo un error. Revisa los datos e inténtalo de nuevo.</p>
    <?php endif; ?>
  </div>

  <p>Si tienes alguna pregunta o sugerencia, no dudes en escribirme.</p>

  <form action="/enviar_contacto.php" method="post" class="form-container" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Honeypot anti-spam (debe quedar vacío) -->
    <div class="hp" aria-hidden="true">
      <label for="website">Deja este campo vacío</label>
      <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div>
      <label for="nombre">Tu nombre</label>
      <input
        type="text" id="nombre" name="nombre"
        autocomplete="name"
        maxlength="100" required
        value="<?= htmlspecialchars($form_data['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="email">Tu correo electrónico</label>
      <input
        type="email" id="email" name="email"
        autocomplete="email" inputmode="email" spellcheck="false"
        maxlength="190" required
        value="<?= htmlspecialchars($form_data['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label for="mensaje">Mensaje</label>
      <textarea
        id="mensaje" name="mensaje" rows="8"
        maxlength="5000" required><?= htmlspecialchars($form_data['mensaje'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <button type="submit">Enviar mensaje</button>
  </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>
<?php
// Limpiar datos del formulario después de mostrar  
unset($_SESSION['form_data']);