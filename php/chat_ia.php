<?php
// Define el tipo de contenido como JSON
header('Content-Type: application/json');

// Lee la entrada JSON del cuerpo de la solicitud (enviada desde el JS)
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

// Si no hay mensaje, envía una respuesta de error
if (empty($userMessage)) {
    echo json_encode(['response' => 'Por favor, escribe un mensaje para el bot.']);
    exit;
}

// Configura tu clave de API de Groq
$apiKey = getenv('GROQ_API_KEY');


// URL del endpoint de la API de Groq
$url = 'https://api.groq.com/openai/v1/chat/completions';

// Prepara los datos para la solicitud. La estructura es la misma que la de OpenAI.
$data = [
    'model' => 'llama3-8b-8192', // Modelo de IA de Llama 3
    'messages' => [
        [
            'role' => 'system',
            'content' => 'Eres un asistente de inteligencia artificial para la tienda de videojuegos GameNexus. Responde preguntas de forma amigable y útil sobre juegos, consolas, géneros, horarios de la tienda, envíos y precios. Tu respuesta debe ser breve y concisa.'
        ],
        [
            'role' => 'user',
            'content' => $userMessage
        ]
    ]
];

// Codifica los datos JSON
$jsonData = json_encode($data);

// Configura y ejecuta la solicitud cURL para comunicarte con la API de Groq
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
    'Content-Length: ' . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Procesa la respuesta de la API
if ($http_code !== 200) {
    echo json_encode(['response' => 'Error de conexión con la IA. Código de error: ' . $http_code]);
    exit;
}

$responseData = json_decode($response, true);

// Extrae el mensaje de la respuesta de la IA.
$botResponse = 'Lo siento, no pude procesar tu solicitud.';
if (isset($responseData['choices'][0]['message']['content'])) {
    $botResponse = $responseData['choices'][0]['message']['content'];
} else if (isset($responseData['error']['message'])) {
    $botResponse = 'Error de la API: ' . $responseData['error']['message'];
}

// Envía la respuesta de la IA de vuelta a JavaScript
echo json_encode(['response' => $botResponse]);