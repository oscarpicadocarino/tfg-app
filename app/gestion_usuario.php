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
        background-color: #ffffff; /* Fondo blanco para las filas */
        }
        thead.table-custom{
            background-color:rgb(97, 160, 255);
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
                <a href="ver_clases.php" class="nav-link"><i class="bi bi-plus-circle"></i> Gestionar Clases</a>
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
        <thead class="table-custom">
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
    <a href="anadir_usuario.php" class="btn btn-success">
    Añadir usuario <i class="bi bi-person-plus"></i>
</a>
</div>

</body>
</html>
