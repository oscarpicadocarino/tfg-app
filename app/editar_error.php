<?php
require 'conexion.php';

// Cambiar el parámetro 'id_error' a 'id' si estás usando 'id' en la URL
$id_error = $_GET['id'] ?? null;
if (!$id_error) {
    die("ID de error no especificado.");
}

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'] ?? null;
    $tema = $_POST['tema'] ?? null;
    $id_asignatura = $_POST['id_asignatura'] ?? null;
    $id_clase = $_POST['id_clase'] ?? null;

    // Verificar si los campos requeridos están presentes
    if (!$descripcion || !$tema) {
        die("Faltan datos para actualizar.");
    }

    // Actualizamos el error común en la base de datos
    $stmt = $pdo->prepare("UPDATE errores_comunes SET descripcion = ?, tema = ? WHERE id_error = ?");
    $stmt->execute([$descripcion, $tema, $id_error]);

    // Redirigir después de la actualización
    header("Location: errores_comunes.php?id_asignatura=$id_asignatura&id_clase=$id_clase");
    exit;
}

// Aquí obtenemos la información del error usando el id_error
$stmt = $pdo->prepare("SELECT * FROM errores_comunes WHERE id_error = ?");
$stmt->execute([$id_error]);

$error = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$error) {
    die("Error no encontrado.");
}

$id_asignatura = $_GET['id_asignatura'] ?? null;
$id_clase = $_GET['id_clase'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Error Común</title>
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
        .table tbody tr {
            background-color: #ffffff;
        }
        thead.table-custom {
            background-color: rgb(97, 160, 255);
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
        <h1>Editar Error Común</h1>
        <form action="editar_error.php?id=<?= $id_error ?>&id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" method="POST">
            <input type="hidden" name="id_error" value="<?= htmlspecialchars($error['id_error']) ?>">
            <input type="hidden" name="id_asignatura" value="<?= htmlspecialchars($id_asignatura) ?>">
            <input type="hidden" name="id_clase" value="<?= htmlspecialchars($id_clase) ?>">
            
            <div class="mb-3">
                <label for="tema" class="form-label">Tema del Error</label>
                <input type="text" class="form-control" name="tema" id="tema" value="<?= htmlspecialchars($error['tema']) ?>">
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción del Error</label>
                <textarea class="form-control" name="descripcion" id="descripcion" rows="3"><?= htmlspecialchars($error['descripcion']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</body>
</html>
