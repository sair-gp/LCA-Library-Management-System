<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
$conexion = conexion();

$nombre = $conexion->real_escape_string($_POST['nombre'] ?? '');

// Validación de formato
if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]{3,}$/', $nombre)) {
    echo json_encode(false);
    exit;
}

// Consulta de existencia
$query = "SELECT nombre FROM editorial WHERE nombre = '$nombre' LIMIT 1";
$resultado = $conexion->query($query);

echo json_encode($resultado->num_rows > 0);
$conexion->close();
?>