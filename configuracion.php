<?php
session_start();

// Verificar si el rol está definido en la sesión
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role']; // Si el rol está guardado en la sesión
} else {
    $role = 'invitado'; // O asignar un valor por defecto
    // Redirigir si es necesario
    // header('Location: login.php');
    // exit;
}

include 'db.php'; // Incluir archivo de conexión

// Verificar si la conexión fue exitosa
if (!isset($conexion)) {
    die("Error: La conexión a la base de datos no está definida.");
}

// Registrar configuración de la tienda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_tienda = $_POST['nombre_tienda'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $horario = $_POST['horario'];
    
    $query = "INSERT INTO configuraciondetienda (nombre_tienda, direccion, telefono, email, horario) 
              VALUES ('$nombre_tienda', '$direccion', '$telefono', '$email', '$horario')";
    
    if (mysqli_query($conexion, $query)) {
        $mensaje = "Configuración de tienda registrada con éxito.";
    } else {
        $mensaje = "Error al registrar la configuración: " . mysqli_error($conexion);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de la Tienda</title>
    <link rel="stylesheet" href="configuracion.css">
</head>
<body>
    <!-- Menú Lateral -->
    <div class="sidebar">
        <!-- Reemplaza el h2 por el logo de imagen -->
        <div class="logo-container">
            <img src="images/1.jpg" alt="Logo de Farmacia El Bienestar" class="logo">
        </div>
        <ul>
            <?php if ($role == 'administrador'): ?>
                <h3> ADMINISTRADOR</h3>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'estadisticas.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadísticas</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventas.php') ? 'active' : ''; ?>"><a href="ventas.php">Ventas</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'usuarios.php') ? 'active' : ''; ?>"><a href="usuarios.php">Usuarios</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventadetalle.php') ? 'active' : ''; ?>"><a href="ventadetalle.php">Detalle de Productos</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'configuracion.php') ? 'active' : ''; ?>"><a href="configuracion.php">Configuración de Tienda</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>"><a href="clientes.php">Clientes</a></li>
            <?php elseif ($role == 'empleado'): ?>
                <h3>EMPLEADO</h3>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'caja.php') ? 'active' : ''; ?>"><a href="caja.php">Caja</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
                <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'registrar-producto.php') ? 'active' : ''; ?>"><a href="registrar-producto.php">Registrar Producto</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>FARMACIA EL BIENESTAR</h1>

        <?php if (isset($mensaje)): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form action="configuracion.php" method="POST">
            <label for="nombre_tienda">Nombre de la Tienda:</label>
            <input type="text" id="nombre_tienda" name="nombre_tienda" required>

            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="horario">Horario de Atención:</label>
            <input type="text" id="horario" name="horario" required>

            <button type="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
