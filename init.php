<?php
// init.php

// -- 1. CARGAR DEPENDENCIAS DE COMPOSER --
// Esto nos da acceso a las librerías instaladas, como phpdotenv.
require_once __DIR__ . '/vendor/autoload.php';

// -- 2. CARGAR LAS VARIABLES DE ENTORNO --
// Le decimos que busque un archivo .env en el directorio actual y lo cargue.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// -- 3. CONFIGURACIÓN BÁSICA --
// Establecer el manejo de errores (muy útil en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// -- 4. INICIAR LA SESIÓN --
// Es crucial que session_start() se llame antes de cualquier output.
session_start();

// -- 5. INCLUIR LAS CREDENCIALES DE LA BASE DE DATOS --
// Ahora solo traemos las constantes, no la conexión en sí.
require_once 'config.php';

// -- 6. CREAR LA CONEXIÓN A LA BASE DE DATOS --
// Esta variable $conn estará disponible en todos los scripts que incluyan init.php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Comprobar la conexión
if ($conn->connect_error) {
    // En un entorno real, aquí registrarías el error en un log y mostrarías una página de error genérica.
    die("Error de Conexión Crítico: " . $conn->connect_error);
}

// Asegurar que la conexión use UTF-8
$conn->set_charset("utf8mb4");

?>