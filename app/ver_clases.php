<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Procesar creación de clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_clases'])) {
    $nombre_clase = $_POST['nombre_clase'];
    $id_asignatura = $_POST['id_asignatura'];
    $id_profesor = $_POST['id_profesor'];

    try {
        $stmt = $pdo->prepare("INSERT INTO clases (nombre, id_asignatura, id_profesor) 
                               VALUES (:nombre_clase, :id_asignatura, :id_profesor)");
        $stmt->bindParam(':nombre_clase', $nombre_clase);
        $stmt->bindParam(':id_asignatura', $id_asignatura);
        $stmt->bindParam(':id_profesor', $id_profesor);
        $stmt->execute();
        $mensaje_exito = "Clase creada con éxito.";
    } catch (PDOException $e) {
        die("Error al crear la clase: " . $e->getMessage());
    }
}

// Obtener asignaturas y profesores
$asignaturas = $pdo->query("SELECT * FROM asignaturas")->fetchAll(PDO::FETCH_ASSOC);
$profesores = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'profesor'")->fetchAll(PDO::FETCH_ASSOC);

// Obtener clases (con filtro por asignatura)
$id_asignatura_filtro = isset($_GET['id_asignatura']) ? $_GET['id_asignatura'] : '';

try {
    if ($id_asignatura_filtro) {
        $stmt = $pdo->prepare("SELECT c.*, a.nombre AS asignatura, p.nombre AS profesor 
                               FROM clases c 
                               JOIN asignaturas a ON c.id_asignatura = a.id_asignatura 
                               LEFT JOIN usuarios p ON c.id_profesor = p.id_usuario
                               WHERE c.id_asignatura = :id_asignatura");
        $stmt->bindParam(':id_asignatura', $id_asignatura_filtro);
        $stmt->execute();
    } else {
        $stmt = $pdo->query("SELECT c.*, a.nombre AS asignatura, p.nombre AS profesor 
                             FROM clases c 
                             JOIN asignaturas a ON c.id_asignatura = a.id_asignatura 
                             LEFT JOIN usuarios p ON c.id_profesor = p.id_usuario");
    }
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener clases: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clases</title>
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
        .table tbody tr {
            background-color: #ffffff;
        }
        thead.table-custom {
            background-color: rgb(97, 160, 255);
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
                <a href="gestion_usuario.php" class="nav-link"><i class="bi bi-person"></i> Gestionar Usuarios</a>
            </li>
            <li class="nav-item">
                <a href="ver_clases.php" class="nav-link"><i class="bi bi-people"></i> Gestionar Clases</a>
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
    <h1 class="mb-4">Gestión de Clases</h1>

    <?php if (isset($mensaje_exito)): ?>
        <div class="alert alert-success"><?= $mensaje_exito ?></div>
    <?php endif; ?>

    <!-- Filtro por asignatura -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="id_asignatura" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Ver todas las asignaturas --</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?= $asignatura['id_asignatura'] ?>" 
                            <?= $id_asignatura_filtro == $asignatura['id_asignatura'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($asignatura['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-custom">
            <tr>
                <th>Nombre Clase</th>
                <th>Asignatura</th>
                <th>Profesor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clases as $clase): ?>
                <tr>
                    <td><?= htmlspecialchars($clase['nombre']) ?></td>
                    <td><?= htmlspecialchars($clase['asignatura']) ?></td>
                    <td><?= $clase['profesor'] ? htmlspecialchars($clase['profesor']) : 'No asignado' ?></td>
                    <td>
                        <a href="editar_clase.php?id_clase=<?= $clase['id_clase'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar_clase.php?id_clase=<?= $clase['id_clase'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        <a href="detalle_clase.php?id_clase=<?= $clase['id_clase'] ?>" class="btn btn-sm btn-info">Ver</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="crear_clase.php" class="btn btn-success">
    Añadir Clase <i class="bi bi-person-plus"></i>
</a>
</div>
</body>
</html>
