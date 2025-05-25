<?php
$apiKey = "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"; // <-- Usa aquí tu API key real

// Asegurar que los errores se muestren para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Leer entrada JSON
$input = json_decode(file_get_contents("php://input"), true);
$mensaje = $input["mensaje"] ?? "";

if (!$mensaje) {
    http_response_code(400);
    echo json_encode(["respuesta" => "Mensaje vacío"]);
    exit;
}

// Preparar payload
$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Eres un asistente educativo que genera actividades relacionadas con programación, incluyendo propósito, objetivo de aprendizaje, enunciado, nivel, tiempo estimado y evaluación. Usa un formato claro y organizado."],
        ["role" => "user", "content" => $mensaje]
    ],
    "temperature" => 0.7
];

// Configurar cURL
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Ejecutar cURL
$response = curl_exec($ch);
$error_msg = curl_error($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Escribir logs para depuración
file_put_contents("respuesta_debug.txt", $response);
file_put_contents("respuesta_errores.txt", $error_msg . "\nStatus HTTP: " . $http_status);

// Manejo de errores
if ($error_msg || !$response) {
    http_response_code(500);
    echo json_encode(["respuesta" => "Error en cURL: $error_msg"]);
    exit;
}

// Procesar respuesta JSON
$data = json_decode($response, true);
if (!isset($data["choices"][0]["message"]["content"])) {
    http_response_code(500);
    echo json_encode(["respuesta" => "Error de OpenAI: respuesta incompleta"]);
    exit;
}

// Respuesta correcta
header('Content-Type: application/json');
echo json_encode([
    "respuesta" => $data["choices"][0]["message"]["content"]
]);
exit;
