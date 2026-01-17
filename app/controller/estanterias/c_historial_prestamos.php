<?php
require_once "../../config/database.php";
header('Content-Type: application/json');

$isbn = $_GET['isbn'] ?? '';
$conexion = conexion();

$query = "SELECT 
    p.fecha_inicio,
    DATE_FORMAT(p.fecha_inicio, '%d/%m/%Y') as fecha_formateada,
    v.nombre,
    ep.estado AS estado
FROM 
    prestamos p
JOIN 
    ejemplares e ON p.cota = e.id
JOIN 
    visitantes v ON p.lector = v.cedula
JOIN 
    estado_prestamo ep ON p.estado = ep.id
WHERE 
    e.isbn_copia = ?
ORDER BY 
    p.fecha_inicio DESC
LIMIT 3";

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();

$historial = [];
while ($row = $result->fetch_assoc()) {
    $historial[] = [
        'fecha_inicio' => $row['fecha_inicio'],
        'fecha_formateada' => $row['fecha_formateada'],
        'nombre' => $row['nombre'],
        'estado' => $row['estado']
    ];
}

echo json_encode($historial);
?>