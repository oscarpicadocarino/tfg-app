<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
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
        .feature-box {
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            position: relative;
        }
        .feature-box i {
            font-size: 3rem;
            margin-bottom: 10px; /* Espacio entre el icono y el texto */
        }
        .feature-box a {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Panel Admin</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="inicio_admin.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a href="crear_clase.php" class="nav-link"><i class="bi bi-plus-circle"></i> Crear Clase</a>
            </li>
            <li class="nav-item">
                <a href="asignar_alumnos.php" class="nav-link"><i class="bi bi-person-check"></i> Asignar Alumnos</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </li>
        </ul>
    </div>

    <div class="content">
        <h1 class="mb-4">Panel de Administración</h1>

        <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="feature-box bg-success text-white">
                    <i class="bi bi-person-plus"></i>Añadir
                    <h3>Gestion Usuarios</h3>
                    <a href="gestion_usuario.php" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="feature-box bg-primary text-white">
                    <i class="bi bi-plus-circle"></i>
                    <h3>Crear Nueva Clase</h3>
                    <a href="crear_clase.php" class="stretched-link"></a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-box bg-secondary text-white">
                    <i class="bi bi-person-check"></i>
                    <h3>Asignar Alumnos a Clases</h3>
                    <a href="asignar_alumnos.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
