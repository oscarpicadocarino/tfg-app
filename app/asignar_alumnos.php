<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Si la petición es AJAX para obtener clases según asignatura
if (isset($_GET['id_asignatura']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $id_asignatura = $_GET['id_asignatura'];
    $stmt = $pdo->prepare("SELECT * FROM clases WHERE id_asignatura = ?");
    $stmt->execute([$id_asignatura]);
    $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($clases);
    exit(); // Detener ejecución del resto del archivo
}

// Procesar formulario de asignación de alumnos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clase = $_POST['id_clase'];
    $alumnos_seleccionados = $_POST['alumnos'] ?? [];

    foreach ($alumnos_seleccionados as $id_alumno) {
        // Evitar duplicados
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM alumnos_clases WHERE id_clase = ? AND id_alumno = ?");
        $stmt->execute([$id_clase, $id_alumno]);

        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO alumnos_clases (id_clase, id_alumno) VALUES (?, ?)");
            $insert->execute([$id_clase, $id_alumno]);
        }
    }

    $mensaje = "Alumnos asignados correctamente.";
}

// Obtener asignaturas, clases y alumnos para el formulario
$asignaturas = $pdo->query("SELECT * FROM asignaturas")->fetchAll(PDO::FETCH_ASSOC);
$clases = $pdo->query("SELECT * FROM clases")->fetchAll(PDO::FETCH_ASSOC);
$alumnos = $pdo->query("SELECT * FROM usuarios WHERE tipo_usuario = 'alumno'")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Alumnos a Clase</title>
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
        </style>
</head>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('id_asignatura').addEventListener('change', function () {
        const idAsignatura = this.value;
        const claseSelect = document.getElementById('id_clase');

        claseSelect.innerHTML = '<option value="">Cargando clases...</option>';

        if (idAsignatura) {
            fetch(`<?php echo $_SERVER['PHP_SELF']; ?>?id_asignatura=${idAsignatura}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                claseSelect.innerHTML = '<option value="">Selecciona una clase</option>';
                data.forEach(clase => {
                    const option = document.createElement('option');
                    option.value = clase.id_clase;
                    option.textContent = clase.nombre || `Clase ${clase.id_clase}`;
                    claseSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar clases:', error);
                claseSelect.innerHTML = '<option value="">Error al cargar</option>';
            });
        } else {
            claseSelect.innerHTML = '<option value="">Selecciona una clase</option>';
        }
    });
});
</script>



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
    <h2>Asignar Alumnos a una Clase</h2>

    <form method="POST">
        <!-- Seleccionar asignatura -->
        <div class="mb-3">
            <label for="id_asignatura" class="form-label">Asignatura</label>
            <select class="form-select" name="id_asignatura" id="id_asignatura" required>
                <option value="">Selecciona una asignatura</option>
                <?php foreach ($asignaturas as $asignatura): ?>
                    <option value="<?= $asignatura['id_asignatura'] ?>"><?= $asignatura['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Seleccionar clase (se llena dinámicamente) -->
        <div class="mb-3">
            <label for="id_clase" class="form-label">Clase</label>
            <select class="form-select" name="id_clase" id="id_clase" required>
                <option value="">Selecciona una clase</option>
            </select>
        </div>


        <div class="mb-3">
            <label class="form-label">Selecciona alumnos:</label>
            <?php foreach ($alumnos as $alumno): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="alumnos[]" value="<?= $alumno['id_usuario'] ?>" id="alumno_<?= $alumno['id_usuario'] ?>">
                    <label class="form-check-label" for="alumno_<?= $alumno['id_usuario'] ?>">
                        <?= $alumno['nombre'] ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary">Asignar Alumnos</button>
    </form>
</div>
</body>
<?php if (!empty($mensaje)): ?>
    <div class="alert alert-success text-center w-100" style="position: fixed; bottom: 0; left: 0; z-index: 9999; border-radius: 0;">
    <?= $mensaje ?>
</div>
    <script>
        setTimeout(function() {
            document.querySelector('.alert').style.display = 'none';
        }, 3000);
    </script>

<?php endif; ?>
</html>
