<?php
// config.php

// -- Credenciales de la Base de Datos --
// Usamos constantes para que no puedan ser redefinidas por error.
define('DB_HOST', 'localhost');
define('DB_USER', 'blogmaster');
define('DB_PASS', 'blogmasteradmin22!');
define('DB_NAME', 'BlogDB');

// -- Crear la conexión usando el estilo Orientado a Objetos de MySQLi --
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// -- Comprobar la conexión --
// ¡Esto es crucial! Si la conexión falla, el script se detiene y muestra el error.
if ($conn->connect_error) {
    die("Error de Conexión: " . $conn->connect_error);
}

// -- Asegurar que la conexión use UTF-8 --
// Esto previene problemas con tildes y 'ñ' al leer o escribir en la BD.
$conn->set_charset("utf8mb4");

?>