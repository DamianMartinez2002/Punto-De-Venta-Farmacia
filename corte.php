<?php
session_start();
require 'db.php';

// Verificar si el usuario ha iniciado sesión y si tiene el rol de empleado
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'empleado') {
    echo "<h2 style='color:red; text-align:center;'>Acceso denegado</h2>";
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$username = $_SESSION['username'] ?? 'Desconocido'; // Manejar si no está definido el nombre de usuario
$fecha_actual = date("Y-m-d H:i:s");

// Obtener el total vendido por el empleado en su turno actual (solo las ventas de hoy)
$query = $conexion->prepare("SELECT SUM(total) AS total_vendido FROM venta WHERE id_usuario = ? AND DATE(fecha) = CURDATE()");
$query->bind_param("i", $id_usuario);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$total_vendido = $row['total_vendido'] ?? 0;

// Si el empleado hace clic en "Cerrar Turno"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cerrar_corte'])) {
    // Registrar el corte en la base de datos
    $query_corte = $conexion->prepare("INSERT INTO cortes (id_usuario, total_vendido, fecha) VALUES (?, ?, ?)");
    $query_corte->bind_param("ids", $id_usuario, $total_vendido, $fecha_actual);
    $query_corte->execute();

    // Mensaje de confirmación
    $mensaje = "Corte de caja realizado con éxito.";

    // Redirigir al index.php para que el siguiente trabajador se loguee
    header("Location: index.php");
    exit(); // Asegúrate de terminar el script para evitar más ejecuciones
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corte de Caja</title>
    <link rel="stylesheet" href="corte.css">
</head>
<body>
<div class="sidebar">
    <div class="logo-container">
        <img src="images/1.jpg" alt="Logo de Farmacia El Bienestar" class="logo">
    </div>
    <ul>
        <?php if ($_SESSION['role'] == 'administrador'): ?>
            <h3> ADMINISTRADOR</h3>
            <li><a href="estadisticas.php">Estadísticas</a></li>
            <li><a href="ventas.php">Ventas</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li class="active"><a href="productos.php">Productos</a></li>
            <li><a href="ventadetalle.php">Detalle de Productos</a></li>
            <li><a href="configuracion.php">Configuración</a></li>
            <li><a href="clientes.php">Clientes</a></li>
        <?php elseif ($_SESSION['role'] == 'empleado'): ?>
            <h3>EMPLEADO</h3>
        <li><a href="ventas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ventas.php' ? 'active' : ''; ?>">Caja</a></li>
        <li><a href="corte.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'corte.php' ? 'active' : ''; ?>">Corte</a></li>
        <li><a href="productos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>">Productos</a></li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'registrar-producto.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadisticas</a></li>
        <?php endif; ?>
        <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
    </ul>
</div>
<div class="corte-container">
    <h2>Corte de Caja</h2>
    <p>Empleado: <strong><?php echo htmlspecialchars($username); ?></strong></p>
    <p>Fecha: <strong><?php echo $fecha_actual; ?></strong></p>
    <p>Total Vendido en Turno: <strong>$<?php echo number_format($total_vendido, 2); ?> MXN</strong></p>

    <?php if (isset($mensaje)): ?>
        <p class="mensaje-exito"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" name="cerrar_corte" class="cerrar-corte-btn">Cerrar Turno</button>
    </form>
</div>
</body>
</html>
