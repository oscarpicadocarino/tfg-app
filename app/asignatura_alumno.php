<?php
// ver_clase.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_clase = $_GET['id'] ?? null;

if (!$id_clase) {
    echo "Clase no especificada.";
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener información de la clase y asignatura
    $stmt = $pdo->prepare("
        SELECT c.nombre AS nombre_clase, a.nombre AS nombre_asignatura
        FROM clases c
        JOIN asignaturas a ON c.id_asignatura = a.id_asignatura
        WHERE c.id_clase = ?
    ");
    $stmt->execute([$id_clase]);
    $clase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clase) {
        echo "Clase no encontrada.";
        exit();
    }

    // Obtener actividades publicadas de esa clase
    $stmt = $pdo->prepare("
        SELECT id, titulo, contenido, fecha_creacion 
        FROM actividad 
        WHERE id_clase = ? AND estado = 'publicada'
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute([$id_clase]);
    $actividad = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($clase['nombre_asignatura']) ?> - actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2><?= htmlspecialchars($clase['nombre_asignatura']) ?> - <?= htmlspecialchars($clase['nombre_clase']) ?></h2>
    <hr>
    <?php if (count($actividad) > 0): ?>
        <?php foreach ($actividad as $act): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($act['titulo']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($act['contenido'])) ?></p>
                    <small class="text-muted">Publicado el <?= $act['fecha_creacion'] ?></small>
                    <a href="ver_actividad_alumno.php?id=<?= $act['id'] ?>" class="btn btn-primary mt-2">Ver</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            No hay actividades publicadas para esta clase todavía.
        </div>
    <?php endif; ?>

    <a href="inicio_alumno.php" class="btn btn-secondary mt-3">Volver</a>
</div>
</body>
</html>
