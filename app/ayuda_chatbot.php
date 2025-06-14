<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificamos que la solicitud sea POST y tenga JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['respuesta' => 'Método no permitido.']);
    exit;
}

header('Content-Type: application/json');

require 'conexion.php';

// Leer la entrada JSON
$input = json_decode(file_get_contents("php://input"), true);

$codigo = trim($input['codigo'] ?? '');
$id_actividad = $input['id_actividad'] ?? null;

if (!$codigo || !$id_actividad) {
    http_response_code(400);
    echo json_encode(['respuesta' => 'Faltan parámetros obligatorios.']);
    exit;
}

// Crear conexión con PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$nombre_bd", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['respuesta' => 'Error de conexión a la base de datos.']);
    exit;
}

// Obtener información de la actividad
$stmt = $pdo->prepare("SELECT titulo, contenido FROM actividad WHERE id = ?");
$stmt->execute([$id_actividad]);
$actividad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$actividad) {
    http_response_code(404);
    echo json_encode(['respuesta' => 'Actividad no encontrada.']);
    exit;
}

// Prompt del sistema
$system_prompt = <<<EOT
Eres un asistente educativo que ayuda a los alumnos a resolver actividades de programación. Proporciona sugerencias y orientaciones basadas en el código proporcionado por el alumno. Sé claro y específico en tus recomendaciones.
Tu tarea es analizar el código del alumno y ofrecerle consejos para mejorar su solución o identificar errores. Considera las siguientes pautas:
1. Fomenta el pensamiento crítico y la reflexión del alumno.
2. Proporciona sugerencias constructivas y evita dar respuestas directas.
3. Si encuentras errores, explica por qué son errores.
4. Asegúrate de que el alumno comprenda los conceptos subyacentes.
Bajo ningún concepto debes responder con el código completo de la actividad, sino que debes guiar al alumno para que mejore su propia solución.
EOT;

// Prompt del usuario
$mensaje_usuario = <<<EOT
Estoy trabajando en la actividad titulada "{$actividad['titulo']}". El enunciado es el siguiente:

{$actividad['contenido']}

Mi respuesta actual es:

$codigo

¿Podrías ayudarme a mejorar mi solución o indicarme posibles errores?
EOT;

// Preparar la solicitud a la API de OpenAI
$apiKey = "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";
$payload = [
    'model' => 'gpt-4o',
    'messages' => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user', 'content' => $mensaje_usuario]
    ],
    'temperature' => 0.7
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['respuesta' => "Error al comunicarse con la API: $curlError"]);
    exit;
}

// Procesar la respuesta
$data = json_decode($response, true);
if (!isset($data['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode(['respuesta' => 'Respuesta inválida de la API.']);
    exit;
}

// Devolver la respuesta al cliente
header('Content-Type: application/json');
echo json_encode(['respuesta' => $data['choices'][0]['message']['content']]);
exit;
?>
