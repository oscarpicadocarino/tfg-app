<?php
session_start();

require 'conexion.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

// Verificar que se haya pasado el ID del error
if (!isset($_GET['eliminar'])) {
    header("Location: errores_comunes.php?error=1");
    exit();
}

$id_error = $_GET['eliminar'];
$id_asignatura = $_GET['id_asignatura'] ?? null;
$id_clase = $_GET['id_clase'] ?? null;

// Primero elimina las relaciones con el error en la base de datos (si las hay)
$sql1 = "DELETE FROM errores_comunes WHERE id_error = :id_error";
$stmt1 = $pdo->prepare($sql1);
$stmt1->bindParam(':id_error', $id_error, PDO::PARAM_INT);
$stmt1->execute();

// Redirige a la página de errores comunes después de eliminar el error
header("Location: errores_comunes.php?id_asignatura=$id_asignatura&id_clase=$id_clase");
exit();
?>
