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
                <a href="gestion_usuario.php" class="nav-link"><i class="bi bi-person"></i> Gestionar Usuarios</a>
            </li>
            <li class="nav-item">
                <a href="ver_clases.php" class="nav-link"><i class="bi bi-people"></i> Gestionar Clases</a>
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

            <button type="submit" name="editar_clase" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</body>
</html>
