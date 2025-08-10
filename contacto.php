<?php
session_start(); // Iniciar sesión para poder leer de $_SESSION

// Recuperar datos del formulario si existen en la sesión
$form_data = $_SESSION['form_data'] ?? [];
// Limpiar la sesión para que los datos no persistan en la siguiente visita
unset($_SESSION['form_data']);

$page_title = 'Contacto';
require_once 'header.php';
?>

<main>
    <h2>Contacto</h2>
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo '<p class="status-success">¡Gracias! Tu mensaje ha sido enviado.</p>';
        } else {
            // Un mensaje de error más genérico funciona para ambos tipos de error
            echo '<p class="status-error">Hubo un error. Por favor, revisa los datos e inténtalo de nuevo.</p>';
        }
    }
    ?>
    <p>Si tienes alguna pregunta o sugerencia, no dudes en escribirme.</p>
    
    <form action="enviar_contacto.php" method="POST" class="form-container">
        <div>
            <label for="nombre">Tu Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($form_data['nombre'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="email">Tu Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
        </div>
        <div>
            <label for="mensaje">Mensaje:</label>
            <textarea id="mensaje" name="mensaje" rows="8" required><?php echo htmlspecialchars($form_data['mensaje'] ?? ''); ?></textarea>
        </div>
        <button type="submit">Enviar Mensaje</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>