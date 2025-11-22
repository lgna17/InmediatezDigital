<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['resultado']) || !isset($data['puntuacion'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit();
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email inválido']);
    exit();
}

$resultado = htmlspecialchars($data['resultado']);
$puntuacion = htmlspecialchars($data['puntuacion']);
$fecha = htmlspecialchars($data['fecha'] ?? date('d/m/Y H:i:s'));

$apiKey = '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';

$emailData = [
    'personalizations' => [
        [
            'to' => [['email' => $email]],
            'subject' => 'Tu resultado del Test de Adicción Digital - Inmediatez Digital'
        ]
    ],
    'from' => [
        'email' => 'nachobustos100@gmail.com',
        'name' => 'Inmediatez Digital'
    ],
    'content' => [
        [
            'type' => 'text/plain',
            'value' => "Hola,\n\nTu resultado del test de Inmediatez Digital:\n\n{$resultado}\n\nPuntuación: {$puntuacion}/30\n\nFecha: {$fecha}\n\n¡Gracias por participar!\n\nInmediatez Digital"
        ]
    ]
];

$ch = curl_init('https://api.sendgrid.com/v3/mail/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 202 || $httpCode === 200) {
    echo json_encode(['success' => true, 'message' => 'Email enviado exitosamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al enviar email', 'code' => $httpCode]);
}
?>
