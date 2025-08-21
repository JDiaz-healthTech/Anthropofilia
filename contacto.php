<?php
require_once __DIR__ . '/init.php';

// Recuperar datos del formulario si existen (patrón PRG)
$form_data = $_SESSION['form_data'] ?? [];
// No lo borres aún; lo eliminamos tras pintar el form

// Whitelist del status
$status = $_GET['status'] ?? '';
$messages = [
    'success'     => '¡Gracias! Tu mensaje ha sido enviado.',
    'invalid'     => 'Datos inválidos. Revisa los campos e inténtalo de nuevo.',
    'error'       => 'Hubo un error inesperado. Inténtalo de nuevo.',
    'error_send'  => 'No se pudo enviar el mensaje. Inténtalo más tarde.',
    'too_long'    => 'El mensaje es demasiado largo.',
];
$page_title = 'Contacto';
$meta_description = 'Formulario de contacto para consultas y sugerencias.';
require_once __DIR__ . '/header.php';
?>
<main class="container">
  <h1>Contacto</h1>

  <div aria-live="polite">
    <?php if (isset($messages[$status])): ?>
      <p class="status-<?php echo $status === 'success' ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($messages[$status], ENT_QUOTES, 'UTF-8'); ?>
      </p>
    <?php endif; ?>
  </div>

  <p>Si tienes alguna pregunta o sugerencia, no dudes en escribirme.</p>

  <form action="procesar_contacto.php" method="POST" class="form-container" novalidate>
    <?php
      // CSRF (usa el helper del SecurityManager)
      echo $security->csrfField();
    ?>

    <!-- Honeypot anti-spam (debe quedar vacío) -->
    <div class="hp" aria-hidden="true" style="position:absolute; left:-9999px;">
      <label for="website">Deja este campo vacío</label>
      <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div>
      <label for="nombre">Tu nombre</label>
      <input
        type="text" id="nombre" name="nombre"
        autocomplete="name"
        maxlength="100" required
        value="<?php echo htmlspecialchars($form_data['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div>
      <label for="email">Tu correo electrónico</label>
      <input
        type="email" id="email" name="email"
        autocomplete="email" inputmode="email" spellcheck="false"
        maxlength="190" required
        value="<?php echo htmlspecialchars($form_data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>

    <div>
      <label for="mensaje">Mensaje</label>
      <textarea
        id="mensaje" name="mensaje" rows="8"
        maxlength="5000" required><?php
          echo htmlspecialchars($form_data['mensaje'] ?? '', ENT_QUOTES, 'UTF-8');
        ?></textarea>
    </div>

    <button type="submit">Enviar mensaje</button>
  </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>

<?php
// Limpiar datos del formulario después de renderizar
unset($_SESSION['form_data']);
