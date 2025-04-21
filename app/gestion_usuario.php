<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$tipoFiltro = $_GET['tipo'] ?? 'todos';

$query = "SELECT * FROM usuarios";
$params = [];

if ($tipoFiltro === 'alumno' || $tipoFiltro === 'profesor') {
    $query .= " WHERE tipo_usuario = :tipo";
    $params[':tipo'] = $tipoFiltro;
}

$usuarios = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener usuarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestión de Usuarios</h1>

    <!-- Filtro por tipo de usuario -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="tipo" class="form-select" onchange="this.form.submit()">
                    <option value="todos" <?= $tipoFiltro === 'todos' ? 'selected' : '' ?>>Todos</option>
                    <option value="alumno" <?= $tipoFiltro === 'alumno' ? 'selected' : '' ?>>Alumnos</option>
                    <option value="profesor" <?= $tipoFiltro === 'profesor' ? 'selected' : '' ?>>Profesores</option>
                </select>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Tipo de Usuario</th>
            <th>Fecha de Creación</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($usuarios) > 0): ?>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                    <td><?= htmlspecialchars($usuario['tipo_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['fecha_creacion']) ?></td>
                    <td>
                        <a href="editar_usuario.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar_usuario.php?id_usuario=<?php echo $usuario['id_usuario']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No hay usuarios registrados con ese filtro.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Botón flotante para añadir usuario -->
<a href="anadir_usuario.php" class="btn btn-success rounded-circle"
   style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
          display: flex; align-items: center; justify-content: center; font-size: 28px;">
    +
</a>
</body>
</html>
