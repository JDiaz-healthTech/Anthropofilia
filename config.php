<?php
declare(strict_types=1);

// config.php

// Helper para leer variables de entorno de forma segura
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $val = $_ENV[$key] ?? getenv($key);
        return ($val !== false && $val !== null && $val !== '') ? $val : $default;
    }
}

// Requeridas
$required = ['DB_HOST','DB_USER','DB_PASS','DB_NAME'];
foreach ($required as $k) {
    if (env($k) === null) {
        http_response_code(500);
        error_log("Falta la variable de entorno {$k}"); // no mostrar credenciales al usuario
        die('Error de configuración.');
    }
}

// Normaliza y construye el DSN
$host    = env('DB_HOST');
$port    = (int) (env('DB_PORT') ?? 3306);
$socket  = env('DB_SOCKET'); // opcional (unix socket)
$dbname  = env('DB_NAME');
$charset = env('DB_CHARSET') ?: 'utf8mb4_unicode_ci';

$dsn = $socket
    ? sprintf('mysql:unix_socket=%s;dbname=%s;charset=%s', $socket, $dbname, $charset)
    : sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);

// Constantes para usar en init.php
define('DB_DSN', $dsn);
define('DB_USER', env('DB_USER'));
define('DB_PASS', env('DB_PASS'));
define('DB_CHARSET', $charset);

// Opciones PDO recomendadas (array válido para define en PHP 7+)
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Si usas MySQL: asegura collation desde el inicio (ajusta si usas otra)
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
]);
