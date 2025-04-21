<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clase = $_POST['id_clase'];
    $alumnos_seleccionados = $_POST['alumnos'] ?? [];

    foreach ($alumnos_seleccionados as $id_alumno) {
        // Evitar duplicados
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM alumnos_clases WHERE id_clase = ? AND id_alumno = ?");
        $stmt->execute([$id_clase, $id_alumno]);

        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO alumnos_clases (id_clase, id_alumno) VALUES (?, ?)");
            $insert->execute([$id_clase, $id_alumno]);
        }
    }

    echo "<div class='alert alert-success'>Alumnos asignados correctamente.</div>";
}

// Obtener clases y alumnos
$clases = $pdo->query("SELECT * FROM clases")->fetchAll(PDO::FETCH_ASSOC);
$alumnos = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'alumno'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Alumnos a Clase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Asignar Alumnos a una Clase</h2>

    <form method="POST">
        <div class="mb-3">
            <label for="id_clase" class="form-label">Clase</label>
            <select class="form-select" name="id_clase" id="id_clase" required>
                <option value="">Selecciona una clase</option>
                <?php foreach ($clases as $clase): ?>
                    <option value="<?= $clase['id_clase'] ?>"><?= $clase['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Selecciona alumnos:</label>
            <?php foreach ($alumnos as $alumno): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="alumnos[]" value="<?= $alumno['id_usuario'] ?>" id="alumno_<?= $alumno['id_usuario'] ?>">
                    <label class="form-check-label" for="alumno_<?= $alumno['id_usuario'] ?>">
                        <?= $alumno['nombre'] ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Asignar Alumnos</button>
    </form>
</div>
</body>
</html>
