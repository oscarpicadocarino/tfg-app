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
            header("Location: inicio_admin.php");
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
</head>
<body>
    <div class="container mt-5">
        <h2>Añadir Nuevo Usuario</h2>
        <?= $mensaje ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo electrónico</label>
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
            <a href="inicio_admin.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>
</html>
