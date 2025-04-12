<?php
$conexion = new mysqli("db", "usuario", "password", "tfg_app_db");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

echo "<h1>¡Conexión exitosa a la base de datos!</h1>";
?>

