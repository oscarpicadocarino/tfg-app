<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es profesor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_asignatura = $_GET['id_asignatura'] ?? null;
$id_clase = $_GET['id_clase'] ?? null;  // Asegúrate de que también tomas el id_clase
if (!$id_asignatura) {
    die("ID de asignatura no especificado.");
}

$stmt = $pdo->prepare("SELECT * FROM errores_comunes WHERE id_asignatura = ?");
$stmt->execute([$id_asignatura]);
$errores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tema = trim($_POST['tema']);
    $descripcion = trim($_POST['descripcion']);

    if ($tema && $descripcion) {
        $stmt = $pdo->prepare("INSERT INTO errores_comunes (tema, descripcion, id_asignatura) VALUES (?, ?, ?)");

        if ($stmt->execute([$tema, $descripcion, $_GET['id_asignatura']])) {
            header("Location: errores_comunes.php?id_asignatura=" . $_GET['id_asignatura']);
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al añadir el error común.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning'>Por favor, rellena todos los campos.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Error Común</title>
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
            <a href="inicio_profesor.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a href="generar_actividad.php?id_clase=<?= $id_clase ?>&id_asignatura=<?= $id_asignatura ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
        </li>
        <li class="nav-item">
            <a href="actividades.php?id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
        </li>
        <li class="nav-item">
            <a href="errores_comunes.php?id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-exclamation-circle"></i> Errores Comunes</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="container mt-5">
    <h2>Añadir Nuevo Error Común</h2>
    <?= $mensaje ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="tema" class="form-label">Tema</label>
            <input type="text" name="tema" id="tema" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

</body>
</html>
