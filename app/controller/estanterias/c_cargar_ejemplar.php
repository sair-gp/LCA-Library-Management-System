<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$ejemplarId = $_GET['id'] ?? '';
$conexion = conexion();

$query = "SELECT 
    e.id,
    es.codigo AS estanteria,
    es.descripcion AS tematica,
    f.NumeroFila AS fila,
    e.cota AS codigo
FROM ejemplares e
JOIN fila f ON e.filaID = f.FilaID
JOIN estanterias es ON f.EstanteriaID = es.id
WHERE e.id = ?";

$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $ejemplarId);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());
?>