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

    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            padding-top: 40px;
            border-right: 1px solid #ddd;
        }
        .content {
            flex-grow: 1;
            padding: 40px;
        }
        .nav-link {
            color: #333 !important;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        .nav-link:hover {
            background-color: #e0e0e0;
            border-radius: 5px;
        }
        .nav-link i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        </style>
</head>
<body>
<div class="sidebar">
    <h3 class="mb-5 text-center fw-bold pb-2 border-bottom border-dark">Menú</h3>
    <ul class="nav flex-column">
            <li class="nav-item">
                <a href="inicio_admin.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a href="gestion_usuario.php" class="nav-link"><i class="bi bi-person-plus"></i> Gestionar Usuarios</a>
            </li>
            <li class="nav-item">
                <a href="ver_clases.php" class="nav-link"><i class="bi bi-plus-circle"></i> Gestionar Clases</a>
            </li>
            <li class="nav-item">
                <a href="asignar_alumnos.php" class="nav-link"><i class="bi bi-person-check"></i> Asignar Alumnos</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </li>
        </ul>
</div>
<div class="container mt-5">
    <h2>Añadir Nueva Clase</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Clase creada con éxito.</div>
    <?php endif; ?>

    <!-- Formulario para crear una clase -->
    <form method="POST" class="mt-4" id="formulario-crear-clase">
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

        <button type="submit" name="crear_clase" class="btn btn-primary">Guardar Clase</button>
    </form>
</div>

</body>
</html>
