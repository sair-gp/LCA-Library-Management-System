<?php
include '../../config/database.php';
include '../../model/autores.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
        throw new Exception('Nombre no proporcionado');
    }

    $conn = conexion();
    $autor = new Autores();
    
    $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
    $existe = $autor->verificarExistencia($conn, $nombre);
    
    echo json_encode(['exists' => $existe]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}