<?php
// send_email.php - Servicio simple para enviar emails con los resultados del test

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$resultado = sanitize_input($_POST['resultado'] ?? '');
$total = intval($_POST['total'] ?? 0);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

$to = $email;
$subject = 'Resultado de tu Test de Adicción Digital - Inmediatez Digital';
$fecha = date('d/m/Y H:i:s');

$mensaje = "Hola,\n\n";
$mensaje .= "Gracias por completar el test de adicción digital.\n\n";
$mensaje .= "RESULTADO:\n";
$mensaje .= "$resultado\n\n";
$mensaje .= "Puntuación total: $total\n";
$mensaje .= "Fecha: $fecha\n\n";
$mensaje .= "---\n";
$mensaje .= "Este es un proyecto educativo sobre los efectos de la inmediatez digital.\n";
$mensaje .= "Proyecto: 'Inmediatez Digital - Los precios de la impaciencia'\n";
$mensaje .= "Autor: Ignacio Bustos | E.P.S.\n";

$headers = "From: noreply@inmedaitezdigital.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $mensaje, $headers)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Email enviado correctamente',
        'email' => $email
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al enviar el email',
        'details' => 'Intenta nuevamente más tarde'
    ]);
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags($data));
}
?>
