<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Procesar creación de clase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_clase'])) {
        $nombre_clase = $_POST['nombre_clase'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_profesor = $_POST['id_profesor'];

        $sql = "INSERT INTO clases (nombre, id_asignatura, id_profesor) 
                VALUES (:nombre_clase, :id_asignatura, :id_profesor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_clase', $nombre_clase);
        $stmt->bindParam(':id_asignatura', $id_asignatura);
        $stmt->bindParam(':id_profesor', $id_profesor);
        $stmt->execute();

        // Redirigir para evitar reenvío del formulario al recargar
        header("Location: ver_clases.php");
        exit();
    }
}

// Obtener datos para el formulario
$asignaturas = $pdo->query("SELECT * FROM asignaturas")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'profesor'")->fetchAll(PDO::FETCH_ASSOC);

// Obtener clases existentes
$clases = $pdo->query("SELECT c.*, a.nombre AS asignatura, u.nombre AS profesor 
                       FROM clases c 
                       JOIN asignaturas a ON c.id_asignatura = a.id_asignatura 
                       JOIN usuarios u ON c.id_profesor = u.id_usuario")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Clase - App TFG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-5">
    <h2>Gestión de Clases</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Clase creada con éxito.</div>
    <?php endif; ?>

    <!-- Formulario para crear una clase -->
    <form method="POST" class="mb-5" id="formulario-crear-clase">
        <div class="mb-3">
            <label for="nombre_clase" class="form-label">Nombre de la clase</label>
            <input type="text" class="form-control" id="nombre_clase" name="nombre_clase" required>
        </div>

        <div class="mb-3">
            <label for="id_asignatura" class="form-label">Asignatura</label>
            <select class="form-select" id="id_asignatura" name="id_asignatura" required>
                <option value="">Seleccione asignatura</option>
                <?php foreach ($asignaturas as $asignatura): ?>
                    <option value="<?= $asignatura['id_asignatura'] ?>"><?= $asignatura['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_profesor" class="form-label">Profesor</label>
            <select class="form-select" id="id_profesor" name="id_profesor" required>
                <option value="">Seleccione profesor</option>
                <?php foreach ($profesores as $profesor): ?>
                    <option value="<?= $profesor['id_usuario'] ?>"><?= $profesor['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="crear_clase" class="btn btn-primary">Crear Clase</button>
    </form>
</div>

</body>
</html>
