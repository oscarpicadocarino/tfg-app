<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Obtener los datos del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuario no encontrado.");
    }

    // Si el usuario es un alumno, obtener las clases a las que está asignado
    $clases_usuario = [];
    if ($usuario['tipo_usuario'] === 'alumno') {
        $stmt = $pdo->prepare("
            SELECT c.id_clase, c.nombre AS clase_nombre
            FROM clases c
            JOIN alumnos_clases ac ON ac.id_clase = c.id_clase
            WHERE ac.id_alumno = :id_usuario
        ");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
        $clases_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Si el formulario es enviado, actualizamos los datos del usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $tipo_usuario = $_POST['tipo_usuario'];

        // Actualizar los datos del usuario
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nombre = :nombre, correo = :correo, tipo_usuario = :tipo_usuario
            WHERE id_usuario = :id_usuario
        ");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        // Confirmación de actualización
        echo "<script>alert('Datos del usuario actualizados correctamente.'); window.location.href = 'gestion_usuario.php';</script>";
        exit();
    }

    // Si se elimina de una clase
    if (isset($_GET['eliminar_clase']) && isset($_GET['id_clase'])) {
        $id_clase = $_GET['id_clase'];

        $stmt = $pdo->prepare("DELETE FROM alumnos_clases WHERE id_alumno = :id_usuario AND id_clase = :id_clase");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':id_clase', $id_clase);
        $stmt->execute();

        echo "<script>alert('Alumno eliminado de la clase correctamente.'); window.location.href = 'editar_usuario.php?id_usuario=" . $id_usuario . "';</script>";
        exit();
    }
} else {
    echo "No se ha especificado un usuario.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
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
        .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control, .form-select {
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
    }

    /* Botón de actualización */
    .btn-primary {
        border-radius: 0.5rem;
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
        background-color: #0d6efd;
        border: none;
    }

    .btn-primary:hover {
        background-color: #0a58ca;
    }

    /* Estilos para la lista de clases asignadas */
    .list-group-item {
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
        margin-bottom: 0.5rem;
    }

    .list-group-item:hover {
        background-color: #e9ecef;
    }

    .btn-danger {
        border-radius: 0.5rem;
        background-color: #d9534f;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c9302c;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
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
                <a href="ver_clase.php" class="nav-link"><i class="bi bi-plus-circle"></i> Crear Clase</a>
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
    <h1>Editar Usuario</h1>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
            <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
                <option value="alumno" <?= $usuario['tipo_usuario'] === 'alumno' ? 'selected' : '' ?>>Alumno</option>
                <option value="profesor" <?= $usuario['tipo_usuario'] === 'profesor' ? 'selected' : '' ?>>Profesor</option>
                <option value="admin" <?= $usuario['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>

    <?php if ($usuario['tipo_usuario'] === 'alumno'): ?>
        <h2 class="mt-5">Clases Asignadas</h2>
        <ul class="list-group">
            <?php foreach ($clases_usuario as $clase): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($clase['clase_nombre']) ?>
                    <a href="editar_usuario.php?id_usuario=<?= $id_usuario ?>&eliminar_clase=1&id_clase=<?= $clase['id_clase'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar al alumno de esta clase?')">Eliminar de Clase</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

</div>
</body>
</html>
