<?php
session_start();
include('db.php');
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'empleado';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['eliminar'])) {
        // Eliminar producto
        $id_producto = mysqli_real_escape_string($conexion, $_POST['id_producto']);
        
        // Primero, eliminar las ventas asociadas a este producto
        $sql_delete_ventas = "DELETE FROM venta WHERE id_producto = '$id_producto'"; 
        if ($conexion->query($sql_delete_ventas) === TRUE) {
            // Ahora eliminar el producto
            $sql_delete = "DELETE FROM productos WHERE id_producto = '$id_producto'";

            if ($conexion->query($sql_delete) === TRUE) {
                header('Location: productos.php');
                exit();
            } else {
                echo "<p>Error al eliminar el producto: " . $conexion->error . "</p>";
            }
        } else {
            echo "<p>Error al eliminar las ventas asociadas: " . $conexion->error . "</p>";
        }
    } else {
        // Registrar producto
        $codigo_barras = mysqli_real_escape_string($conexion, $_POST['codigo_barras']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $descripcion = mysqli_real_escape_string($conexion, $_POST['caducidad']);
        $precio = mysqli_real_escape_string($conexion, $_POST['precio']);
        $cantidad = mysqli_real_escape_string($conexion, $_POST['stock']);
        $categoria = mysqli_real_escape_string($conexion, $_POST['lote']);
        $laboratorio = mysqli_real_escape_string($conexion, $_POST['laboratorio']);

        $sql = "INSERT INTO productos (codigo_barras, nombre, descripcion, precio, stock, categoria, laboratorio) 
                VALUES ('$codigo_barras', '$nombre', '$descripcion', '$precio', '$cantidad', '$categoria', '$laboratorio')";
        

        if ($conexion->query($sql) === TRUE) {
            header('Location: productos.php');
            exit();
        } else {
            echo "<p>Error: " . $conexion->error . "</p>";
        }
    }
}

$sql = "SELECT * FROM productos";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="productos.css">
    <style>
        /* Estilos para el botón de eliminar */
        .delete-btn {
            background-color: #f44336; /* Rojo para eliminar */
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: #d32f2f; /* Rojo más oscuro al pasar el mouse */
        }
    </style>
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
                <li class="active"><a href="productos.php">Productos</a></li>
                <li><a href="ventadetalle.php">Detalle de Productos</a></li>
                <li><a href="configuracion.php">Configuración</a></li>
                <li><a href="clientes.php">Clientes</a></li>
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
    <h2>Registrar Producto</h2>
    <form action="productos.php" method="POST">
        <label>Código de Barras:</label>
        <input type="text" name="codigo_barras" required>

        <label>Medicamento:</label>
        <input type="text" name="nombre" required>

        <label>Caducidad:</label>
        <input type="text" name="descripcion" required>

        <label>Precio:</label>
        <input type="number" step="0.01" name="precio" required>

        <label>Stock:</label>
        <input type="number" name="stock" required>

        <label>Lote:</label>
        <input type="text" name="categoria" required>

        <label>Laboratorio:</label>  
        <input type="text" name="laboratorio" required>  

        <label>Doctor:</label>  
        <input type="text" name="nombre_doctor" required> <!-- Nuevo campo -->

        <button type="submit">Registrar</button>
    </form>

    <h2>Lista de Productos</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código de Barras</th>
                <th>Medicamento</th>
                <th>Caducidad</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Lote</th>
                <th>Laboratorio</th>
                <th>Doctor</th> <!-- Nueva columna -->
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_producto']; ?></td>
                    <td><?php echo htmlspecialchars($row['codigo_barras']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['caducidad']); ?></td>
                    <td><?php echo $row['precio']; ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo htmlspecialchars($row['lote']); ?></td>
                    <td><?php echo htmlspecialchars($row['laboratorio']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_doctor']); ?></td> <!-- Nueva celda -->

                    <td>
                        <form action="productos.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
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
