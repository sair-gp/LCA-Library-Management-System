<?php
// Incluir la clase Database
require_once "../../config/database.php";

// Crear una instancia de la clase Database
$database = new Database();

// Verificar si la solicitud es POST (para evitar ejecuciones accidentales)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Llamar al método respaldo de la clase Database
    $database->respaldo();
} else {
    // Si no es una solicitud POST, mostrar un mensaje de error
    http_response_code(405); // Método no permitido
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Solo se aceptan solicitudes POST.'
    ]);
}
?>