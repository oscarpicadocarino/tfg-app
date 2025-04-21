<?php
// publicar_actividad.php
require 'conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de actividad no especificado.");
}

// Obtener el estado actual de la actividad
$stmt = $pdo->prepare("SELECT estado, id_clase FROM actividad WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actividad) {
    die("Actividad no encontrada.");
}

// Definir el nuevo estado
$new_estado = ($actividad['estado'] === 'publicada') ? 'borrador' : 'publicada';

// Actualizar el estado de la actividad
$stmt = $pdo->prepare("UPDATE actividad SET estado = ? WHERE id = ?");
$stmt->execute([$new_estado, $id]);

header("Location: actividades.php?id_clase=" . $actividad['id_clase']);
exit;
?>
