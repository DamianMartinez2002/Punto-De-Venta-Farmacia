<?php
// Conexión a la base de datos
include('db.php');

// Obtener todos los productos
$sql = "SELECT p.producto_id, p.producto_nombre, p.producto_precio, c.categoria_nombre 
        FROM productos p 
        JOIN categorias c ON p.categoria_id = c.categoria_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Productos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="table-container">
        <h2>Inventario de Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Producto</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['producto_id']; ?></td>
                        <td><?php echo $row['producto_nombre']; ?></td>
                        <td><?php echo "$" . number_format($row['producto_precio'], 2); ?></td>
                        <td><?php echo $row['categoria_nombre']; ?></td>
                        <td>
                            <a href="editar-producto.php?id=<?php echo $row['producto_id']; ?>">Editar</a> |
                            <a href="eliminar-producto.php?id=<?php echo $row['producto_id']; ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
