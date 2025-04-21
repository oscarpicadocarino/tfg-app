<?php
// ver_actividad.php
require 'conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de actividad no especificado.");
}

// Obtener actividad
$stmt = $pdo->prepare("SELECT * FROM actividad WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actividad) {
    die("Actividad no encontrada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Detalles de la Actividad</h3>
    <div class="mb-3">
        <strong>Título:</strong> <?= htmlspecialchars($actividad['titulo']) ?>
    </div>
    <div class="mb-3">
        <strong>Contenido:</strong> <p><?= nl2br(htmlspecialchars($actividad['contenido'])) ?></p>
    </div>
    <a href="asignatura_alumno.php?id=<?= $actividad['id_clase'] ?>" class="btn btn-primary">Volver</a>
</div>

</body>
</html>
