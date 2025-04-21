<?php
// Suponemos que ya recibiste el ID de la asignatura por GET
$id_asignatura = $_GET['id_asignatura'] ?? 1;

// Aquí puedes traer datos de la asignatura desde la base de datos (nombre, etc.)
$nombre_asignatura = "Programación Orientada a Objetos"; // Ejemplo estático por ahora
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
            margin: 0;;
        }
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
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
    </style>

</head>
<body>
<div class="sidebar">
        <h3>App TFG</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="inicio_profesor.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <div class="container mt-4">
        <h2 class="mb-4 text-center"><?= $nombre_asignatura ?> - Panel del Profesor</h2>
        
        <div class="row g-4">
            <!-- Generar Actividad -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Generar Actividad</h5>
                        <p class="card-text">Crea una nueva actividad educativa con ayuda del asistente.</p>
                        <button class="btn btn-primary mt-auto">Generar</button>
                    </div>
                </div>
            </div>

            <!-- Lista de Errores Comunes -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Errores Comunes</h5>
                        <p class="card-text">Aquí puedes ver los errores comunes que pueden ocurrir en la actividad.</p>
                        <a href="errores_comunes.php?id_asignatura=<?= $id_asignatura ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <!-- Actividades -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Actividades</h5>
                        <p class="card-text">Consulta y gestiona todas las actividades asociadas a esta asignatura.</p>
                        <a href="actividades.php?id_asignatura=<?= $id_asignatura ?>" class="stretched-link"></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
