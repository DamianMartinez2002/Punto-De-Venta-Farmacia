<?php
include('db.php');

if (isset($_GET['id'])) {
    $producto_id = $_GET['id'];

    // Eliminar el producto de la base de datos
    $sql = "DELETE FROM productos WHERE producto_id = '$producto_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Producto eliminado correctamente.";
        header("Location: inventario.php");  // Redirigir a la página de inventario
        exit();
    } else {
        echo "Error al eliminar el producto: " . $conn->error;
    }
}
?>
