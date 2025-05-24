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
    <title>Generador de Actividades</title>
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
            display: flex;
            flex-direction: column;
        }
        .chat-box {
            flex-grow: 1;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .chat-message {
            margin-bottom: 15px;
        }
        .chat-message.user {
            text-align: right;
        }
        .chat-message.assistant {
            text-align: left;
        }
        .chat-message .bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 70%;
        }
        .user .bubble {
            background-color: #d1e7dd;
            color: #000;
        }
        .assistant .bubble {
            background-color: #e2e3e5;
            color: #000;
        }
        .chat-input {
            display: flex;
        }
        .chat-input input {
            flex-grow: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .chat-input button {
            margin-left: 10px;
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

<div class="content">
    <h2 class="fw-semibold mb-4">Generador de Actividades por IA</h2>

    <div class="chat-box" id="chat-box">
        <div class="chat-message assistant">
            <div class="bubble">
                Hola 👋 Soy tu asistente para generar actividades educativas. Puedes escribirme, por ejemplo:<br>
                <i>“Diseña una actividad sobre estructuras de datos para nivel medio”</i>
            </div>
        </div>
    </div>

    <form id="chat-form" class="chat-input">
        <input type="text" id="user-input" placeholder="Escribe tu mensaje aquí..." required>
        <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
    </form>
</div>

<script>
document.getElementById("chat-form").addEventListener("submit", async function (e) {
    e.preventDefault();
    const input = document.getElementById("user-input");
    const mensaje = input.value.trim();
    if (!mensaje) return;

    // Mostrar mensaje del usuario
    const chatBox = document.getElementById("chat-box");
    const userMessage = document.createElement("div");
    userMessage.className = "chat-message user";
    userMessage.innerHTML = `<div class="bubble">${mensaje}</div>`;
    chatBox.appendChild(userMessage);

    chatBox.scrollTop = chatBox.scrollHeight;
    input.value = "";

    // Llamar a la IA (esto simula de momento)
    const assistantMessage = document.createElement("div");
    assistantMessage.className = "chat-message assistant";
    assistantMessage.innerHTML = `<div class="bubble"><em>Generando actividad...</em></div>`;
    chatBox.appendChild(assistantMessage);
    chatBox.scrollTop = chatBox.scrollHeight;

    // Aquí deberías hacer un fetch a procesar_actividad.php (lo hacemos falso de momento)
    setTimeout(() => {
        assistantMessage.innerHTML = `<div class="bubble"><strong>Actividad generada:</strong><br><br><b>Propósito:</b> Comprender recursividad.<br><b>Enunciado:</b> Diseña una función recursiva que calcule la suma de los primeros N números naturales.<br><b>Evaluación:</b> Corrección del algoritmo, uso de recursividad, legibilidad.</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 1500);
});
</script>
</body>
</html>
