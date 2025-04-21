<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'profesor') {
    header("Location: login.php");
    exit();
}

$host = 'db';
$usuario = 'usuario';
$contrasena = 'password';
$nombre_bd = 'tfg_app_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['id_asignatura'])) {
        die("ID de asignatura no especificado.");
    }
    $id_asignatura = $_GET['id_asignatura'];

    // Nombre de la asignatura (ejemplo)
    $nombre_asignatura = "Programación Orientada a Objetos";

    // Eliminar
    if (isset($_GET['eliminar'])) {
        $stmt = $pdo->prepare("DELETE FROM errores_comunes WHERE id_error = ? AND id_asignatura = ?");
        $stmt->execute([$_GET['eliminar'], $id_asignatura]);
        header("Location: errores_comunes.php?id_asignatura=" . $id_asignatura);
        exit();
    }

    // Insertar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tema = $_POST['tema'];
        $descripcion = $_POST['descripcion'];

        if (!empty($tema) && !empty($descripcion)) {
            $stmt = $pdo->prepare("INSERT INTO errores_comunes (tema, descripcion, id_asignatura) VALUES (?, ?, ?)");
            $stmt->execute([$tema, $descripcion, $id_asignatura]);
        }
    }

    $temas_stmt = $pdo->prepare("SELECT DISTINCT tema FROM errores_comunes WHERE id_asignatura = ?");
    $temas_stmt->execute([$id_asignatura]);
    $temas = $temas_stmt->fetchAll(PDO::FETCH_COLUMN);

    $filtro_tema = $_GET['tema'] ?? '';
    if ($filtro_tema) {
        $stmt = $pdo->prepare("SELECT * FROM errores_comunes WHERE id_asignatura = ? AND tema = ?");
        $stmt->execute([$id_asignatura, $filtro_tema]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM errores_comunes WHERE id_asignatura = ?");
        $stmt->execute([$id_asignatura]);
    }
    $errores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Errores Comunes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            margin: 0;
            height: 100vh;
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
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
            flex-grow: 1;
            overflow-y: auto;
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

    <!-- Contenido Principal -->
    <div class="main-content">
        <h2 class="mb-4"><?= $nombre_asignatura ?> - Errores Comunes</h2>

        <!-- Filtro -->
        <form method="GET" class="mb-3">
            <input type="hidden" name="id_asignatura" value="<?= $id_asignatura ?>">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label for="tema_filtro" class="form-label">Filtrar por tema:</label>
                    <select name="tema" id="tema_filtro" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach ($temas as $tema): ?>
                            <option value="<?= htmlspecialchars($tema) ?>" <?= $tema === $filtro_tema ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tema) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <!-- Lista de errores -->
        <?php if (count($errores) > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tema</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($errores as $error): ?>
                        <tr>
                            <td><?= htmlspecialchars($error['tema']) ?></td>
                            <td><?= htmlspecialchars($error['descripcion']) ?></td>
                            <td>
                                <a href="?id_asignatura=<?= $id_asignatura ?>&eliminar=<?= $error['id_error'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este error?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay errores comunes registrados para esta asignatura.</div>
        <?php endif; ?>
    </div>
    <!-- Botón para añadir nuevo error (flotante en la esquina inferior derecha) -->
<a href="anadir_error.php?id_asignatura=<?= $id_asignatura ?>" class="btn btn-primary btn-lg rounded-circle position-fixed" style="bottom: 20px; right: 20px; z-index: 1000;">
    <i class="bi bi-plus"></i>
</a>

</body>
</html>
