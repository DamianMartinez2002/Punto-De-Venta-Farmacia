<?php
session_start(); // Inicia la sesión

include('db.php'); // Conexión a la base de datos

// Verificar si la sesión tiene el rol asignado, si no, asigna un valor por defecto
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'empleado'; // Asegúrate de que esta variable esté definida

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $nombre_completo = $_POST['nombre_completo']; 
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];

    // Validar y escapar los datos para evitar inyecciones SQL
    $nombre_completo = mysqli_real_escape_string($conexion, $nombre_completo); // Cambié $conn a $conexion
    $telefono = mysqli_real_escape_string($conexion, $telefono); // Cambié $conn a $conexion
    $email = mysqli_real_escape_string($conexion, $email); // Cambié $conn a $conexion
    $direccion = mysqli_real_escape_string($conexion, $direccion); // Cambié $conn a $conexion
    
    // Consulta SQL para insertar el nuevo cliente en la tabla 'clientes'
    $sql = "INSERT INTO clientes (nombre_completo, telefono, email, direccion) VALUES ('$nombre_completo', '$telefono', '$email', '$direccion')";

    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) { // Cambié $conn a $conexion
        header("Location: clientes.php"); // Recargar la página para mostrar el nuevo cliente
        exit();
    } else {
        echo "<p>Error al registrar: " . $conexion->error . "</p>"; // Cambié $conn a $conexion
    }
}

// Obtener la lista de clientes de la tabla 'clientes'
$sql = "SELECT id_cliente, nombre_completo, telefono, email, direccion, fecha_registro FROM clientes";
$result = $conexion->query($sql); // Cambié $conn a $conexion
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="clientes.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Iconos FontAwesome -->
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
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventadetalle.php') ? 'active' : ''; ?>"><a href="ventadetalle.php">Detalle de productos</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'configuracion.php') ? 'active' : ''; ?>"><a href="configuracion.php">Configuración de Tienda</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>"><a href="clientes.php">Clientes</a></li>
        <?php elseif ($role == 'empleado'): ?>
            <h3>EMPLEADO</h3>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'caja.php') ? 'active' : ''; ?>"><a href="caja.php">Caja</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'registrar-producto.php') ? 'active' : ''; ?>"><a href="registrar-producto.php">Registrar Producto</a></li>
        <?php endif; ?>
        <a href="logout.php" class="logout" id="logout-btn">Cerrar sesión</a>

    </ul>
</div>

    <div class="container">
        <!-- Contenido de la página -->
        <div class="client-form">
            <h2><i class="fas fa-user-plus"></i> Registrar Cliente</h2>
            <form action="clientes.php" method="POST">
                <label for="nombre_completo"><i class="fas fa-user"></i> Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" required placeholder="Nombre completo del cliente">
                
                <label for="telefono"><i class="fas fa-phone"></i> Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required placeholder="Número de teléfono">
                
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" required placeholder="Correo electrónico">
                
                <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
                <input type="text" id="direccion" name="direccion" required placeholder="Dirección del cliente">
                
                <button type="submit"><i class="fas fa-save"></i> Registrar</button>
            </form>
        </div>

        <div class="client-table">
            <h2><i class="fas fa-users"></i> Lista de Clientes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_cliente']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                            <td><?php echo $row['fecha_registro']; ?></td>
                            
                            <td>
                <!-- Enlace para eliminar el cliente -->
                <a href="clientes.php?eliminar_id=<?php echo $row['id_cliente']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar este cliente?');">
                    Eliminar
                </a>
            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        document.getElementById('logout-btn').addEventListener('click', function() {
    this.classList.toggle('active');
});
    </script>
</body>
</html>
