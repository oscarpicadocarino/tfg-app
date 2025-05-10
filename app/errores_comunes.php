<?php
require 'conexion.php';

$id_asignatura = $_GET['id_asignatura'] ?? null;
$id_clase = $_GET['id_clase'] ?? null;  // Asegúrate de que también tomas el id_clase
if (!$id_asignatura) {
    die("ID de asignatura no especificado.");
}

$stmt = $pdo->prepare("SELECT * FROM errores_comunes WHERE id_asignatura = ?");
$stmt->execute([$id_asignatura]);
$errores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Errores Comunes de la Clase</title>
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
            <a href="inicio_profesor.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a href="generar_actividad.php?id_clase=<?= $id_clase ?>&id_asignatura=<?= $id_asignatura ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
        </li>
        <li class="nav-item">
            <a href="actividades.php?id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
        </li>
        <li class="nav-item">
            <a href="errores_comunes.php?id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" class="nav-link"><i class="bi bi-exclamation-circle"></i> Errores Comunes</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="container mt-5">
    <h1 class="mb-4">Errores Comunes</h1>

    <!-- Filtro de temas -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="id_asignatura" value="<?= htmlspecialchars($id_asignatura) ?>">
        <input type="hidden" name="id_clase" value="<?= htmlspecialchars($id_clase) ?>"> <!-- Incluye id_clase en el formulario -->
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label for="tema_filtro" class="form-label">Filtrar por tema:</label>
                <select name="tema" id="tema_filtro" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($temas as $tema): ?>
                        <option value="<?= htmlspecialchars($tema) ?>" <?= isset($_GET['tema']) && $_GET['tema'] === $tema ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tema) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <?php if (empty($errores)): ?>
    <div class="alert alert-info mt-4" role="alert">
        No se han registrado errores comunes para esta asignatura.
    </div>
<?php else: ?>

    <!-- Tabla de errores comunes -->
    <table class="table table-bordered table-hover">
        <thead class="table-custom">
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
                    <a href="editar_error.php?id=<?= $error['id_error'] ?>&id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="eliminar_error.php?eliminar=<?= $error['id_error'] ?>&id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este error?');">Eliminar</a>
                    </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Botón para añadir nuevo error común -->
    <a href="anadir_error.php?id_asignatura=<?= $id_asignatura ?>&id_clase=<?= $id_clase ?>" class="btn btn-success">
        Añadir Error Común <i class="bi bi-plus-circle"></i>
    </a>

</div>
</body>
</html>
<?php endif; ?>
