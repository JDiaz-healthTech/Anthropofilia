<?php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

use App\Models\User;

// 1) Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: registro.php');
    exit();
}

// 2) CSRF
$security->requireValidCsrf();

// 3) Rate limit: 5 intentos de registro por IP cada 30 min
$security->checkRateLimit('registro', 5, 1800);

// 4) Recoger inputs
$nombre   = trim((string)($_POST['nombre'] ?? ''));
$apellido1 = trim((string)($_POST['apellido1'] ?? ''));
$apellido2 = trim((string)($_POST['apellido2'] ?? ''));
$email     = trim((string)($_POST['email'] ?? ''));
$contrasena        = (string)($_POST['contrasena'] ?? '');
$contrasenaConfirm = (string)($_POST['contrasena_confirm'] ?? '');
$rgpd = (bool)($_POST['rgpd'] ?? false);

// Guardar datos para PRG (nunca contraseñas)
$formData = [
    'nombre'   => $nombre,
    'apellido1' => $apellido1,
    'apellido2' => $apellido2,
    'email'     => $email,
];

// 5) Validar RGPD
if (!$rgpd) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=rgpd');
    exit();
}

// 6) Validar nombre y apellidos (1-3 palabras, solo letras y espacios)
$nombrePattern = '/^\p{L}+(?:\s+\p{L}+){0,2}$/u';

if ($nombre === '' || $apellido1 === '') {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=invalid');
    exit();
}

if (!preg_match($nombrePattern, $nombre)
    || !preg_match($nombrePattern, $apellido1)
    || ($apellido2 !== '' && !preg_match($nombrePattern, $apellido2))
) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=invalid_name');
    exit();
}

// 7) Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=invalid_email');
    exit();
}

// 8) Validar contraseña
if (mb_strlen($contrasena) < 8) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=password_short');
    exit();
}

if ($contrasena !== $contrasenaConfirm) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=password_match');
    exit();
}

// 9) Comprobar email duplicado
$existente = User::findByEmail($email);
if ($existente) {
    $_SESSION['form_registro'] = $formData;
    header('Location: registro.php?status=email_exists');
    exit();
}

// 10) Construir nombre completo para nombre_usuario
$nombreCompleto = $nombre . ' ' . $apellido1;
if ($apellido2 !== '') {
    $nombreCompleto .= ' ' . $apellido2;
}

// 11) Hash de contraseña e insertar
try {
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    User::create($nombreCompleto, $email, $hash);

    $security->logEvent('info', 'user_registered', ['email' => $email]);

    header('Location: login.php?status=registered');
    exit();

} catch (\PDOException $e) {
    $security->logEvent('error', 'registration_db_error', ['error' => $e->getMessage()]);
    $_SESSION['form_registro'] = $formData;
    $_SESSION['registro_error'] = 'Error al crear la cuenta. Inténtalo de nuevo.';
    header('Location: registro.php?status=db_error');
    exit();
}
