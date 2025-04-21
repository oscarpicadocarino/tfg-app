<?php
session_start();

require 'conexion.php';

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_clase'])) {
    header("Location: crear_clase.php?error=1");
    exit();
}

$id_clase = $_GET['id_clase'];

// Primero elimina las relaciones en alumnos_clases
$sql1 = "DELETE FROM alumnos_clases WHERE id_clase = :id_clase";
$stmt1 = $pdo->prepare($sql1);
$stmt1->bindParam(':id_clase', $id_clase, PDO::PARAM_INT);
$stmt1->execute();

// Luego elimina la clase
$sql2 = "DELETE FROM clases WHERE id_clase = :id_clase";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':id_clase', $id_clase, PDO::PARAM_INT);
$stmt2->execute();

header("Location: ver_clases.php");
exit();
