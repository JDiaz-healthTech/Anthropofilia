<?php
// ¡Siempre al principio!
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strip_tags(trim($_POST["nombre"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $mensaje = trim($_POST["mensaje"]);

    if (empty($nombre) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($mensaje)) {
        // Guardamos los datos del POST en la sesión
        $_SESSION['form_data'] = $_POST;
        
        header("Location: contacto.php?status=error");
        exit();
    }

    $destinatario = "analosampedro@gmail.com";
    $asunto = "Nuevo mensaje de contacto de: $nombre";
    $contenido_email = "Nombre: $nombre\nEmail: $email\n\nMensaje:\n$mensaje\n";
    $cabeceras = "From: $nombre <$email>";

    if (mail($destinatario, $asunto, $contenido_email, $cabeceras)) {
        header("Location: contacto.php?status=success");
        exit();
    } else {
        $_SESSION['form_data'] = $_POST; // También guardamos si falla el envío
        header("Location: contacto.php?status=error_send");
        exit();
    }
} else {
    header("Location: contacto.php");
    exit();
}
?>