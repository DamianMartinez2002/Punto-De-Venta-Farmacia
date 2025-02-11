<?php
session_start();
require 'db.php'; // Archivo para la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role']; // Puede ser 'administrador' o 'empleado'

// Consultas a la base de datos
// Recuento de ventas realizadas hoy
$fecha_actual = date("Y-m-d");
$query_ventas = "SELECT COUNT(*) as total_ventas FROM venta WHERE DATE(fecha) = ?";
$stmt_ventas = $conexion->prepare($query_ventas);
$stmt_ventas->bind_param("s", $fecha_actual);
$stmt_ventas->execute();
$result_ventas = $stmt_ventas->get_result();
$ventas_hoy = $result_ventas->fetch_assoc()['total_ventas'];
$stmt_ventas->close();

// Recuento de productos en stock
$query_productos = "SELECT SUM(stock) as total_stock FROM productos";
$result_productos = $conexion->query($query_productos);
$productos_stock = $result_productos->fetch_assoc()['total_stock'] ?? 0;

// Recuento de clientes registrados
$query_clientes = "SELECT COUNT(*) as total_clientes FROM clientes";
$result_clientes = $conexion->query($query_clientes);
$clientes_registrados = $result_clientes->fetch_assoc()['total_clientes'] ?? 0;

$conexion->close();
?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Punto de Venta</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="panel.css">
    <style>
        .welcome-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background-color:hsl(210, 73.80%, 46.50%);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 9999; /* Asegura que el mensaje de bienvenida esté por encima del resto del contenido */
            animation: fadeOut 6s ease-out forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                display: none;
            }
        }

        .logo-animation {
            animation: fadeInLogo 2s ease-out;
        }

        @keyframes fadeInLogo {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .logo {
            max-width: 200px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
    </style>
</head>
<body>
    <!-- Bienvenida Animada -->
    <div class="welcome-container">
        <div class="logo-animation">
            <img src="images/1.jpg" alt="Farmacia El Bienestar" class="logo">
        </div>
        <h1>¡Bienvenido a Farmacia El Bienestar, <?php echo ucfirst($username); ?>! 👋</h1>
        <p>Nos complace tenerte con nosotros. ¡Gracias por ser parte de nuestro equipo!</p>
    </div>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Punto de Venta</h2>
            <ul>
                <?php if ($role == 'administrador'): ?>
                    <h3>ADMINISTRADOR</h3>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'estadisticas.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadísticas</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventas.php') ? 'active' : ''; ?>"><a href="ventas.php">Ventas</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'usuarios.php') ? 'active' : ''; ?>"><a href="usuarios.php">Usuarios</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventadetalle.php') ? 'active' : ''; ?>"><a href="ventadetalle.php">Detalle de Productos</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'configuracion.php') ? 'active' : ''; ?>"><a href="configuracion.php">Configuración de Tienda</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'clientes.php') ? 'active' : ''; ?>"><a href="clientes.php">Clientes</a></li>
                <?php elseif ($role == 'empleado'): ?>
                    <h3>EMPLEADO</h3>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'ventas.php') ? 'active' : ''; ?>"><a href="ventas.php">Caja</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'corte.php') ? 'active' : ''; ?>"><a href="corte.php">Corte</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'productos.php') ? 'active' : ''; ?>"><a href="productos.php">Productos</a></li>
                    <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'estadisticas.php') ? 'active' : ''; ?>"><a href="estadisticas.php">Estadísticas</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="main-content">
            <h1>Bienvenido, <?php echo ucfirst($username); ?> 👋</h1>
            <p>Rol: <strong><?php echo ucfirst($role); ?></strong></p>
            <div class="dashboard-info">
                <div class="card">
                    <h3>Ventas Hoy</h3>
                    <p><?php echo $ventas_hoy; ?> ventas</p>
                </div>
                <div class="card">
                    <h3>Productos en Stock</h3>
                    <p><?php echo $productos_stock; ?> productos</p>
                </div>
                <div class="card">
                    <h3>Clientes Registrados</h3>
                    <p><?php echo $clientes_registrados; ?> clientes</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
