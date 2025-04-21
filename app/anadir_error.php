<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener ID de la asignatura desde la URL
    if (!isset($_GET['id_asignatura'])) {
        die("ID de asignatura no especificado.");
    }
    $id_asignatura = $_GET['id_asignatura'];

    // Manejar inserción
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tema = $_POST['tema'];
        $descripcion = $_POST['descripcion'];

        if (!empty($tema) && !empty($descripcion)) {
            $stmt = $pdo->prepare("INSERT INTO errores_comunes (tema, descripcion, id_asignatura) VALUES (?, ?, ?)");
            $stmt->execute([$tema, $descripcion, $id_asignatura]);
            header("Location: errores_comunes.php?id_asignatura=" . $id_asignatura);
            exit();
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            flex-direction: row;
        }

        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            padding: 20px;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
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

        .content {
            margin-left: 270px;
            padding: 20px;
            flex: 1;
        }

        .form-container h5 {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
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

    <!-- Contenido -->
    <div class="content">
        <h2>Añadir Nuevo Error</h2>
        <p>Rellena el formulario a continuación para añadir un nuevo error común en la asignatura.</p>

        <h5>Añadir Nuevo Error</h5>
        <form method="POST" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="tema" class="form-label">Tema</label>
                    <input type="text" name="tema" id="tema" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" name="descripcion" id="descripcion" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Añadir Error</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
