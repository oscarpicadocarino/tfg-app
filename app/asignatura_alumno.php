<?php
// ver_clase.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_clase = $_GET['id_clase'] ?? null;

if (!$id_clase) {
    echo "Clase no especificada.";
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener información de la clase y asignatura
    $stmt = $pdo->prepare("
        SELECT c.nombre AS nombre_clase, a.nombre AS nombre_asignatura
        FROM clases c
        JOIN asignaturas a ON c.id_asignatura = a.id_asignatura
        WHERE c.id_clase = ?
    ");
    $stmt->execute([$id_clase]);
    $clase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$clase) {
        echo "Clase no encontrada.";
        exit();
    }

    // Obtener actividades publicadas de esa clase
    $stmt = $pdo->prepare("
        SELECT id, titulo, estado, fecha_creacion 
        FROM actividad 
        WHERE id_clase = ? AND estado = 'publicada'
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute([$id_clase]);
    $actividad = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($clase['nombre_asignatura']) ?> - Actividades</title>
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
            <a href="inicio_alumno.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a href="chatbot.php" class="nav-link"><i class="bi bi-chat-dots"></i> ChatBot</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="content">
    <h1 class="mb-4" ><?= htmlspecialchars($clase['nombre_asignatura']) ?> - <?= htmlspecialchars($clase['nombre_clase']) ?></h1>

    <div class="container mt-5">
        <?php if (count($actividad) > 0): ?>
            <table class="table table-bordered table-hover">
                <thead class="table-custom">
                    <tr>
                        <th>Título</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($actividad as $act): ?>
                        <tr>
                            <td><?= htmlspecialchars($act['titulo']) ?></td>
                            <td>
                                <a href="ver_actividad_alumno.php?id=<?= $act['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info mt-4" role="alert">
                No hay actividades publicadas para esta clase todavía.
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
