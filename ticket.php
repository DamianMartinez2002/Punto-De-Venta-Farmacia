<?php
include 'db.php';

$id_venta = $_GET['id_venta'];

$query = "SELECT v.id_venta, v.codigo_barras, v.cantidad, v.total, v.fecha, 
                 p.nombre AS producto, u.nombre_completo AS usuario 
          FROM venta v
          JOIN productos p ON v.id_producto = p.id_producto
          JOIN usuarios u ON v.id_usuario = u.id_usuario 
          WHERE v.id_venta = '$id_venta'";

$result = mysqli_query($conexion, $query);
$venta = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Venta - Farmacia</title>
    <link rel="stylesheet" href="ticket.css">
    <script>
        function imprimirTicket() {
            window.print();
        }

        window.onafterprint = function() {
            // Redirige al apartado de ventas después de la impresión
            window.location.href = 'ventas.php'; // Cambia 'venta.php' a la ruta correcta del apartado de ventas
        }
    </script>
</head>
<body>
    <div class="ticket-container">
        <header class="ticket-header">
            <h1>Farmacia El Bienestar</h1>
            <p class="ticket-date">Fecha: <?php echo $venta['fecha']; ?></p>
        </header>
        
        <section class="ticket-info">
            <h2>Ticket de Venta</h2>
            <table class="ticket-details">
                <tr>
                    <th>ID Venta</th>
                    <td><?php echo $venta['id_venta']; ?></td>
                </tr>
                <tr>
                    <th>Producto</th>
                    <td><?php echo $venta['producto']; ?></td>
                </tr>
                <tr>
                    <th>Cantidad</th>
                    <td><?php echo $venta['cantidad']; ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>$<?php echo number_format($venta['total'], 2); ?></td>
                </tr>
                <tr>
                    <th>Atendió</th>
                    <td><?php echo $venta['usuario']; ?></td>
                </tr>
            </table>
        </section>

        <footer class="ticket-footer">
            <button class="btn-print" onclick="imprimirTicket()">Imprimir Ticket</button>
        </footer>
    </div>
</body>
</html>
