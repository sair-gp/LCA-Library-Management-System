<?php
include '../../../app/config/database.php';

header('Content-Type: application/json');

// Configuración de conexión MySQLi
$conexion = conexion();

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

// Obtener parámetros
$estanteria = isset($_GET['estanteria']) ? intval($_GET['estanteria']) : null;
$fila = isset($_GET['fila']) ? $conexion->real_escape_string($_GET['fila']) : null;

$response = [];

try {
    if ($estanteria !== null) {
        // Consulta para obtener filas con espacio disponible
        $query = "SELECT f.FilaID as id, 
                 CONCAT('Fila ', f.NumeroFila, ' - Espacio: ', 
                 (f.Capacidad - IFNULL((
                     SELECT COUNT(*) 
                     FROM ejemplares e 
                     WHERE e.filaID = f.FilaID AND e.delete_at = 1
                 ), 0)), '/', f.Capacidad,
                 ' (', d.Codigo, ' - ', d.Descripcion, ')') as text,
        
                 (f.Capacidad - IFNULL((
                     SELECT COUNT(*) 
                     FROM ejemplares e 
                     WHERE e.filaID = f.FilaID AND e.delete_at = 1
                 ), 0)) as disponible
                 FROM fila f
                 JOIN dewey d ON f.DeweyID = d.DeweyID
                 WHERE f.EstanteriaID = ?";
        
        // Si hay término de búsqueda, lo añadimos
        if ($fila !== null) {
            $query .= " AND (f.NumeroFila LIKE ? OR d.Codigo LIKE ? OR d.Descripcion LIKE ?)";
        }
        
        $query .= " ORDER BY f.NumeroFila";
        
        $stmt = $conexion->prepare($query);
        
        if ($fila !== null) {
            $searchTerm = "%$fila%";
            $stmt->bind_param("isss", $estanteria, $searchTerm, $searchTerm, $searchTerm);
        } else {
            $stmt->bind_param("i", $estanteria);
        }
        
    } else {
        // Consulta para estanterías
        $query = "SELECT id, CONCAT(codigo, ' - ', descripcion) as text 
                 FROM estanterias 
                 ORDER BY codigo";
        $stmt = $conexion->prepare($query);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    $stmt->close();
} catch (Exception $e) {
    $response = ['error' => $e->getMessage()];
}

$conexion->close();

echo json_encode($response);