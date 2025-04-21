<?php
// actividades.php
require 'conexion.php';

$id_asignatura = $_GET['id_asignatura'] ?? null;

if (!$id_asignatura) {
    die("ID de asignatura no especificado.");
}

// Obtener actividades
$stmt = $pdo->prepare("SELECT * FROM actividad WHERE id_asignatura = ?");
$stmt->execute([$id_asignatura]);
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades de la Asignatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h3 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .table {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table td {
            background-color: #f9f9f9;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            font-size: 14px;
            padding: 5px 10px;
            text-transform: capitalize;
        }

        .btn {
            margin: 0 5px;
        }

        .btn-sm {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h3>Actividades de la asignatura</h3>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($actividades as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['titulo']) ?></td>
                <td>
                    <span class="badge bg-<?= $row['estado'] === 'publicada' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($row['estado']) ?>
                    </span>
                </td>
                <td>
                    <a href="ver_actividad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                    <a href="editar_actividad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                    <?php if ($row['estado'] !== 'publicada'): ?>
                        <a href="publicar_actividad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Publicar</a>
                    <?php else: ?>
                        <a href="publicar_actividad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Pasar a Borrador</a>
                    <?php endif; ?>
                    <a href="eliminar_actividad.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
