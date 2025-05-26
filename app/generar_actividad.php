<?php
require 'conexion.php';

$id_clase = $_GET['id_clase'] ?? null;
if (!$id_clase) {
    die("ID de clase no especificado.");
}

// Obtener id_asignatura usando id_clase
$stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_clase = ?");
$stmtClase->execute([$id_clase]);
$clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
$id_asignatura = $clase['id_asignatura'] ?? null;

if (!$id_asignatura) {
    die("Asignatura no encontrada para la clase especificada.");
}

// Filtro de tema desde GET
$tema_filtro = $_GET['tema'] ?? 'todos';

// Obtener errores comunes por asignatura
$errores_stmt = $pdo->prepare("SELECT id_error, descripcion, tema FROM errores_comunes WHERE id_asignatura = ?");
$errores_stmt->execute([$id_asignatura]);
$errores_comunes = $errores_stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar errores por tema, aplicando filtro
$errores_por_tema = [];
foreach ($errores_comunes as $error) {
    if ($tema_filtro === 'todos' || $error['tema'] === $tema_filtro) {
        $errores_por_tema[$error['tema']][] = $error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Generador de Actividades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    
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
            white-space: pre-wrap;
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
            white-space: pre-wrap;
            word-wrap: break-word;
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
            flex-direction: column;
        }
        .chat-input textarea {
            flex-grow: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            resize: vertical;
            font-family: monospace, monospace;
            font-size: 1rem;
            min-height: 60px;
        }
        .chat-input button {
            margin-top: 10px;
            align-self: flex-end;
        }
        .errores-selector {
            margin-bottom: 15px;
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

    <!-- Filtro de errores comunes -->
    <div class="errores-selector">
        <form method="GET" class="mb-3">
            <input type="hidden" name="id_clase" value="<?= htmlspecialchars($id_clase) ?>">
            <label for="tema" class="form-label fw-semibold">Filtrar por tema:</label>
            <select name="tema" id="tema" class="form-select mb-3" onchange="this.form.submit()">
                <option value="todos" <?= $tema_filtro === 'todos' ? 'selected' : '' ?>>Todos</option>
                <?php foreach (array_unique(array_column($errores_comunes, 'tema')) as $tema): ?>
                    <option value="<?= htmlspecialchars($tema) ?>" <?= $tema_filtro === $tema ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tema) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <label class="form-label">Errores comunes a tener en cuenta:</label>
        <div class="border rounded p-3" style="background-color: #fff; max-height: 250px; overflow-y: auto;">
            <?php if (!empty($errores_por_tema)): ?>
                <?php foreach ($errores_por_tema as $tema => $errores): ?>
                    <div>
                        <strong><?= htmlspecialchars($tema) ?></strong>
                        <?php foreach ($errores as $error): ?>
                            <div class="form-check ms-3">
                                <input class="form-check-input error-checkbox" type="checkbox" value="<?= htmlspecialchars($error['descripcion']) ?>" id="error-<?= $error['id_error'] ?>">
                                <label class="form-check-label" for="error-<?= $error['id_error'] ?>">
                                    <?= htmlspecialchars($error['descripcion']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No hay errores comunes disponibles para este tema.</p>
            <?php endif; ?>
        </div>
        <small class="text-muted">Marca los errores que deseas considerar para esta actividad.</small>
    </div>

    <div class="chat-box" id="chat-box">
        <div class="chat-message assistant">
            <div class="bubble">
                Hola 👋 <br>
            </div>
        </div>
    </div>

    <form id="chat-form" class="chat-input">
        <textarea id="user-input" placeholder="Escribe tu mensaje aquí..." required></textarea>
        <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
    </form>
</div>

<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.getElementById("chat-form").addEventListener("submit", async function (e) {
    e.preventDefault();
    const input = document.getElementById("user-input");
    const mensaje = input.value.trim();
    if (!mensaje) return;

    const erroresSeleccionados = Array.from(document.querySelectorAll(".error-checkbox:checked"))
        .map(cb => cb.value)
        .join(", ");

    const mensajeFinal = mensaje;

    const chatBox = document.getElementById("chat-box");

    const userMessage = document.createElement("div");
    userMessage.className = "chat-message user";
    userMessage.innerHTML = `<div class="bubble"><pre>${escapeHtml(mensaje)}</pre></div>`;
    chatBox.appendChild(userMessage);

    const assistantMessage = document.createElement("div");
    assistantMessage.className = "chat-message assistant";
    assistantMessage.innerHTML = `<div class="bubble"><em>Generando actividad...</em></div>`;
    chatBox.appendChild(assistantMessage);
    chatBox.scrollTop = chatBox.scrollHeight;
    input.value = "";

    try {
        const idClase = <?= json_encode($id_clase) ?>;

        const res = await fetch("procesar_actividad.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                mensaje: mensajeFinal,
                id_clase: idClase,
                errores_seleccionados: Array.from(document.querySelectorAll(".error-checkbox:checked")).map(cb => cb.value)
            })
        });

        if (!res.ok) throw new Error('Error en la respuesta');
        const data = await res.json();
        assistantMessage.innerHTML = `<div class="bubble"><pre>${escapeHtml(data.respuesta)}</pre></div>`;
    } catch (err) {
        assistantMessage.innerHTML = `<div class="bubble">Error al generar la actividad.</div>`;
    }

    chatBox.scrollTop = chatBox.scrollHeight;
});
</script>

</body>
</html>
