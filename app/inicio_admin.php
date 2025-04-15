<?php
// inicio_admin.php
session_start();

// Verifica si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Conectar con la base de datos
$host = 'db';
$usuario = 'usuario'; 
$contrasena = 'password';
$nombre_bd = 'tfg_app_db';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit();
}

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

        <!-- Formulario para crear una clase -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="nombre_clase" class="form-label">Nombre de la clase</label>
                <input type="text" class="form-control" id="nombre_clase" name="nombre_clase" required>
            </div>

            <div class="mb-3">
                <label for="id_asignatura" class="form-label">Asignatura</label>
                <select class="form-select" id="id_asignatura" name="id_asignatura" required>
                    <option value="">Seleccione asignatura</option>
                    <?php foreach ($asignaturas as $asignatura): ?>
                        <option value="<?= $asignatura['id_asignatura'] ?>"><?= $asignatura['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_profesor" class="form-label">Profesor</label>
                <select class="form-select" id="id_profesor" name="id_profesor" required>
                    <option value="">Seleccione profesor</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?= $profesor['id_usuario'] ?>"><?= $profesor['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="crear_clase" class="btn btn-primary">Crear Clase</button>
        </form>

        <!-- Tabla de clases existentes -->
        <h3>Clases Existentes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre Clase</th>
                    <th>Asignatura</th>
                    <th>Profesor</th>
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
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
