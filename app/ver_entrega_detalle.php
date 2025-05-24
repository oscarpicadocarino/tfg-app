<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_entrega = $_GET['id'] ?? null;
if (!$id_entrega) {
    die("ID de entrega no especificado.");
}

// Obtener detalle de la entrega con datos del alumno y actividad
$stmt = $pdo->prepare("
    SELECT e.codigo, e.fecha_entrega, u.nombre, a.titulo, a.id_clase
    FROM entregas e
    JOIN usuarios u ON e.id_alumno = u.id_usuario
    JOIN actividad a ON e.id_actividad = a.id
    WHERE e.id = ?
");
$stmt->execute([$id_entrega]);
$entrega = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entrega) {
    die("Entrega no encontrada.");
}

// Obtener id_asignatura usando id_clase para el sidebar
$id_clase = $entrega['id_clase'] ?? null;
$id_asignatura = null;
if ($id_clase) {
    $stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_clase = ?");
    $stmtClase->execute([$id_clase]);
    $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
    $id_asignatura = $clase['id_asignatura'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Entrega - <?= htmlspecialchars($entrega['titulo']) ?></title>
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
        .container {
            padding: 20px;
            overflow-y: auto;
            flex-grow: 1;
            height: 100vh;
        }
        pre {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .btn-custom-blue {
            background-color: rgb(97, 160, 255);
            border-color: rgb(97, 160, 255);
            color: white;
        }

        .btn-custom-blue:hover {
            background-color: rgb(77, 140, 230); /* un azul un poco más oscuro al hacer hover */
            border-color: rgb(77, 140, 230);
            color: white;
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
                <a href="generar_actividad.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
            </li>
            <li class="nav-item">
                <a href="actividades.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
            </li>
            <li class="nav-item">
                <a href="errores_comunes.php?id_clase=<?= htmlspecialchars($id_clase) ?>&id_asignatura=<?= htmlspecialchars($id_asignatura) ?>" class="nav-link"><i class="bi bi-exclamation-circle"></i> Errores Comunes</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </li>
        </ul>
    </div>

    <div class="container mt-5">
        <h2>Entrega de <?= htmlspecialchars($entrega['nombre']) ?></h2>
        <h4>Actividad: <?= htmlspecialchars($entrega['titulo']) ?></h4>
        <p><strong>Fecha de entrega:</strong> <?= htmlspecialchars($entrega['fecha_entrega']) ?></p>

        <h5>Código entregado:</h5>
        <pre><?= htmlspecialchars($entrega['codigo']) ?></pre>

        <a href="ver_entregas.php?id=<?= urlencode($id_clase) ?>" class="btn btn-secondary mt-3" style="background-color: rgb(97, 160, 255); border-color: rgb(97, 160, 255);">
            Volver a entregas
        </a>
    </div>
</body>
</html>
