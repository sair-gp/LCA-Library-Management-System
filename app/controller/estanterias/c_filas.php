<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$estanteriaId = $_GET['estanteriaId'] ?? '';
$conexion = conexion();

$query = "SELECT 
    f.FilaID AS id,
    f.NumeroFila AS numero,
    f.Capacidad AS capacidad,
    COUNT(e.id) AS ocupacion
FROM fila f
LEFT JOIN ejemplares e ON e.filaID = f.FilaID AND e.delete_at = 1
WHERE f.EstanteriaID = ?
GROUP BY f.FilaID
HAVING (capacidad - ocupacion) > 0";

$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $estanteriaId);
$stmt->execute();
$result = $stmt->get_result();
$filas = [];

while ($row = $result->fetch_assoc()) {
    $filas[] = $row;
}

echo json_encode($filas);
?>