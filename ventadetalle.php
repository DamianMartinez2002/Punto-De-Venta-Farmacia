<?php
session_start();
include('db.php');
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'empleado';

// Verificar si se ha enviado un formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar'])) {
    $id_producto = $_POST['id_producto'];
    $codigo_barras = $_POST['codigo_barras'];
    $medicamento = $_POST['medicamento'];
    $caducidad = $_POST['caducidad'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $lote = $_POST['lote'];
    $laboratorio = $_POST['laboratorio'];
    $nombre_doctor = $_POST['nombre_doctor']; // Nuevo campo

    $update_sql = "UPDATE productos SET 
                    codigo_barras = ?, nombre = ?, caducidad = ?, 
                    precio = ?, stock = ?, lote = ?, laboratorio = ?, nombre_doctor = ?
                   WHERE id_producto = ?";
    $stmt = $conexion->prepare($update_sql);
    $stmt->bind_param("ssssdisis", $codigo_barras, $medicamento, $caducidad, $precio, $stock, $lote, $laboratorio, $nombre_doctor, $id_producto);

    if ($stmt->execute()) {
        echo "<script>alert('Producto actualizado correctamente'); window.location.href='detalle_productos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el producto');</script>";
    }
    $stmt->close();
}

// Verificar si se ha solicitado la eliminación de un producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $id_producto = $_POST['id_producto'];
    $delete_sql = "DELETE FROM productos WHERE id_producto = ?";
    $stmt = $conexion->prepare($delete_sql);
    $stmt->bind_param("i", $id_producto);
    
    if ($stmt->execute()) {
        echo "<script>alert('Producto eliminado correctamente'); window.location.href='detalle_productos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el producto');</script>";
    }
    $stmt->close();
}

// Consulta para obtener los productos registrados
$sql = "SELECT id_producto, codigo_barras, nombre AS medicamento, caducidad, precio, stock, lote, laboratorio, nombre_doctor 
        FROM productos ORDER BY nombre ASC";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Productos</title>
    <link rel="stylesheet" href="ventadetalle.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo-container">
            <img src="images/1.jpg" alt="Logo de Farmacia El Bienestar" class="logo">
        </div>
        <ul>
            <?php if ($role == 'administrador'): ?>
                <h3> ADMINISTRADOR</h3>
                <li><a href="estadisticas.php">Estadísticas</a></li>
                <li><a href="ventas.php">Ventas</a></li>
                <li><a href="usuarios.php">Usuarios</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li class="active"><a href="detalle_productos.php">Detalle de Productos</a></li>
                <li><a href="configuracion.php">Configuración</a></li>
                <li><a href="clientes.php">Clientes</a></li>
            <?php elseif ($role == 'empleado'): ?>
                <h3>EMPLEADO</h3>
                <li class="active"><a href="detalle_productos.php">Detalle de Productos</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="logout">Cerrar sesión</a></li>
        </ul>
    </div>
    
    <div class="container">
        <h2>Detalle de Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código de Barras</th>
                    <th>Medicamento</th>
                    <th>Caducidad</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Lote</th>
                    <th>Laboratorio</th>
                    <th>Nombre del Doctor</th> <!-- Nueva columna -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form action="detalle_productos.php" method="POST">
                            <td><?php echo $row['id_producto']; ?></td>
                            <td><input type="text" name="codigo_barras" value="<?php echo htmlspecialchars($row['codigo_barras']); ?>"></td>
                            <td><input type="text" name="medicamento" value="<?php echo htmlspecialchars($row['medicamento']); ?>"></td>
                            <td><input type="date" name="caducidad" value="<?php echo $row['caducidad']; ?>"></td>
                            <td><input type="number" step="0.01" name="precio" value="<?php echo $row['precio']; ?>"></td>
                            <td><input type="number" name="stock" value="<?php echo $row['stock']; ?>"></td>
                            <td><input type="text" name="lote" value="<?php echo htmlspecialchars($row['lote']); ?>"></td>
                            <td><input type="text" name="laboratorio" value="<?php echo htmlspecialchars($row['laboratorio']); ?>"></td>
                            <td><input type="text" name="nombre_doctor" value="<?php echo htmlspecialchars($row['nombre_doctor']); ?>"></td> <!-- Campo nuevo -->
                            <td class="action-buttons">
                                <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                <button type="submit" name="editar" class="edit-btn">Guardar</button>
                            </form>
                            <form action="detalle_productos.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');" style="display:inline;">
                                <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                <button type="submit" name="eliminar" class="delete-btn">Eliminar</button>
                            </form>
                            </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
