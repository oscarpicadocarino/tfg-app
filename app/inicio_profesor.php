<?php
// inicio_profesor.php
session_start();

// Verifica si el usuario ha iniciado sesión y es profesor
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_profesor = $_SESSION['user_id'];

    // Consulta para obtener las clases que imparte el profesor, junto con la asignatura correspondiente
    $stmt = $pdo->prepare("
        SELECT 
            a.id_asignatura, 
            a.nombre AS nombre_asignatura, 
            a.descripcion AS descripcion_asignatura, 
            c.id_clase, 
            c.nombre AS nombre_clase
        FROM clases c
        JOIN asignaturas a ON c.id_asignatura = a.id_asignatura
        WHERE c.id_profesor = ?  -- Aquí filtras por el id del profesor
    ");
    $stmt->execute([$id_profesor]);

    // Verifica si el profesor tiene asignaturas o clases
    if ($stmt->rowCount() == 0) {
        echo "<div class='alert alert-warning'>No gestionas ninguna asignatura o clase todavía.</div>";
    } else {
        $asignaturas_clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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
    <title>Inicio Profesor - App TFG</title>
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
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .card-body {
            flex-grow: 1;
        }
        .feature-box i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #0d6efd;
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
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>
<div class="content">
    <h2 class="mb-5 fw-semibold">
        Hola <?= isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Profesor' ?>! 👋
    </h2>
    <div class="container">
        <div class="container-fluid">
            <div class="row g-4">
                <?php if (isset($asignaturas_clases) && count($asignaturas_clases) > 0): ?>
                    <?php foreach ($asignaturas_clases as $asignatura_clase): ?>
                        <div class="col-md-6 d-flex">
                            
                            <a href="clase_profesor.php?id_clase=<?= $asignatura_clase['id_clase'] ?>&id_asignatura=<?= $asignatura_clase['id_asignatura'] ?>" class="text-decoration-none text-dark w-100">
                                <div class="card p-4 shadow-sm border-0 w-100">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-journal-code me-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($asignatura_clase['nombre_asignatura']) ?></h4>
                                    </div>
                                    <div class="card-body px-0 pt-3 pb-0">
                                        <p class="mb-2"><?= htmlspecialchars($asignatura_clase['descripcion_asignatura']) ?></p>
                                        <div>
                                            <h5 class="mb-1">Clase:</h5>
                                            <ul class="mb-0">
                                                <li><?= htmlspecialchars($asignatura_clase['nombre_clase']) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            No gestionas ninguna asignatura o clase todavía.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
