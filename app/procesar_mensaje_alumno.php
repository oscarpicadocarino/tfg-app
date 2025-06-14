<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Verificamos si existe la conexión
if (!file_exists('conexion.php')) {
    file_put_contents("debug_error.txt", "❌ No se encuentra conexion.php\n");
    exit;
}
require 'conexion.php';

// 2. API key
$apiKey = "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";

// 3. Leemos input recibido (JSON)
$input = json_decode(file_get_contents("php://input"), true);

// 4. Obtenemos parámetros
$mensaje_usuario = trim($input["mensaje"] ?? "");
$id_clase = $input["id_clase"] ?? null;
$errores_seleccionados = $input["errores_seleccionados"] ?? [];

if (!$mensaje_usuario || !$id_clase) {
    file_put_contents("debug_input_error.txt", "❌ Faltan 'mensaje' o 'id_clase'\n");
    http_response_code(400);
    echo json_encode(["respuesta" => "Faltan parámetros obligatorios."]);
    exit;
}

// 5. Obtenemos la asignatura a partir de la clase
$stmtClase = $pdo->prepare("SELECT id_asignatura FROM clases WHERE id_clase = ?");
$stmtClase->execute([$id_clase]);
$clase = $stmtClase->fetch(PDO::FETCH_ASSOC);
$id_asignatura = $clase['id_asignatura'] ?? null;

if (!$id_asignatura) {
    file_put_contents("debug_clase_error.txt", "❌ No se encontró id_asignatura para la clase $id_clase\n");
    http_response_code(400);
    echo json_encode(["respuesta" => "No se encontró la asignatura para la clase dada."]);
    exit;
}


// 6. Obtenemos nombre de la asignatura
$stmtAsignatura = $pdo->prepare("SELECT nombre FROM asignaturas WHERE id_asignatura = ?");
$stmtAsignatura->execute([$id_asignatura]);
$asignatura = $stmtAsignatura->fetch(PDO::FETCH_ASSOC);
$nombre_asignatura = $asignatura['nombre'] ?? 'la asignatura';

// 7. Obtenemos competencias y resultados de aprendizaje
$stmtGuias = $pdo->prepare("SELECT competencias, resultados_aprendizaje FROM guias_docentes WHERE asignatura_id = ?");
$stmtGuias->execute([$id_asignatura]);
$guia = $stmtGuias->fetch(PDO::FETCH_ASSOC);

$competencias = $guia['competencias'] ?? 'No hay competencias definidas.';
$resultados_aprendizaje = $guia['resultados_aprendizaje'] ?? 'No hay resultados de aprendizaje definidos.';

// 8. Convertimos errores seleccionados a texto
$errores_texto = count($errores_seleccionados) > 0
    ? "- " . implode("\n- ", $errores_seleccionados)
    : "No se seleccionaron errores comunes.";

// 11. Creamos el system prompt
$system_prompt = <<<EOT
Eres un asistente educativo que resuelve dudas relacionadas con $nombre_asignatura,

Ten en cuenta que las competencias de la asignatura son:
$competencias

Y los resultados de aprendizaje:
$resultados_aprendizaje

Tu objetivo principal es fomentar el pensamiento crítico, la reflexión y la resolución autónoma de los ejercicios por parte del alumno.

Tienes prohibido dar directamente la solución completa o corregida de ningún ejercicio. 
Tampoco debes validar si una respuesta es "correcta" o "incorrecta" de forma directa. 
En su lugar, debes hacer preguntas orientadoras, señalar pistas, o invitar al alumno a reflexionar sobre aspectos concretos del problema.

Ejemplos de respuestas adecuadas:
- “¿Qué conceptos de la asignatura crees que se aplican en este ejercicio?”
- “¿Podrías explicarme tu razonamiento paso a paso?”
- “¿Qué alternativa se te ocurre que respete las restricciones del enunciado?”
- “¿Crees que estás cumpliendo todas las condiciones indicadas?”
- “¿Qué parte de tu solución te genera más dudas?”

No des nunca respuestas como:
- “El código corregido sería este: [...]”
- “Aquí tienes la solución completa.”
- “Este es el código correcto.”

Siempre actúa como un tutor que guía, no como un solucionador.
EOT;

// 9. Creamos el payload para OpenAI
$payload = [
    "model" => "gpt-4-turbo",
    "messages" => [
        ["role" => "system", "content" => $system_prompt],
        ["role" => "user", "content" => $mensaje_usuario]
    ],
    "temperature" => 0.7
];

// 10. Preparamos la petición cURL

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// 11. Ejecutamos y verificamos
$response = curl_exec($ch);
$error_msg = curl_error($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


if ($error_msg || !$response) {
    http_response_code(500);
    echo json_encode(["respuesta" => "Error en cURL: $error_msg"]);
    exit;
}

// 12. Procesamos la respuesta
$data = json_decode($response, true);

if (!isset($data["choices"][0]["message"]["content"])) {
    http_response_code(500);
    echo json_encode(["respuesta" => "Error de OpenAI: respuesta incompleta"]);
    exit;
}

// 13. Respondemos al cliente
header('Content-Type: application/json');
echo json_encode([
    "respuesta" => $data["choices"][0]["message"]["content"]
]);
exit;
