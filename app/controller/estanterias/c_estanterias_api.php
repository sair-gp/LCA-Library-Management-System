<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$conexion = conexion();

$query = "SELECT 
    es.id,
    es.codigo,
    es.descripcion,
    es.capacidad_total AS capacidad,
    COUNT(DISTINCT e.id) AS ocupacion
FROM estanterias es
LEFT JOIN fila f ON f.EstanteriaID = es.id
LEFT JOIN ejemplares e ON e.filaID = f.FilaID AND e.delete_at = 1
GROUP BY es.id
HAVING (capacidad - ocupacion) > 0";

$result = $conexion->query($query);
$estanterias = [];

while ($row = $result->fetch_assoc()) {
    $estanterias[] = $row;
}

echo json_encode($estanterias);
?>