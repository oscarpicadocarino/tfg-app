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
                <a href="inicio_admin.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a href="gestion_usuario.php" class="nav-link"><i class="bi bi-person-plus"></i> Gestionar Usuarios</a>
            </li>
            <li class="nav-item">
                <a href="ver_clases.php" class="nav-link"><i class="bi bi-plus-circle"></i> Gestionar Clases</a>
            </li>
            <li class="nav-item">
                <a href="asignar_alumnos.php" class="nav-link"><i class="bi bi-person-check"></i> Asignar Alumnos</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
            </li>
        </ul>
    </div>

    <div class="content">
    <h2 class="mb-5 fw-semibold">
    Hola <?= isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Administrador' ?>! 👋
    </h2>

    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-100">
                <div class="feature-box">
                    <i class="bi bi-person-plus"></i>
                    <h3>Gestionar Usuarios</h3>
                    <a href="gestion_usuario.php" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-100">
                <div class="feature-box">
                    <i class="bi bi-plus-circle"></i>
                    <h3>Crear Clase</h3>
                    <a href="ver_clases.php" class="stretched-link"></a>
                </div>
            </div>
            <div class="col-md-100">
                <div class="feature-box">
                    <i class="bi bi-person-check"></i>
                    <h3>Asignar Alumnos</h3>
                    <a href="asignar_alumnos.php" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
