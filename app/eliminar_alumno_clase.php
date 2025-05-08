<?php
session_start();
require 'conexion.php';

if (!isset($_GET['id_clase']) || !isset($_GET['id_alumno'])) {
    die("Parámetros incompletos.");
}

$id_clase = $_GET['id_clase'];
$id_alumno = $_GET['id_alumno'];

try {
    $stmt = $pdo->prepare("DELETE FROM alumnos_clases WHERE id_clase = :id_clase AND id_alumno = :id_alumno");
    $stmt->execute([':id_clase' => $id_clase, ':id_alumno' => $id_alumno]);
    header("Location: detalle_clase.php?id_clase=$id_clase");
    exit();
} catch (PDOException $e) {
    die("Error al eliminar alumno: " . $e->getMessage());
}
