<?php
require 'conexion.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de actividad no especificado.");
}

// Obtener datos de la actividad
$stmt = $pdo->prepare("SELECT * FROM actividad WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actividad) {
    die("Actividad no encontrada.");
}

$id_clase = $actividad['id_clase'];

// Obtener id_asignatura desde la clase
$stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_asignatura = ?");
$stmtClase->execute([$id_clase]);
$clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
$id_asignatura = $clase['id_asignatura'] ?? null;

if (!$actividad) {
    die("Actividad no encontrada.");
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $estado = $actividad['estado'];

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
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
        .main-content {
            flex-grow: 1;
            padding: 40px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 0;
            font-size: 1.3rem;
            font-weight: bold;
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
            <a href="generar_actividad.php?id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
        </li>
        <li class="nav-item">
            <a href="actividades.php?id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
        </li>
        <li class="nav-item">
            <a href="errores_comunes.php?id_asignatura=<?= $id_asignatura ?>" class="nav-link"><i class="bi bi-exclamation-circle"></i> Errores Comunes</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>


<div class="main-content container">
<h2 class="mb-4">Editar Actividad</h2>

    <!-- Formulario para editar la actividad -->
    <form method="POST">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3"><strong>Título:</strong> 
                    <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($actividad['titulo']) ?>" required>
                </h5>
                <p><strong>Contenido:</strong></p>
                <textarea class="form-control" id="contenido" name="contenido" rows="5" required><?= htmlspecialchars($actividad['contenido']) ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>

</body>
</html>
