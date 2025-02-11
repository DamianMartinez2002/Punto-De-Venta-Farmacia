<?php
session_start(); // Inicia la sesión

include('db.php'); // Conexión a la base de datos

// Verificar conexión a la base de datos
if (!$conexion) { // Cambié $conn a $conexion para que coincida con el nombre de la variable en db.php
    die("Error: La conexión a la base de datos ha fallado.");
}

// Verificar si se recibió una solicitud para eliminar un usuario
if (isset($_GET['eliminar_id'])) {
    // Obtener el ID del usuario a eliminar
    $eliminar_id = $_GET['eliminar_id'];

    // Escapar el ID para prevenir inyecciones SQL
    $eliminar_id = mysqli_real_escape_string($conexion, $eliminar_id); // Cambié $conn a $conexion

    // Consulta para eliminar el usuario
    $sql_delete = "DELETE FROM usuarios WHERE id_usuario = '$eliminar_id'";

    // Ejecutar la consulta
    if ($conexion->query($sql_delete) === TRUE) { // Cambié $conn a $conexion
        echo "<p>Usuario eliminado con éxito.</p>";
        header("Location: usuarios.php"); // Recargar la página para reflejar el cambio
        exit();
    } else {
        echo "<p>Error al eliminar el usuario: " . $conexion->error . "</p>"; // Cambié $conn a $conexion
    }
}

// Verificar si la sesión tiene el rol asignado, si no, asigna un valor por defecto
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'empleado'; // Asegúrate de que esta variable esté definida

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $usuario = $_POST['usuario'];
    $password = $_POST['contrasena'];
    $role = $_POST['role'];  // Puede ser 'admin' o 'empleado'
    $nombre_completo = $_POST['nombre_completo']; 

    // Validar y escapar los datos para evitar inyecciones SQL
    $usuario = mysqli_real_escape_string($conexion, $usuario); // Cambié $conn a $conexion
    $password = mysqli_real_escape_string($conexion, $password); // Cambié $conn a $conexion
    $nombre_completo = mysqli_real_escape_string($conexion, $nombre_completo); // Cambié $conn a $conexion
    
    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Consulta SQL para insertar el nuevo usuario en la tabla 'usuarios'
    $sql = "INSERT INTO usuarios (nombre_completo, usuario, contrasena, rol) VALUES ('$nombre_completo', '$usuario', '$hashed_password', '$role')";

    // Ejecutar la consulta
    if ($conexion->query($sql) === TRUE) { // Cambié $conn a $conexion
        header("Location: usuarios.php"); // Recargar la página para mostrar el nuevo usuario
        exit();
    } else {
        echo "<p>Error al registrar: " . $conexion->error . "</p>"; // Cambié $conn a $conexion
    }
}

// Obtener la lista de usuarios de la tabla 'usuarios'
$sql = "SELECT id_usuario, nombre_completo, usuario, rol, fecha_registro FROM usuarios";
$result = $conexion->query($sql); // Cambié $conn a $conexion
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="usuarios.css">
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
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventadetalle.php') ? 'active' : ''; ?>"><a href="ventadetalle.php">Detalle de Productos</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'configuracion.php') ? 'active' : ''; ?>"><a href="configuracion.php">Configuración de Tienda</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>"><a href="clientes.php">Clientes</a></li>
        <?php elseif ($role == 'empleado'): ?>
            <h3>EMPLEADO</h3>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventas.php') ? 'active' : ''; ?>"><a href="caja.php">Caja</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'registrar-producto.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadisticas</a></li>
        <?php endif; ?>
        <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
    </ul>
</div>

    <div class="container">
        <!-- Contenido de la página -->
        <div class="user-form">
            <h2><i class="fas fa-user-plus"></i> Registrar Usuario</h2>
            <form action="usuarios.php" method="POST">
                <label for="nombre_completo"><i class="fas fa-user"></i> Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" required placeholder="Nombre completo del usuario">
                
                <label for="usuario"><i class="fas fa-user"></i> Usuario:</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Ingresa un usuario">
                
                <label for="contrasena"><i class="fas fa-lock"></i> Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required placeholder="Ingresa una contraseña">
                
                <label for="role"><i class="fas fa-user-tag"></i> Rol:</label>
                <select name="role" id="role">
                    <option value="empleado">Empleado</option>
                    <option value="admin">Administrador</option>
                </select>
                
                <button type="submit"><i class="fas fa-save"></i> Registrar</button>
            </form>
        </div>

        <div class="user-table">
            <h2><i class="fas fa-users"></i> Lista de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_usuario']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                            <td><?php echo ucfirst($row['rol']); ?></td>
                            <td><?php echo $row['fecha_registro']; ?></td>
                            <td>
                                <?php if ($role == 'administrador'): ?>
                                    <!-- Botón de eliminar -->
                                    <a href="usuarios.php?eliminar_id=<?php echo $row['id_usuario']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>

                                <?php endif; ?>
                            </td>
                            
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
