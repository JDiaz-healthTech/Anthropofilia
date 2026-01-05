<?php
/**
 * init.php - Bootstrap de la aplicación
 */
declare(strict_types=1);

// 1. IMPORTACIONES (Siempre arriba del todo)
use App\Models\Database;
use App\Security\SecurityManager;
use Dotenv\Dotenv;

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
// AUTOLOAD (Composer)
// ============================================================
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('<h1>Error: vendor/autoload.php no encontrado</h1>');
}
require_once $autoloadPath;

// ============================================================
// VARIABLES DE ENTORNO (.env)
// ============================================================
if (!file_exists(BASE_PATH . '/.env')) {
    die('<h1>Error: .env no encontrado</h1>');
}
$dotenv = Dotenv::createImmutable(BASE_PATH);
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
// BASE URL
// ============================================================
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $_ENV['APP_URL'] ?? ($scheme . '://' . $host);

function url(string $path = ''): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $protocol . '://' . $host;
    
    // Remover "/" inicial si existe
    $path = ltrim($path, '/');
    
    return $baseUrl . '/' . $path;
}

function canonical_url(): string {
    global $baseUrl;
    $requestPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return rtrim($baseUrl, '/') . $requestPath;
}

// ============================================================
// CONEXIÓN A BASE DE DATOS (PDO)
// ============================================================
try {
    // Usamos la clase importada arriba.
    // Esto define la variable $pdo que usaremos abajo
    $pdo = Database::getConnection();
    
} catch (Exception $e) {
    error_log('[DB] Connection failed: ' . $e->getMessage());
    
    if (isset($env) && $env === 'dev') {
        die("<h1>Error de Conexión</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>");
    }
    
    http_response_code(500);
    exit('Error de conexión a la base de datos.');
}

// ============================================================
// SECURITY MANAGER
// ============================================================
// Ya no hace falta "use" aquí, está arriba del todo.

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
    $pdo // Pasamos la conexión creada en el bloque anterior
);
$security->boot();

// ============================================================
// HELPERS ADICIONALES
// ============================================================
require_once BASE_PATH . '/admin/lib/settings.php';