<?php
require_once 'config.php';

// 1. INICIAR LA SESIÓN
// session_start() debe ser lo primero en cualquier script que use sesiones.
session_start();

// 2. OBTENER DATOS DEL FORMULARIO
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = $_POST['contrasena'];

// 3. BUSCAR AL USUARIO EN LA BASE DE DATOS
$sql = "SELECT id_usuario, rol, contrasena_hash FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// 4. VERIFICAR LA CONTRASEÑA
// password_verify() compara la contraseña enviada con el hash guardado.
if ($usuario && password_verify($contrasena, $usuario['contrasena_hash'])) {
    
    // Contraseña correcta: ¡Login exitoso!
    // 5. GUARDAR DATOS EN LA SESIÓN
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre_usuario'] = $nombre_usuario;
    $_SESSION['rol'] = $usuario['rol'];

    // Redirigimos al panel de administración
    header("Location: admin.php");
    exit();

} else {
    // Usuario o contraseña incorrectos
    echo "Error: Usuario o contraseña no válidos.";
    // Opcional: Redirigir de nuevo al login con un mensaje de error.
    // header("Location: login.php?error=1");
    // exit();
}

$stmt->close();
$conn->close();
?>