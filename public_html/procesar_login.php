<?php
// procesar_login.php
declare(strict_types=1);

require_once __DIR__ . '/init.php';

// 1) Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: login.php');
    exit();
}

// 2) CSRF
$security->requireValidCsrf();

// 3) Rate limit dedicado a login (p.ej. 5 intentos / 15 min por IP)
$security->checkRateLimit('login_attempt', 5, 900);

// 4) Inputs
$email      = trim((string)($_POST['email'] ?? ''));
$contrasena = (string)($_POST['contrasena'] ?? '');

if ($email === '' || $contrasena === '') {
    $_SESSION['form_data'] = ['email' => $email];
    header('Location: login.php?status=invalid');
    exit();
}

try {
    // 5) Buscar usuario por email
    $stmt = $pdo->prepare(
        'SELECT id_usuario, nombre_usuario, email, rol, contrasena_hash
         FROM usuarios
         WHERE email = ?
         LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $ok = $user && password_verify($contrasena, (string)$user['contrasena_hash']);

    if (!$ok) {
        usleep(250000);
        $security->logEvent('security', 'login_failed', ['email' => $email]);
        $_SESSION['form_data'] = ['email' => $email];
        header('Location: login.php?status=bad_credentials');
        exit();
    }

    // 6) Rehash si el algoritmo por defecto ha cambiado (opcional pero recomendado)
    if (password_needs_rehash((string)$user['contrasena_hash'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($contrasena, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE usuarios SET contrasena_hash = ? WHERE id_usuario = ?');
        $upd->execute([$newHash, (int)$user['id_usuario']]);
    }

    // 7) Sesión: regenerar ID y establecer datos
    session_regenerate_id(true);
    $_SESSION['id_usuario']     = (int)$user['id_usuario'];
    $_SESSION['nombre_usuario'] = (string)$user['nombre_usuario'];
    $_SESSION['rol']            = (string)$user['rol'];
    $_SESSION['roles']          = [ (string)$user['rol'] ]; // para SecurityManager::roles()

    $security->logEvent('info', 'login_success', ['user_id' => (int)$user['id_usuario']]);

    // 8) Redirigir a panel
    header('Location: dashboard.php');
    exit();

} catch (\PDOException $e) {
    $security->logEvent('error', 'login_db_error', ['error' => $e->getMessage()]);
    $_SESSION['form_data'] = ['email' => $email];
    header('Location: login.php?status=error');
    exit();
}
