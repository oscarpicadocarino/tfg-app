<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

if (!isset($_GET['id_clase'])) {
    die("Clase no especificada.");
}

$id_clase = $_GET['id_clase'];

// Obtener información de la clase (nombre, asignatura, profesor)
$stmt = $pdo->prepare("
    SELECT c.nombre AS nombre_clase, 
           a.nombre AS nombre_asignatura, 
           u.nombre AS nombre_profesor, 
           u.correo AS correo_profesor
    FROM clases c
    JOIN asignaturas a ON c.id_asignatura = a.id_asignatura
    LEFT JOIN usuarios u ON c.id_profesor = u.id_usuario
    WHERE c.id_clase = :id_clase
");
$stmt->execute([':id_clase' => $id_clase]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    die("Clase no encontrada.");
}

// Obtener lista de alumnos asignados a la clase
$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.nombre, u.correo
    FROM alumnos_clases ac
    JOIN usuarios u ON ac.id_alumno = u.id_usuario
    WHERE ac.id_clase = :id_clase
");
$stmt->execute([':id_clase' => $id_clase]);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Clase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
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
        .table tbody tr {
            background-color: #ffffff;
        }
        thead.table-custom {
            background-color: rgb(97, 160, 255);
        }
</style>

<body>
    <div class="sidebar">
        <h3 class="mb-5 text-center fw-bold pb-2 border-bottom border-dark">Menú</h3>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="inicio_admin.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li class="nav-item"><a href="gestion_usuario.php" class="nav-link"><i class="bi bi-person-plus"></i> Gestionar Usuarios</a></li>
            <li class="nav-item"><a href="ver_clases.php" class="nav-link"><i class="bi bi-plus-circle"></i> Gestionar Clases</a></li>
            <li class="nav-item"><a href="asignar_alumnos.php" class="nav-link"><i class="bi bi-person-check"></i> Asignar Alumnos</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="container mt-5">
        <h1 class="mb-4">Detalle de la Clase</h1>

        <div class="mb-4">
            <p><strong>Nombre de la clase:</strong> <?= htmlspecialchars($clase['nombre_clase']) ?></p>
            <p><strong>Asignatura:</strong> <?= htmlspecialchars($clase['nombre_asignatura']) ?></p>
            <p><strong>Profesor:</strong> <?= $clase['nombre_profesor'] ? htmlspecialchars($clase['nombre_profesor']) . " ({$clase['correo_profesor']})" : "No asignado" ?></p>
        </div>

        <h3>Alumnos Asignados</h3>
        <?php if (count($alumnos) > 0): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-custom">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td><?= htmlspecialchars($alumno['nombre']) ?></td>
                            <td><?= htmlspecialchars($alumno['correo']) ?></td>
                            <td>
                                <a href="eliminar_alumno_clase.php?id_clase=<?= $id_clase ?>&id_alumno=<?= $alumno['id_usuario'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Seguro que quieres eliminar a este alumno de la clase?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay alumnos asignados a esta clase.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
