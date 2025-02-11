<?php
session_start();
include('db.php'); // Incluir la conexión a la base de datos

// Ahora la variable $conexion ya debería estar disponible
$query = "SELECT * FROM productos";
$stmt = $conexion->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Consulta preparada para evitar SQL Injection
    $sql = "SELECT id_usuario, usuario, contrasena, rol FROM usuarios WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $user['contrasena'])) {
            // Iniciar sesión y guardar los datos del usuario
            $_SESSION['id_usuario'] = $user['id_usuario']; // Guardar ID del usuario
            $_SESSION['username'] = $user['usuario'];
            $_SESSION['role'] = strtolower(trim($user['rol'])); // Asegurar que el rol esté en minúsculas y sin espacios

            // Redirigir al panel
            header("Location: panel.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta.";
        }
    } else {
        $error_message = "Usuario no encontrado.";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta - Inicio de Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="inicio.css">
</head>
<body>
    <!-- Formulario de inicio de sesión -->
    <div class="login-container">
        <div class="login-header">
            <h2>FARMACIA EL BIENESTAR</h2>
            <p>Por favor, inicia sesión para continuar</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?= $error_message ?></div>
        <?php endif; ?>
        
        <form action="index.php" method="POST">
            <div class="input-container">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Escribe tu usuario" required>
            </div>
            <div class="input-container">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Escribe tu contraseña" required>
            </div>
            <button type="submit" class="btn-submit">Iniciar Sesión</button>
        </form>
        
        <div class="register-link">
            <p>¿No tienes cuenta? <a href="registrar.php">Regístrate aquí</a></p>
        </div>
        
        <div class="footer-version">Ver. Lite 1.0.0</div>
    </div>

</body>
</html>
