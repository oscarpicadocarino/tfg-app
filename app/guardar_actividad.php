<?php
require 'conexion.php';

$id_clase = $_POST['id_clase'] ?? null;
$contenido = $_GET['contenido'] ?? '';
$titulo = '';
$mensaje = '';

if (!$id_clase) {
    die("ID de clase no especificado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if ($titulo && $contenido) {
    $stmt = $pdo->prepare("INSERT INTO actividad (id_clase, titulo, contenido, estado) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$id_clase, $titulo, $contenido, 'borrador'])) {
            $mensaje = "✅ Actividad guardada correctamente.";
            $titulo = '';
            $contenido = '';
        } else {
            $mensaje = "❌ Error al guardar la actividad.";
        }
    } else {
        $mensaje = "⚠️ Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guardar Actividad</title>
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
        .content {
            flex-grow: 1;
            padding: 40px;
            background-color: #fff;
        }
        textarea {
            resize: vertical;
            min-height: 150px;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h3 class="mb-5 text-center fw-bold pb-2 border-bottom border-dark">Menú</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="inicio_profesor.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a href="generar_actividad.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
        </li>
        <li class="nav-item">
            <a href="actividades.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="content">
    <h2 class="fw-semibold mb-4">Guardar Actividad</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id_clase" value="<?= htmlspecialchars($id_clase) ?>">

        <div class="mb-3">
            <label for="titulo" class="form-label fw-semibold">Título de la actividad:</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="<?= htmlspecialchars($titulo) ?>" required>
        </div>

        <div class="mb-3">
            <label for="contenido" class="form-label fw-semibold">Contenido de la actividad:</label>
            <textarea name="contenido" id="contenido" class="form-control" required><?= htmlspecialchars($contenido) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Guardar Actividad</button>
    </form>

</div>
</body>
</html>
