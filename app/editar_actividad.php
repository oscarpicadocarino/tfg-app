<?php
// editar_actividad.php
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

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $estado = $_POST['estado'];

    // Actualizar la actividad
    $stmt = $pdo->prepare("UPDATE actividad SET titulo = ?, contenido = ?, estado = ? WHERE id = ?");
    $stmt->execute([$titulo, $contenido, $estado, $id]);

    // Redirigir a la página de actividades de la clase
    header("Location: actividades.php?id_clase=" . $actividad['id_clase']);
    exit; // Asegúrate de usar exit para evitar que el código continúe ejecutándose después de la redirección
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Editar Actividad</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($actividad['titulo']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea class="form-control" id="contenido" name="contenido" rows="5" required><?= htmlspecialchars($actividad['contenido']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado">
                <option value="borrador" <?= $actividad['estado'] === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                <option value="publicada" <?= $actividad['estado'] === 'publicada' ? 'selected' : '' ?>>Publicada</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="actividades.php?id_clase=<?= $actividad['id_clase'] ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
