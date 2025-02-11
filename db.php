<?php
// Datos de la base de datos
$host = 'localhost';
$user = 'root';  // Tu usuario de la base de datos
$password = '';  // Tu contraseña de la base de datos
$db_name = 'puntoventa';  // Nombre de la base de datos

// Crear la conexión
$conexion= new mysqli($host, $user, $password, $db_name);

// Verificar si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
