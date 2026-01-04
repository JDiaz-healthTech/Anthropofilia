<?php
/**
 * init.php - Bootstrap de la aplicación
 * 
 * Este archivo está en /public y carga recursos de la raíz del proyecto.
 * PUBLIC_PATH = /var/www/html/public (donde está este archivo)
 * BASE_PATH   = /var/www/html (raíz del proyecto, un nivel arriba)
 */
declare(strict_types=1);

// ============================================================
// CONSTANTES DE RUTAS
// ============================================================
define('PUBLIC_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));

// ============================================================
// MANEJO DE ERRORES (Desarrollo)
// ============================================================
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo "<h1 style='color:red'>Error fatal en PHP</h1>";
        echo "<pre>" . print_r($error, true) . "</pre>";
    }
});

set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    echo "<h1 style='color:red'>Excepción no capturada</h1>";
    echo "<p><strong>" . get_class($e) . ":</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
});

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// ============================================================
// AUTOLOAD (Composer) - En la RAÍZ del proyecto
// ============================================================
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('<h1>Error: vendor/autoload.php no encontrado</h1>
         <p>Ejecuta: <code>composer install</code> en la raíz del proyecto</p>
         <p>Buscando en: ' . $autoloadPath . '</p>');
}
require_once $autoloadPath;

// ============================================================
// VARIABLES DE ENTORNO (.env) - En la RAÍZ del proyecto
// ============================================================
if (!file_exists(BASE_PATH . '/.env')) {
    die('<h1>Error: .env no encontrado</h1>
         <p>Copia .env.example a .env y configúralo</p>
         <p>Buscando en: ' . BASE_PATH . '/.env</p>');
}
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// ============================================================
// CONFIGURACIÓN SEGÚN ENTORNO
// ============================================================
$env = $_ENV['APP_ENV'] ?? 'prod';
if ($env === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');
}

// Charset y timezone
ini_set('default_charset', 'UTF-8');
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
date_default_timezone_set($_ENV['APP_TZ'] ?? 'Europe/Madrid');

// ============================================================
// BASE URL (para generar enlaces)
// ============================================================
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $_ENV['APP_URL'] ?? ($scheme . '://' . $host);

/**
 * Genera una URL absoluta
 */
function url(string $path = ''): string {
    global $baseUrl;
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Genera la URL canónica de la página actual
 */
function canonical_url(): string {
    global $baseUrl;
    $requestPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return rtrim($baseUrl, '/') . $requestPath;
}

// ============================================================
// CONEXIÓN A BASE DE DATOS (PDO)
// ============================================================
$dbHost    = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort    = (int)($_ENV['DB_PORT'] ?? 3306);
$dbName    = $_ENV['DB_NAME'] ?? '';
$dbUser    = $_ENV['DB_USER'] ?? '';
$dbPass    = $_ENV['DB_PASS'] ?? '';
$dbCharset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset={$dbCharset}";

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (PDOException $e) {
    error_log('[DB] Connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Error de conexión a la base de datos. Inténtalo más tarde.');
}

// ============================================================
// SECURITY MANAGER - Namespace corregido: App\Security
// ============================================================
use App\Security\SecurityManager;

$security = new SecurityManager(
    [
        'env'         => $env,
        'trust_proxy' => !empty($_ENV['TRUST_PROXY']),
        'csp' => [
            'allow_unsafe_inline' => ($_ENV['CSP_INLINE'] ?? 'true') === 'true',
            'tinymce_cdn'         => 'https://cdn.tiny.cloud',
            'extra_script_src'    => ['https://cdn.jsdelivr.net'],
        ],
    ],
    $pdo
);
$security->boot();

// ============================================================
// HELPERS ADICIONALES - En la RAÍZ del proyecto
// ============================================================
require_once BASE_PATH . '/admin/lib/settings.php';

// ============================================================
// FIN DE INIT.PHP
// ============================================================