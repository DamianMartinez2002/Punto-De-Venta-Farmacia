<?php
include('db.php'); // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']); // Escapar el rol

    // Validación de contraseñas
    if ($password !== $confirm_password) {
        echo "<div class='alert error'><p>Las contraseñas no coinciden.</p></div>";
        exit;
    }

    // Escapar datos y encriptar contraseña
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Verificar si el usuario ya existe
    $sql = "SELECT * FROM usuarios WHERE usuario = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<div class='alert error'><p>El nombre de usuario ya está registrado. Por favor elige otro.</p></div>";
    } else {
        // Consulta para insertar usuario
        $sql = "INSERT INTO usuarios (nombre_completo, usuario, contrasena, rol) 
                VALUES ('$full_name', '$username', '$hashed_password', '$role')";

        // Ejecutar consulta y manejar errores
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert success'><p>¡Registro exitoso!.</p></div>";
        } else {
            echo "<div class='alert error'><p>Error al registrar: " . $conn->error . "</p></div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Registro de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="registrar.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Regístrate</h2>
            <p>Crea tu cuenta para comenzar a usar el sistema</p>
        </div>
        <form action="registrar.php" method="POST">
            <div class="input-container">
                <label for="full_name">Nombre Completo:</label>
                <input type="text" id="full_name" name="full_name" placeholder="Escribe tu nombre completo" required>
            </div>
            <div class="input-container">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Escribe tu usuario" required>
            </div>
            <div class="input-container">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Escribe tu contraseña" required>
            </div>
            <div class="input-container">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirma tu contraseña" required>
            </div>
            <div class="input-container">
                <label for="role">Rol:</label>
                <select id="role" name="role" required>
                    <option value="empleado">Empleado</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Registrar</button>
        </form>
        <div class="register-link">
            <p>¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
        <div class="footer-version">Ver. Lite 1.0.0</div>
    </div>
</body>
</html>
