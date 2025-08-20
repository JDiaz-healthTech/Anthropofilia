<?php
// init.php (Versión Profesional)

// -- 1. CARGAR DEPENDENCIAS Y CLASES PRINCIPALES --
// Esto nos da acceso a las librerías instaladas, como phpdotenv y HTMLPurifier.
require_once __DIR__ . '/vendor/autoload.php';

// --> NUEVO: Cargamos nuestra clase de seguridad personalizada.
require_once __DIR__ . '/security_manager.php';


// -- 2. CARGAR LAS VARIABLES DE ENTORNO --
// Le decimos a Dotenv que busque un archivo .env en el directorio actual y lo cargue.
// Las variables estarán disponibles en el array superglobal $_ENV.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// -- 3. CONFIGURACIÓN DE ERRORES (SENSIBLE AL ENTORNO) --
// --> MODIFICADO: Leemos la variable APP_ENV de tu .env para decidir si mostrar errores.
if (($_ENV['APP_ENV'] ?? 'prod') === 'dev') {
    // En desarrollo, queremos ver todos los errores para depurar.
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    // En producción, nunca mostramos errores. Se guardan en un log.
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    // Idealmente, la ruta del log también estaría en el .env
    ini_set('error_log', __DIR__ . '/logs/php-error.log');
}


// -- 4. CREAR LA CONEXIÓN A LA BASE DE DATOS CON PDO --
// --> MODIFICADO: Usamos PDO en lugar de mysqli. Es más moderno, seguro y flexible.
// Esta variable $pdo estará disponible en todos los scripts que incluyan init.php.
$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$charset = 'utf8mb4';

// El "DSN" (Data Source Name) contiene la información de conexión.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de PDO para una conexión robusta y segura.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,      // Lanza excepciones en errores, deteniendo el script.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Devuelve los resultados como arrays asociativos.
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Usa sentencias preparadas nativas de MySQL.
];

try {
    // Creamos la instancia de PDO.
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En un entorno real, tu SecurityManager podría registrar este error antes de terminar.
    // die() no es ideal en producción, pero aquí previene la ejecución si no hay BBDD.
    die("Error de Conexión Crítico: No se pudo conectar a la base de datos.");
}


// -- 5. ARRANCAR EL GESTOR DE SEGURIDAD --
// --> NUEVO: Este es el paso final y más importante.
// Creamos la instancia de nuestro gestor de seguridad, le pasamos la conexión PDO
// y lo arrancamos con boot().
$security = SecurityManager::instance(
    ['env' => $_ENV['APP_ENV'] ?? 'prod'], // Le decimos si estamos en 'dev' o 'prod'
    $pdo                                  // Le damos acceso a la BBDD
);

// boot() se encarga de iniciar la sesión segura, aplicar cabeceras,
// y ejecutar el rate limiter en cada petición.
$security->boot();


// NOTA IMPORTANTE:
// Tu antiguo fichero 'config.php' ya no es necesario, ya que las credenciales
// se leen directamente de .env. Puedes eliminarlo.
// La variable de conexión que usarás en tus scripts a partir de ahora es $pdo.

?>