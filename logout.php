<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Deshabilitar temporalmente la verificación de claves foráneas
mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=0");

// Eliminar primero los registros en detalle_venta (tabla hija)
mysqli_query($conexion, "DELETE FROM detalle_venta");

// Luego eliminar los registros en venta (tabla padre)
mysqli_query($conexion, "DELETE FROM venta");

// Volver a habilitar la verificación de claves foráneas
mysqli_query($conexion, "SET FOREIGN_KEY_CHECKS=1");

// Limpiar las estadísticas de ventas del usuario
unset($_SESSION['total_ventas']);
unset($_SESSION['producto_mas_vendido']);
unset($_SESSION['ventas_por_dia']);
unset($_SESSION['ultimo_acceso']);

// Destruir la sesión
session_unset();
session_destroy();

// Redirigir al usuario a la página de inicio de sesión
header("Location: index.php");
exit;
?>
