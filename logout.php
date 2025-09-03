<?php
// logout.php
declare(strict_types=1);

// NUNCA imprimas nada antes:
require_once __DIR__ . '/init.php';

// Solo POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

// CSRF
try {
    if (isset($security) && method_exists($security, 'requireValidCsrf')) {
        $security->requireValidCsrf();
    } else {
        $token = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            throw new RuntimeException('CSRF inválido');
        }
    }
} catch (Throwable $e) {
    http_response_code(403);
    exit('Acceso denegado (CSRF).');
}

// Invalidar sesión de forma correcta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar datos
$_SESSION = [];

// Borrar cookie de sesión (respetando parámetros)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    // iguala los flags que usas en init.php (path, domain, secure, httponly, samesite si procede)
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Destruir sesión
session_destroy();

// Evitar caché de la respuesta de logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Redirigir a inicio (mejor 303 tras POST)
header('Location: ' . url('index.php'), true, 303);
exit;
