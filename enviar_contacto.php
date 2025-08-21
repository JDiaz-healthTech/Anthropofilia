<?php
// procesar_contacto.php (o como lo llames)
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: contacto.php');
    exit();
}

// 2) CSRF (token desde el formulario)
$security->csrfValidate($_POST['csrf_token'] ?? null);

// 3) Rate limit específico (además del general en boot)
$security->checkRateLimit('contact_form', 5, 3600); // 5 envíos/hora por IP

// 4) Honeypot anti-bots (campo oculto "website" debe venir vacío)
if (!empty($_POST['website'] ?? '')) {
    // Opcional: loguear y simular éxito para no dar pista a bots
    $security->logEvent('security', 'contact_honeypot_triggered', ['hp'=>$_POST['website']]);
    header('Location: contacto.php?status=success');
    exit();
}

// 5) Normalizar y validar inputs
$nombre  = trim((string)($_POST['nombre']  ?? ''));
$email   = trim((string)($_POST['email']   ?? ''));
$mensaje = trim((string)($_POST['mensaje'] ?? ''));

// Longitudes razonables
if (mb_strlen($nombre) > 120)  $nombre = mb_substr($nombre, 0, 120);
if (mb_strlen($email)  > 200)  $email  = mb_substr($email, 0, 200);
if (mb_strlen($mensaje) > 5000) {
    $_SESSION['form_data'] = $_POST;
    header('Location: contacto.php?status=too_long');
    exit();
}

// Validaciones
$nombre_ok  = $nombre !== '';
$email_ok   = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
$mensaje_ok = $mensaje !== '';

if (!$nombre_ok || !$email_ok || !$mensaje_ok) {
    $_SESSION['form_data'] = $_POST; // para “sticky form”
    header('Location: contacto.php?status=invalid');
    exit();
}

// 6) Construcción de correo (sin romper SPF/DKIM)
// Usa variables de entorno si las tienes (.env), con fallback a las tuyas
$to      = $_ENV['MAIL_TO']   ?? 'analosampedro@gmail.com';
$from    = $_ENV['MAIL_FROM'] ?? ('no-reply@' . (parse_url($_ENV['APP_URL'] ?? ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), PHP_URL_HOST) ?? 'localhost'));

// Mitigar header injection en cabeceras
$safeName  = preg_replace('/[\r\n]+/', ' ', $nombre);
$safeEmail = preg_replace('/[\r\n]+/', ' ', $email);

// Asunto en UTF-8
$subjectPlain = "Nuevo mensaje de contacto de: {$safeName}";
$subject = '=?UTF-8?B?' . base64_encode($subjectPlain) . '?=';

// Cuerpo en texto plano
$body = "Nombre: {$nombre}\nEmail: {$email}\n\nMensaje:\n{$mensaje}\n";

// Cabeceras (From dominio propio, Reply-To al usuario)
$headers  = "From: {$from}\r\n";
$headers .= "Reply-To: {$safeName} <{$safeEmail}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: Anthropofilia\r\n";

$ok = @mail($to, $subject, $body, $headers);

if ($ok) {
    $security->logEvent('info', 'contact_sent', ['from'=>$safeEmail]);
    header('Location: contacto.php?status=success');
    exit();
} else {
    $security->logEvent('error', 'contact_send_failed', ['from'=>$safeEmail]);
    $_SESSION['form_data'] = $_POST;
    header('Location: contacto.php?status=error_send');
    exit();
}
