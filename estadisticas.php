<?php
session_start(); // Inicia la sesión

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

// Verificar conexión a la base de datos
if (!isset($conexion)) {
    die("Error: La conexión a la base de datos no está definida.");
}

// Verificar si es un empleado
if ($role == 'empleado') {
    // Verificar si la fecha de último acceso está definida en la sesión
    if (!isset($_SESSION['ultimo_acceso'])) {
        $_SESSION['ultimo_acceso'] = date('Y-m-d'); // Guardamos la fecha del primer acceso
    }

    // Comparar si el día actual es diferente al último acceso
    if ($_SESSION['ultimo_acceso'] != date('Y-m-d')) {
        // Si el día ha cambiado, reiniciar las estadísticas
        unset($_SESSION['total_ventas']);
        unset($_SESSION['producto_mas_vendido']);
        unset($_SESSION['ventas_por_dia']);

        // Actualizar la fecha de último acceso
        $_SESSION['ultimo_acceso'] = date('Y-m-d');
    }
}

// Consultar total de ventas
$total_ventas_query = "SELECT SUM(total) AS total_ventas FROM venta";
$total_ventas_result = mysqli_query($conexion, $total_ventas_query);
$total_ventas = 0;
if ($total_ventas_result) {
    $total_ventas_row = mysqli_fetch_assoc($total_ventas_result);
    $total_ventas = $total_ventas_row['total_ventas'] ?? 0;
}

// Consultar el producto más vendido
$producto_mas_vendido_query = "
    SELECT p.nombre, COUNT(v.id_producto) AS cantidad_vendida, SUM(v.total) AS total_vendido
    FROM productos p
    JOIN venta v ON p.id_producto = v.id_producto
    GROUP BY p.id_producto
    ORDER BY cantidad_vendida DESC
    LIMIT 1;
";
$producto_mas_vendido_result = mysqli_query($conexion, $producto_mas_vendido_query);
$producto_mas_vendido = null;
if ($producto_mas_vendido_result) {
    $producto_mas_vendido = mysqli_fetch_assoc($producto_mas_vendido_result);
}

// Consultar ventas por día (últimos 7 días)
$ventas_por_dia_query = "SELECT DATE(fecha) AS fecha, SUM(total) AS total_ventas
                         FROM venta
                         WHERE id_usuario = {$_SESSION['id_usuario']} 
                         AND fecha >= CURDATE() - INTERVAL 7 DAY
                         GROUP BY DATE(fecha)
                         ORDER BY fecha ASC";

$ventas_por_dia_result = mysqli_query($conexion, $ventas_por_dia_query);
$ventas_por_dia = [];
if ($ventas_por_dia_result) {
    while ($row = mysqli_fetch_assoc($ventas_por_dia_result)) {
        $ventas_por_dia[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Ventas</title>
    <link rel="stylesheet" href="estadisticas.css">
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
        <li><a href="ventas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ventas.php' ? 'active' : ''; ?>">Caja</a></li>
        <li><a href="corte.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'corte.php' ? 'active' : ''; ?>">Corte</a></li>
        <li><a href="productos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>">Productos</a></li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'registrar-producto.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadisticas</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
        </ul>
    </div>

    <div class="container">
        <h1>Estadísticas de Ventas</h1>

        <!-- Total de ventas -->
        <div>
            <h3>Total de Ventas</h3>
            <p>$<?php echo number_format($total_ventas, 2); ?></p>
        </div>

        <!-- Producto más vendido -->
        <div>
            <h3>Producto Más Vendido</h3>
            <?php if ($producto_mas_vendido): ?>
                <p><?php echo $producto_mas_vendido['nombre']; ?> - <?php echo $producto_mas_vendido['cantidad_vendida']; ?> unidades</p>
            <?php else: ?>
                <p>No se encontraron productos vendidos.</p>
            <?php endif; ?>
        </div>

        <!-- Ventas por día (últimos 7 días) -->
        <div>
            <h3>Ventas por Día (Últimos 7 Días)</h3>
            <?php if (!empty($ventas_por_dia)): ?>
                <table>
                    <tr>
                        <th>Fecha</th>
                        <th>Total Ventas</th>
                    </tr>
                    <?php foreach ($ventas_por_dia as $venta): ?>
                        <tr>
                            <td><?php echo $venta['fecha']; ?></td>
                            <td>$<?php echo number_format($venta['total_ventas'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No se encontraron ventas en los últimos 7 días.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
