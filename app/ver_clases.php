<?php
session_start();

// Verifica si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Procesar la creación de una nueva clase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear_clase'])) {
        // Obtener los datos del formulario
        $nombre_clase = $_POST['nombre_clase'];
        $id_asignatura = $_POST['id_asignatura'];
        $id_profesor = $_POST['id_profesor'];

        // Insertar nueva clase en la base de datos
        $sql = "INSERT INTO clases (nombre, id_asignatura, id_profesor) 
                VALUES (:nombre_clase, :id_asignatura, :id_profesor)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre_clase', $nombre_clase);
        $stmt->bindParam(':id_asignatura', $id_asignatura);
        $stmt->bindParam(':id_profesor', $id_profesor);
        $stmt->execute();
        
        echo "<div class='alert alert-success'>Clase creada con éxito.</div>";
    }

    }


// Obtener asignaturas y profesores para mostrarlos en el formulario
$asignaturas = $pdo->query("SELECT * FROM asignaturas")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'profesor'")->fetchAll(PDO::FETCH_ASSOC);

// Obtener la clase a editar si se seleccionó
if (isset($_GET['id_clase'])) {
    $id_clase = $_GET['id_clase'];
    $clase = $pdo->prepare("SELECT * FROM clases WHERE id_clase = :id_clase");
    $clase->bindParam(':id_clase', $id_clase);
    $clase->execute();
    $clase = $clase->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador - App TFG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestionar Clases</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre Clase</th>
                    <th>Asignatura</th>
                    <th>Profesor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $clases = $pdo->query("SELECT c.*, a.nombre AS asignatura, p.nombre AS profesor 
                                       FROM clases c 
                                       JOIN asignaturas a ON c.id_asignatura = a.id_asignatura 
                                       JOIN usuarios p ON c.id_profesor = p.id_usuario")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($clases as $clase):
                ?>
                    <tr>
                        <td><?= $clase['nombre'] ?></td>
                        <td><?= $clase['asignatura'] ?></td>
                        <td><?= $clase['profesor'] ?></td>
                        <td>
                            <a href="editar_clase.php?id_clase=<?= $clase['id_clase'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="eliminar_clase.php?id_clase=<?= $clase['id_clase'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
<a href="crear_clase.php" class="btn btn-primary rounded-circle position-fixed" 
   style="bottom: 30px; right: 30px; width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 30px;">
    +
</a>
</html>
