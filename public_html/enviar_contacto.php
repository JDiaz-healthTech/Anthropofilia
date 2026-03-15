<?php
// enviar_contacto.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . url('contacto.php'), true, 302);
    exit();
}

try {
    // CSRF
    $security->requireValidCsrf();

    // Rate limit: 5 envíos/h por IP
    $security->checkRateLimit('contact_form', 3, 3600);

    // Honeypot
    if (!empty($_POST['website'] ?? '')) {
        $security->logEvent('security', 'contact_honeypot_triggered', ['hp'=>$_POST['website']]);
        header('Location: ' . url('contacto.php?status=success'), true, 303);
        exit();
    }

    // Normalizar/validar
    $nombre  = trim((string)($_POST['nombre']  ?? ''));
    $email   = trim((string)($_POST['email']   ?? ''));
    $mensaje = (string)($_POST['mensaje'] ?? '');
    // Normaliza saltos de línea
    $mensaje = preg_replace("/\r\n?/", "\n", trim($mensaje));

    // Longitudes
    if (mb_strlen($nombre)  > 120)  $nombre  = mb_substr($nombre, 0, 120);
    if (mb_strlen($email)   > 200)  $email   = mb_substr($email,  0, 200);
    if (mb_strlen($mensaje) > 5000) {
        $_SESSION['form_data'] = ['nombre'=>$nombre,'email'=>$email,'mensaje'=>$mensaje];
        header('Location: ' . url('contacto.php?status=too_long'), true, 303);
        exit();
    }

    $nombre_ok  = $nombre !== '';
    $email_ok = (bool)filter_var($email, FILTER_VALIDATE_EMAIL)
            && preg_match('/^[^@]+@[^@]+\.[a-z]{2,}$/i', $email);    $mensaje_ok = $mensaje !== '';

    if (!$nombre_ok || !$email_ok || !$mensaje_ok) {
        $_SESSION['form_data'] = ['nombre'=>$nombre,'email'=>$email,'mensaje'=>$mensaje];
        header('Location: ' . url('contacto.php?status=invalid'), true, 303);
        exit();
    }

// Configuración desde .env
$mailHost = $_ENV['MAIL_HOST'] ?? 'smtp.hostinger.com';
$mailPort = (int)($_ENV['MAIL_PORT'] ?? 465);
$mailUser = $_ENV['MAIL_USERNAME'] ?? '';
$mailPass = $_ENV['MAIL_PASSWORD'] ?? '';
$mailFrom = $_ENV['MAIL_FROM'] ?? 'als@anthropofilia.es';
$mailTo   = $_ENV['MAIL_TO'] ?? 'analosampedro@gmail.com';

// Mitigar header injection
$safeName  = preg_replace('/[\r\n]+/', ' ', $nombre);
$safeEmail = preg_replace('/[\r\n]+/', ' ', $email);

$mail = new PHPMailer(true);

$mail->SMTPDebug = ($env === 'dev') ? 2 : 0; // Mostrar debug en logs / produccion vs desarrollo

$mail->Debugoutput = function($str, $level) use ($security) {
    $security->logEvent('debug', 'smtp_debug', ['level' => $level, 'message' => $str]);
};

try {
    // Configuración SMTP
    $mail->isSMTP();
    $mail->Host       = $mailHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailUser;
    $mail->Password   = $mailPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $mailPort;
    $mail->CharSet    = 'UTF-8';

    // Remitente y destinatario
    $mail->setFrom($mailFrom, 'Anthropofilia');
    $mail->addAddress($mailTo);
    $mail->addBCC($mailFrom); // Copia oculta al remitente
    $mail->addReplyTo($safeEmail, $safeName);

    // Contenido
    $mail->isHTML(false);
    $mail->Subject = "Nuevo mensaje de contacto de: {$safeName}";
    $mail->Body    = "Nombre: {$nombre}\nEmail: {$email}\n\nMensaje:\n{$mensaje}\n";

    $ok = $mail->send();
} catch (Exception $e) {
    $security->logEvent('error', 'phpmailer_error', ['error' => $mail->ErrorInfo]);
    $ok = false;
}

    if ($ok) {
        $security->logEvent('info', 'contact_sent', ['from'=>$safeEmail]);
        unset($_SESSION['form_data']);
        header('Location: ' . url('contacto.php?status=success'), true, 303);
    } else {
        $security->logEvent('error', 'contact_send_failed', ['from'=>$safeEmail]);
        $_SESSION['form_data'] = ['nombre'=>$nombre,'email'=>$email,'mensaje'=>$mensaje];
        header('Location: ' . url('contacto.php?status=error_send'), true, 303);
    }
    exit();

} catch (Throwable $e) {
    $_SESSION['form_data'] = [
        'nombre'  => trim((string)($_POST['nombre']  ?? '')),
        'email'   => trim((string)($_POST['email']   ?? '')),
        'mensaje' => trim((string)($_POST['mensaje'] ?? '')),
    ];
    header('Location: ' . url('contacto.php?status=error'), true, 303);
    exit();
}
