<?php

// config/database.php
// Aseguramos que las variables críticas existan, si no, detenemos la ejecución (Fail Fast).
// Esto evita que intente conectar con credenciales vacías.
if (empty($_ENV['DB_HOST']) || empty($_ENV['DB_USER'])) {
    // Nota: En producción esto debería loguearse y mostrar un error genérico, 
    // pero en desarrollo nos ayuda a ver si falta el archivo .env
    die('Error Crítico: No se han configurado las variables de entorno de la base de datos (DB_HOST, DB_USER, etc). Revisa tu archivo .env');
}

return [
    'driver'    => 'mysql',
    // Leemos EXCLUSIVAMENTE del entorno. 
    // Si no existe la variable, usamos null o defaults seguros (como 'localhost'), NUNCA contraseñas reales.
    'host'      => $_ENV['DB_HOST'] ?? 'localhost',
    'database'  => $_ENV['DB_NAME'] ?? 'blogdb',
    'username'  => $_ENV['DB_USER'] ?? 'root',
    'password'  => $_ENV['DB_PASS'] ?? '', // ¡Sin fallback peligroso!
    
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];