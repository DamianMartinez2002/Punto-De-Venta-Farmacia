<?php
session_start();

// Verificar si el rol está definido en la sesión
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $id_usuario = $_SESSION['id_usuario'];
} else {
    $role = 'invitado';
    $id_usuario = null;
}

include 'db.php'; // Incluir archivo de conexión

// Verificar conexión a la base de datos
if (!isset($conexion)) {
    die("Error: La conexión a la base de datos no está definida.");
}

// Procesar la venta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar los datos
    $codigo_barras = mysqli_real_escape_string($conexion, $_POST['codigo_barras']);
    $cantidad = mysqli_real_escape_string($conexion, $_POST['cantidad']);
    
    // Buscar el producto con el código de barras
    $producto_query = "SELECT id_producto, nombre, precio, stock FROM productos WHERE codigo_barras = '$codigo_barras'";
    $producto_result = mysqli_query($conexion, $producto_query);
    
    if (mysqli_num_rows($producto_result) > 0) {
        $producto = mysqli_fetch_assoc($producto_result);
        $id_producto = $producto['id_producto'];
        $nombre_producto = $producto['nombre'];
        $precio_unitario = $producto['precio'];
        $stock = $producto['stock'];
        
        // Verificar si hay suficiente stock
        if ($stock >= $cantidad) {
            // Calcular el total
            $total = $cantidad * $precio_unitario;
            $fecha = date('Y-m-d H:i:s');

            // Insertar la venta
            $query = "INSERT INTO venta (codigo_barras, id_producto, cantidad, total, fecha, id_usuario)
                      VALUES ('$codigo_barras', '$id_producto', '$cantidad', '$total', '$fecha', '$id_usuario')";
            if (mysqli_query($conexion, $query)) {
                // Obtener el ID de la venta recién insertada
                $id_venta = mysqli_insert_id($conexion);

                // Insertar en detalle_venta
                $detalle_query = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, total)
                                  VALUES ('$id_venta', '$id_producto', '$cantidad', '$total')";
                if (mysqli_query($conexion, $detalle_query)) {
                    // Actualizar el stock del producto
                    $nuevo_stock = $stock - $cantidad;
                    $update_stock_query = "UPDATE productos SET stock = '$nuevo_stock' WHERE id_producto = '$id_producto'";
                    if (mysqli_query($conexion, $update_stock_query)) {
                        // Registrar la venta en la tabla cortes
                        $fecha_corte = date('Y-m-d');
                        $corte_query = "SELECT id_corte, total_vendido FROM cortes WHERE fecha = '$fecha_corte' AND id_usuario = '$id_usuario'";
                        $corte_result = mysqli_query($conexion, $corte_query);

                        if (mysqli_num_rows($corte_result) > 0) {
                            $corte = mysqli_fetch_assoc($corte_result);
                            $nuevo_total = $corte['total_vendido'] + $total;
                            $update_corte_query = "UPDATE cortes SET total_vendido = '$nuevo_total' WHERE id_corte = '{$corte['id_corte']}'";
                            mysqli_query($conexion, $update_corte_query);
                        } else {
                            $insert_corte_query = "INSERT INTO cortes (fecha, total_vendido, id_usuario) VALUES ('$fecha_corte', '$total', '$id_usuario')";
                            mysqli_query($conexion, $insert_corte_query);
                        }

                        // Redirigir al ticket
                        header("Location: ticket.php?id_venta=$id_venta");
                        exit;
                    } else {
                        $mensaje = "Error al actualizar el stock: " . mysqli_error($conexion);
                    }
                } else {
                    $mensaje = "Error al registrar el detalle de la venta: " . mysqli_error($conexion);
                }
            } else {
                $mensaje = "Error al registrar la venta: " . mysqli_error($conexion);
            }
        } else {
            $mensaje = "No hay suficiente stock para realizar esta venta.";
        }
    } else {
        $mensaje = "Producto no encontrado con ese código de barras.";
    }
}

// Obtener los productos disponibles
$productos_query = "SELECT id_producto, nombre, codigo_barras, precio FROM productos";
$productos_result = mysqli_query($conexion, $productos_query);
$productos = [];
if ($productos_result) {
    while ($row = mysqli_fetch_assoc($productos_result)) {
        $productos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Farmacia</title>
    <link rel="stylesheet" href="ventas.css">
    <script>
        function imprimirTicket() {
            window.print();
        }
    </script>
</head>
<body>
    
    <!-- Menú Lateral -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="images/1.jpg" alt="Logo de Farmacia El Bienestar" class="logo">
        </div>
        <ul>
    <?php if ($role == 'administrador'): ?>
        <h3>ADMINISTRADOR</h3>
        <li><a href="estadisticas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'estadisticas.php' ? 'active' : ''; ?>">Estadísticas</a></li>
        <li><a href="ventas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ventas.php' ? 'active' : ''; ?>">Ventas</a></li>
        <li><a href="usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">Usuarios</a></li>
        <li><a href="productos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>">Productos</a></li>
        <li><a href="ventadetalle.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ventadetalle.php' ? 'active' : ''; ?>">Detalle de Productos</a></li>
        <li><a href="configuracion.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active' : ''; ?>">Configuración de Tienda</a></li>
        <li><a href="clientes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : ''; ?>">Clientes</a></li>
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

    <!-- Contenedor de Ventas -->
    <div class="container">
        <h1>Registrar Venta</h1>

        <?php if (isset($mensaje)): ?>
    <p class="mensaje <?php echo (strpos($mensaje, 'Error') === false) ? 'mensaje-exito' : 'mensaje-error'; ?>">
        <?php echo $mensaje; ?>
    </p>
<?php endif; ?>


        <form action="ventas.php" method="POST">
            <label for="codigo_barras">Código de Barras:</label>
            <input type="text" name="codigo_barras" id="codigo_barras" required>

            <label for="nombre_producto">Producto:</label>
            <input type="text" name="nombre_producto" id="nombre_producto" required readonly>

            <label for="cantidad">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" required min="1">

            <label for="precio">Precio :</label>
            <input type="text" name="precio" id="precio" required readonly>

            <label for="total">Total:</label>
            <input type="text" name="total" id="total" required readonly>

            <button type="submit">Registrar Venta</button>
        </form>
    </div>

    <script>
    // Script para autocompletar el nombre del producto cuando se ingresa el código de barras
    document.getElementById('codigo_barras').addEventListener('input', function() {
        var codigoBarras = this.value; // Obtener el código de barras ingresado
        var productos = <?php echo json_encode($productos); ?>; // Traemos todos los productos desde PHP
        var producto = productos.find(function(p) {
            return p.codigo_barras === codigoBarras; // Buscamos el producto por código de barras
        });

        // Si encontramos el producto con el código de barras ingresado
        if (producto) {
            document.getElementById('nombre_producto').value = producto.nombre; // Autocompletar el nombre del producto
            document.getElementById('precio').value = producto.precio; // Completar el precio si se encuentra el producto
            document.getElementById('total').value = ''; // Limpiar el total hasta que se ingrese la cantidad
        } else {
            // Si no encontramos el producto, vaciar los campos
            document.getElementById('nombre_producto').value = '';
            document.getElementById('precio').value = '';
            document.getElementById('total').value = '';
        }
    });

    // Script para autocompletar el precio y calcular el total cuando se ingresa la cantidad
    document.getElementById('cantidad').addEventListener('input', function() {
        var cantidad = parseInt(this.value) || 0; // Obtener la cantidad ingresada
        var precio = parseFloat(document.getElementById('precio').value) || 0; // Obtener el precio del producto
        if (precio > 0) {
            document.getElementById('total').value = cantidad * precio; // Calcular y mostrar el total
        } else {
            document.getElementById('total').value = ''; // Si no hay precio, dejar el total vacío
        }
    });

    // Script para hacer desaparecer el mensaje después de 3 segundos
window.onload = function() {
    // Verificar si hay un mensaje
    var mensaje = document.querySelector('.mensaje');
    if (mensaje) {
        setTimeout(function() {
            mensaje.style.display = 'none'; // Ocultar el mensaje después de 3 segundos
        }, 3000); // 3000 milisegundos = 3 segundos
    }
};
</script>

</body>
</html>
