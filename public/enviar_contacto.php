<?php
// enviar_contacto.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . url('contacto.php'), true, 302);
    exit();
}

try {
    // CSRF
    $security->csrfValidate($_POST['csrf_token'] ?? null);

    // Rate limit: 5 envíos/h por IP
    $security->checkRateLimit('contact_form', 5, 3600);

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
    $email_ok   = (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    $mensaje_ok = $mensaje !== '';

    if (!$nombre_ok || !$email_ok || !$mensaje_ok) {
        $_SESSION['form_data'] = ['nombre'=>$nombre,'email'=>$email,'mensaje'=>$mensaje];
        header('Location: ' . url('contacto.php?status=invalid'), true, 303);
        exit();
    }

    // Email
    $to   = $_ENV['MAIL_TO']   ?? 'analosampedro@gmail.com';
    $host = parse_url($_ENV['APP_URL'] ?? ('https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), PHP_URL_HOST) ?: 'localhost';
    $from = $_ENV['MAIL_FROM'] ?? ('no-reply@' . $host);

    // Mitigar header injection
    $safeName  = preg_replace('/[\r\n]+/', ' ', $nombre);
    $safeEmail = preg_replace('/[\r\n]+/', ' ', $email);

    $subjectPlain = "Nuevo mensaje de contacto de: {$safeName}";
    $subject = '=?UTF-8?B?' . base64_encode($subjectPlain) . '?=';

    $body = "Nombre: {$nombre}\nEmail: {$email}\n\nMensaje:\n{$mensaje}\n";

    $headers  = "From: {$from}\r\n";
    if ($email_ok) {
        $headers .= "Reply-To: {$safeName} <{$safeEmail}>\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: Anthropofilia\r\n";

    $ok = @mail($to, $subject, $body, $headers);

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
