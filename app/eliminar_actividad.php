<?php
// eliminar_actividad.php
require 'conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de actividad no especificado.");
}

// Eliminar actividad
$stmt = $pdo->prepare("DELETE FROM actividad WHERE id = ?");
$stmt->execute([$id]);

header("Location: actividades.php?id_asignatura=" . $actividad['id_asignatura']);
exit;
?>
