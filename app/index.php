<?php
$host = 'db';
$usuario = 'usuario'; 
$contrasena = 'password';
$nombre_bd = 'tfg_app_db';  

// Establecer la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $nombre_bd);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

echo "¡Conexión exitosa a la base de datos!";
?>
