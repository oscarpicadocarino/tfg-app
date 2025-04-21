<?php
// inicio_alumno.php
session_start();

// Verifica si el usuario ha iniciado sesión y es alumno
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_alumno = $_SESSION['user_id'];

    // Consulta para obtener las clases del alumno con el nombre y descripción de la asignatura
    $stmt = $pdo->prepare("
        SELECT 
            c.id_clase, 
            a.nombre AS nombre_asignatura, 
            a.descripcion
        FROM clases c
        JOIN alumnos_clases ca ON c.id_clase = ca.id_clase
        JOIN asignaturas a ON c.id_asignatura = a.id_asignatura
        WHERE ca.id_alumno = ?
    ");
    $stmt->execute([$id_alumno]);
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Alumno - App TFG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .nav-link {
            color: black !important;
            font-size: 18px;
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
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>App TFG</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="inicio_alumno.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a href="chatbot.php" class="nav-link"><i class="bi bi-chat-dots"></i> ChatBot</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <div class="content">
        <h2 class="mb-4">Asignaturas Disponibles</h2>
        <div class="container-fluid">
            <div class="row g-4">
                <?php if (count($clases) > 0): ?>
                    <?php foreach ($clases as $clase): ?>
                        <div class="col-md-6">
                            <a href="ver_clase.php?id=<?= $clase['id_clase'] ?>" class="text-decoration-none text-dark">
                                <div class="card p-4 shadow-sm border-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-journal-bookmark me-3" style="font-size: 2.5rem;"></i>
                                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($clase['nombre_asignatura']) ?></h4>
                                    </div>
                                    <p class="mt-3"><?= htmlspecialchars($clase['descripcion']) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            No estás matriculado en ninguna clase todavía.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
