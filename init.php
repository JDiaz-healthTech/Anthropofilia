<?php
// init.php
declare(strict_types=1);

// 1) Autoload (Composer)
require_once __DIR__ . '/vendor/autoload.php';

use App\SecurityManager;

// 2) Entorno (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3) Ajustes de runtime según entorno
$env = $_ENV['APP_ENV'] ?? 'prod';
if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/logs/php-error.log');
}

// Charset / timezone (opcional pero recomendado)
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
date_default_timezone_set($_ENV['APP_TZ'] ?? 'Europe/Madrid');

// 4) Conexión PDO
$host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port    = (int)($_ENV['DB_PORT'] ?? 3306);
$db      = $_ENV['DB_NAME'] ?? '';
$user    = $_ENV['DB_USER'] ?? '';
$pass    = $_ENV['DB_PASS'] ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // PDO::ATTR_TIMEOUT         => 5, // si quieres timeout
    // PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='STRICT_ALL_TABLES'", // opcional
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('[DB] Connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Error de conexión. Inténtalo más tarde.');
}

// 5) SecurityManager
$security = new SecurityManager(
    [
        'env'         => $env,
        'trust_proxy' => !empty($_ENV['TRUST_PROXY']),   // si estás detrás de proxy/cdn
        'csp' => [
            // si quieres desactivar inline y usar nonce en tus <script>:
            'allow_unsafe_inline' => ($_ENV['CSP_INLINE'] ?? 'true') === 'true',
            'tinymce_cdn'         => 'https://cdn.tiny.cloud',
            'extra_script_src'    => ['https://cdn.jsdelivr.net'],
        ],
    ],
    $pdo
);

// Importante: ningún echo/HTML antes de esto
$security->boot();

// (opcional) URL base para canónicas/links absolutos
$_APP_BASE_URL = $_ENV['APP_URL'] ?? null;
