<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $query = sanitizeInput($data['query'] ?? '');
    
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }
    
    $conn = conexion();
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Consulta que solo incluye ejemplares con estantería asignada
    $sql = "SELECT 
    e.id, 
    e.cota, 
    ee.estado_ejemplar,
    l.portada,
    l.isbn,
    CASE
    WHEN l.es_obra_completa = 1 THEN
        CONCAT(l.titulo, '\. ', l.edicion, ' | ', e.cota)
    ELSE
        CASE
            WHEN v.nombre IS NULL THEN
                CONCAT(l.titulo, ' \. ', l.edicion, ' | ', e.cota)  -- Si no hay volumen
            WHEN REGEXP_REPLACE(v.nombre, '[0-9]', '') = l.titulo THEN
                CONCAT(l.titulo, ' \. ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
            ELSE
                CONCAT(l.titulo, ' \"', v.nombre, '\". ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
        END
END AS titulo_libro, 
    es.codigo AS estanteria,
    f.NumeroFila AS numero_fila
FROM ejemplares e
INNER JOIN libros l ON e.isbn_copia = l.isbn           -- Solo ejemplares con libro existente
INNER JOIN estado_ejemplar ee ON e.estado = ee.id      -- Solo ejemplares con estado válido
LEFT JOIN volumen v ON v.isbn_vol = e.isbn_vol         -- LEFT JOIN porque puede ser opcional
INNER JOIN fila f ON e.filaID = f.FilaID                -- LEFT JOIN si la fila puede ser NULL
LEFT JOIN estanterias es ON f.EstanteriaID = es.id
            WHERE e.delete_at = 1 AND (e.cota LIKE ?
               OR l.titulo LIKE ?
               OR l.isbn LIKE ?)
              AND e.filaID IS NOT NULL  -- Aseguramos que tenga fila asignada
            ORDER BY l.titulo ASC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }
    
    $searchQuery = "%$query%";
    $stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $results = [];
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($results);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    
    if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
        $conn->close();
    }
}