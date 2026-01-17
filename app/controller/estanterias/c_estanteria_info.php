<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$codigo = $_GET['codigo'] ?? '';
if (empty($codigo)) {
    http_response_code(400);
    echo json_encode(['error' => 'Código de estantería no proporcionado']);
    exit;
}

$conexion = conexion();

$query = "SELECT 
    es.codigo,
    es.descripcion,
    es.capacidad_total AS capacidad,
    COUNT(DISTINCT e.id) AS cantidadTotal
FROM estanterias es
LEFT JOIN fila f ON f.EstanteriaID = es.id
LEFT JOIN ejemplares e ON e.filaID = f.FilaID AND e.delete_at = 1
WHERE es.codigo = ?
GROUP BY es.id";

$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Estantería no encontrada']);
    exit;
}

echo json_encode($result->fetch_assoc());
?>