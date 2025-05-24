<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_actividad = $_GET['id'] ?? null;
if (!$id_actividad) {
    die("ID de actividad no especificado.");
}

// Obtener info de la actividad para mostrar título y obtener id_clase y id_asignatura para el menú
$stmtActividad = $pdo->prepare("SELECT titulo, id_clase FROM actividad WHERE id = ?");
$stmtActividad->execute([$id_actividad]);
$actividad = $stmtActividad->fetch(PDO::FETCH_ASSOC);

if (!$actividad) {
    die("Actividad no encontrada.");
}

$id_clase = $actividad['id_clase'];

// Obtener id_asignatura desde id_clase para el menú
$stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_clase = ?");
$stmtClase->execute([$id_clase]);
$clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
$id_asignatura = $clase['id_asignatura'] ?? null;

// Obtener entregas con datos del alumno
$stmtEntregas = $pdo->prepare("
    SELECT e.id, e.codigo, e.fecha_entrega, u.nombre
    FROM entregas e
    JOIN usuarios u ON e.id_alumno = u.id_usuario
    WHERE e.id_actividad = ?
    ORDER BY e.fecha_entrega DESC
");
$stmtEntregas->execute([$id_actividad]);
$entregas = $stmtEntregas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entregas - <?= htmlspecialchars($actividad['titulo']) ?></title>
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
    <h1 class="mb-4">Entregas de la Actividad: <?= htmlspecialchars($actividad['titulo']) ?></h1>

    <?php if (empty($entregas)): ?>
        <div class="alert alert-info">No hay entregas para esta actividad.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-custom">
                <tr>
                    <th>Alumno</th>
                    <th>Fecha de Entrega</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entregas as $entrega): ?>
                    <tr>
                        <td><?= htmlspecialchars($entrega['nombre']) ?></td>
                        <td><?= htmlspecialchars($entrega['fecha_entrega']) ?></td>
                        <td>
                            <a href="ver_entrega_detalle.php?id=<?= htmlspecialchars($entrega['id']) ?>" class="btn btn-sm btn-info">Ver</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
