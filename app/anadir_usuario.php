<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contraseña']);
    $tipo_usuario = $_POST['tipo_usuario'];

    if ($nombre && $correo && $contraseña && $tipo_usuario) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contraseña, tipo_usuario, fecha_creacion) VALUES (?, ?, ?, ?, NOW())");

        if ($stmt->execute([$nombre, $correo, $contraseña, $tipo_usuario])) {
            header("Location: gestion_usuario.php");
            exit();
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al añadir el usuario.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-warning'>Por favor, rellena todos los campos.</div>";
    }
}
 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Usuario</title>
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

    <div class="container mt-5">
        <h2>Añadir Nuevo Usuario</h2>
        <?= $mensaje ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="text" name="contraseña" id="contraseña" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tipo_usuario" class="form-label">Tipo de usuario</label>
                <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
                    <option value="">Selecciona un tipo</option>
                    <option value="admin">Administrador</option>
                    <option value="profesor">Profesor</option>
                    <option value="alumno">Alumno</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        </form>
    </div>
</body>
</html>
