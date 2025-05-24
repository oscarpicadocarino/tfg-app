<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

$id_actividad = $_GET['id'] ?? null;
$id_alumno = $_SESSION['user_id'];

if (!$id_actividad) {
    echo "Actividad no especificada.";
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Procesar el envío del código
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $codigo = $_POST['codigo'] ?? '';

        if (!empty($codigo)) {
            $stmt = $pdo->prepare("INSERT INTO entregas (id_alumno, id_actividad, codigo) VALUES (?, ?, ?)");
            $stmt->execute([$id_alumno, $id_actividad, $codigo]);
            $mensaje_exito = "¡Entrega realizada con éxito!";
        } else {
            $mensaje_error = "El código no puede estar vacío.";
        }
    }

    // Obtener la actividad
    $stmt = $pdo->prepare("SELECT titulo, contenido FROM actividad WHERE id = ?");
    $stmt->execute([$id_actividad]);
    $actividad = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$actividad) {
        echo "Actividad no encontrada.";
        exit();
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
    <title>Resolver Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs/loader.min.js"></script>
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
            overflow-y: auto;
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
        #editor {
            height: 400px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 class="mb-5 text-center fw-bold pb-2 border-bottom border-dark">Menú</h3>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="inicio_alumno.php" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
        </li>
        <li class="nav-item">
            <a href="chatbot.php" class="nav-link"><i class="bi bi-chat-dots"></i> ChatBot</a>
        </li>
        <li class="nav-item">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
        </li>
    </ul>
</div>

<div class="content">
    <h2 class="mb-4 fw-semibold"><?= htmlspecialchars($actividad['titulo']) ?></h2>

    <div class="mb-4">
        <p><?= nl2br(htmlspecialchars($actividad['contenido'])) ?></p>
    </div>

    <form method="POST" onsubmit="return enviarCodigo()">
        <div class="mb-4">
            <label class="form-label fw-semibold">Tu solución:</label>
            <div id="editor"></div>
            <input type="hidden" name="codigo" id="codigo">
        </div>
        <input type="hidden" name="id_actividad" value="<?= $id_actividad ?>">
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

    <!-- Mensajes de éxito o error aquí, dentro del contenido -->
    <?php if (isset($mensaje_exito)): ?>
        <div class="alert alert-success mt-3"><?= htmlspecialchars($mensaje_exito) ?></div>
    <?php elseif (isset($mensaje_error)): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($mensaje_error) ?></div>
    <?php endif; ?>
</div>


<script>
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.34.1/min/vs' }});
require(['vs/editor/editor.main'], function () {
    window.editor = monaco.editor.create(document.getElementById('editor'), {
        value: "// Escribe tu código aquí...",
        language: "cpp", // Puedes ajustar el lenguaje según el tipo de actividad
        theme: "vs-dark",
        automaticLayout: true
    });
});

function enviarCodigo() {
    document.getElementById('codigo').value = editor.getValue();
    return true;
}


</script>

</body>
</html>
