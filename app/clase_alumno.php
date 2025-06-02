<?php

require_once 'conexion.php'; // Si tienes la conexión en un archivo separado

$id_clase = $_GET['id_clase'] ?? 1;

// Consultamos los datos de la clase y su asignatura asociada
$sql = "SELECT c.nombre, a.nombre AS nombre_asignatura, a.id_asignatura 
        FROM clases c 
        JOIN asignaturas a ON c.id_asignatura = a.id_asignatura 
        WHERE c.id_clase = :id_clase";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_clase' => $id_clase]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) {
    die("Clase no encontrada.");
}

$nombre_clase = $datos['nombre'];
$nombre_asignatura = $datos['nombre_asignatura'];
$id_asignatura = $datos['id_asignatura']; // Obtén el id_asignatura

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clase - Panel del Profesor</title>
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
        .card {
            border-radius: 1rem;
        }
        .card-title {
            font-weight: bold;
        }
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
        .feature-box {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #333;
            padding: 20px;
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        .feature-box i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #0d6efd;
        }
        .feature-box:hover {
            background-color: #f8f9fa;
            transform: translateY(-3px);
        }
        .feature-box h3 {
            font-size: 1.2rem;
            margin: 0;
        }
        .feature-box a {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
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
<h2 class="mb-5 fw-semibold">
    <?= htmlspecialchars($nombre_clase) ?>
</h2>

<div class="container">
    <div class="row g-4 mb-4">
        <div class="col-md-100">
            <div class="feature-box">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-plus-square"></i>
                    <i class="bi bi-robot"></i>
                </div>
                <h5 class="card-title">Chatbot</h5>
                <p class="card-text">Pregunta dudas sobre actividades y conceptos clave.</p>
                <a href="chat_alumno.php?id_clase=<?= $id_clase ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-100">
            <div class="feature-box">
                <i class="bi bi-list-ul"></i>
                <h5 class="card-title">Actividades</h5>
                <p class="card-text">Consulta las actividades pendientes.</p>
                <a href="asignatura_alumno.php?id_clase=<?= $id_clase ?>&id_asignatura=<?= $id_asignatura ?>" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
