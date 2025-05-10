<?php
require 'conexion.php';

$id_clase = $_GET['id_clase'] ?? null;
if (!$id_clase) {
    die("ID de clase no especificado.");
}

// Obtener id_asignatura correctamente usando id_clase
$stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_clase = ?");
$stmtClase->execute([$id_clase]);

$clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
$id_asignatura = $clase['id_asignatura'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM actividad WHERE id_clase = ?");
$stmt->execute([$id_clase]);
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades de la Clase</title>
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
            <a href="generar_actividad.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-plus-square"></i> Generar Actividad</a>
        </li>
        <li class="nav-item">
            <a href="actividades.php?id_clase=<?= htmlspecialchars($id_clase) ?>" class="nav-link"><i class="bi bi-list-ul"></i> Gestionar Actividades</a>
        </li>
        <li class="nav-item">
            <a href="errores_comunes.php?id_clase=<?= htmlspecialchars($id_clase) ?>&id_asignatura=<?= htmlspecialchars($id_asignatura) ?>" class="nav-link"><i class="bi bi-exclamation-circle"></i> Errores Comunes</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="container mt-5">
    <h1 class="mb-4">Actividades de la Clase</h1>

        <?php if (empty($errores)): ?>
    <div class="alert alert-info mt-4" role="alert">
        No se han registrado actividades para esta clase.
    </div>
<?php else: ?>

    <table class="table table-bordered table-hover">
        <thead class="table-custom">
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($actividades) === 0): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay actividades registradas para esta clase.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($actividades as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['titulo']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['estado'] === 'publicada' ? 'success' : 'secondary' ?>">
                                <?= ucfirst(htmlspecialchars($row['estado'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="ver_actividad.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="editar_actividad.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-warning">Editar</a>
                            <?php if ($row['estado'] !== 'publicada'): ?>
                                <a href="publicar_actividad.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-success">Publicar</a>
                            <?php else: ?>
                                <a href="publicar_actividad.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm" style="background-color: #fd7e14; color: white;">Pasar a Borrador</a>
                            <?php endif; ?>
                            <a href="eliminar_actividad.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta actividad?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
    </table>
</div>
</body>
</html>
<?php endif; ?>