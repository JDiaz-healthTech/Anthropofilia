<?php $page_title = 'Login de Administrador'; ?>
<?php require_once 'header.php'; ?>

<main>
    <h2>Acceso al Panel</h2>
    <form action="procesar_login.php" method="POST" class="form-container">
        <div>
            <label for="nombre_usuario">Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        </div>
        <div>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>