<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es admin
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Panel de Administración</h1>

        <div class="d-grid gap-3">
            <a href="crear_clase.php" class="btn btn-primary btn-lg">➕ Crear Nueva Clase</a>
            <a href="asignar_alumnos.php" class="btn btn-secondary btn-lg">👨‍🎓 Asignar Alumnos a Clases</a>
        </div>
    </div>
</body>
</html>
