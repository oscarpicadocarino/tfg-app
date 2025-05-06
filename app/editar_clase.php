<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Procesar la actualización de una clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_clase'])) {
    // Obtener los datos del formulario
    $id_clase = $_POST['id_clase'];
    $nombre_clase = $_POST['nombre_clase'];
    $id_profesor = $_POST['id_profesor'];

    // Actualizar la clase en la base de datos
    $sql = "UPDATE clases SET nombre = :nombre_clase, id_profesor = :id_profesor WHERE id_clase = :id_clase";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre_clase', $nombre_clase);
    $stmt->bindParam(':id_profesor', $id_profesor);
    $stmt->bindParam(':id_clase', $id_clase);
    $stmt->execute();
    
    header("Location: ver_clases.php");
    exit();
}

// Obtener asignaturas y profesores para mostrarlos en el formulario
$asignaturas = $pdo->query("SELECT * FROM asignaturas")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'profesor'")->fetchAll(PDO::FETCH_ASSOC);

// Obtener la clase a editar si se seleccionó
if (isset($_GET['id_clase'])) {
    $id_clase = $_GET['id_clase'];
    $clase = $pdo->prepare("SELECT * FROM clases WHERE id_clase = :id_clase");
    $clase->bindParam(':id_clase', $id_clase);
    $clase->execute();
    $clase = $clase->fetch(PDO::FETCH_ASSOC);
    
    // Obtener el nombre de la asignatura correspondiente al id_asignatura
    $id_asignatura = $clase['id_asignatura'];
    $asignatura = $pdo->prepare("SELECT nombre FROM asignaturas WHERE id_asignatura = :id_asignatura");
    $asignatura->bindParam(':id_asignatura', $id_asignatura);
    $asignatura->execute();
    $asignatura = $asignatura->fetch(PDO::FETCH_ASSOC);
} else {
    // Si no se pasa un id de clase, redirigir al inicio
    header("Location: inicio_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Clase - App TFG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Clase</h2>

        <!-- Formulario para editar una clase -->
        <form method="POST">
            <input type="hidden" name="id_clase" value="<?= $clase['id_clase'] ?>">

            <div class="mb-3">
                <label for="nombre_clase" class="form-label">Nombre de la clase</label>
                <input type="text" class="form-control" id="nombre_clase" name="nombre_clase" value="<?= $clase['nombre'] ?>" required>
            </div>

            <div class="mb-3">
                <label for="id_asignatura" class="form-label">Asignatura</label>
                <select class="form-select" id="id_asignatura" name="id_asignatura" required disabled>
                    <option value="<?= $clase['id_asignatura'] ?>"><?= $asignatura['nombre'] ?></option>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_profesor" class="form-label">Profesor</label>
                <select class="form-select" id="id_profesor" name="id_profesor" required>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?= $profesor['id_usuario'] ?>" <?= $profesor['id_usuario'] === $clase['id_profesor'] ? 'selected' : '' ?>><?= $profesor['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="editar_clase" class="btn btn-success">Actualizar Clase</button>
        </form>
    </div>
</body>
</html>
