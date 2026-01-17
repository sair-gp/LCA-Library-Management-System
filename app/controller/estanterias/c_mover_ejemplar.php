<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

// Habilitar CORS si es necesario
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Obtener datos del cuerpo de la petición
$data = json_decode(file_get_contents('php://input'), true);

// Validar datos recibidos
if (empty($data['ejemplarId']) || empty($data['nuevaFila'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$conexion = conexion();

// Iniciar transacción
$conexion->begin_transaction();

try {
    // 1. Obtener información actual del ejemplar
    $queryEjemplar = "SELECT e.id, e.filaID as fila_actual, f.EstanteriaID as estanteria_actual 
                     FROM ejemplares e
                     JOIN fila f ON e.filaID = f.FilaID
                     WHERE e.id = ? AND e.delete_at = 1";
    $stmt = $conexion->prepare($queryEjemplar);
    $stmt->bind_param("i", $data['ejemplarId']);
    $stmt->execute();
    $ejemplar = $stmt->get_result()->fetch_assoc();
    
    if (!$ejemplar) {
        throw new Exception('Ejemplar no encontrado o eliminado');
    }

    // 2. Verificar que la fila destino existe y tiene espacio
    $queryVerificarFila = "SELECT 
                          f.Capacidad,
                          COUNT(e.id) AS ocupacion
                          FROM fila f
                          LEFT JOIN ejemplares e ON e.filaID = f.FilaID AND e.delete_at = 1
                          WHERE f.FilaID = ?
                          GROUP BY f.FilaID";
    
    $stmt = $conexion->prepare($queryVerificarFila);
    $stmt->bind_param("i", $data['nuevaFila']);
    $stmt->execute();
    $filaDestino = $stmt->get_result()->fetch_assoc();
    
    if (!$filaDestino || ($filaDestino['ocupacion'] >= $filaDestino['Capacidad'])) {
        throw new Exception('La fila destino no tiene espacio disponible');
    }

    // 3. Actualizar la ubicación del ejemplar
    $queryMover = "UPDATE ejemplares SET filaID = ? WHERE id = ?";
    $stmt = $conexion->prepare($queryMover);
    $stmt->bind_param("ii", $data['nuevaFila'], $data['ejemplarId']);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al actualizar la ubicación del ejemplar');
    }

    // 4. Obtener datos completos del ejemplar para la respuesta
    $queryDatosEjemplar = "SELECT 
                          e.id, e.cota, l.titulo, l.isbn, l.portada,
                          GROUP_CONCAT(DISTINCT a.nombre SEPARATOR ', ') AS autor,
                          es.codigo AS estanteria, es.descripcion AS tematica,
                          f.NumeroFila AS fila
                          FROM ejemplares e
                          JOIN libros l ON e.isbn_copia = l.isbn
                          JOIN libro_autores la ON l.isbn = la.isbn_libro
                          JOIN autores a ON la.id_autor = a.id
                          JOIN fila f ON e.filaID = f.FilaID
                          JOIN estanterias es ON f.EstanteriaID = es.id
                          WHERE e.id = ?
                          GROUP BY e.id";
    
    $stmt = $conexion->prepare($queryDatosEjemplar);
    $stmt->bind_param("i", $data['ejemplarId']);
    $stmt->execute();
    $ejemplarActualizado = $stmt->get_result()->fetch_assoc();

    // 5. Actualizar contadores de las filas afectadas
    actualizarContadores($conexion, $ejemplar['fila_actual']);
    actualizarContadores($conexion, $data['nuevaFila']);
    
    // Confirmar transacción
    $conexion->commit();
    
    // Respuesta exitosa con datos actualizados
    echo json_encode([
        'success' => true,
        'message' => 'Ejemplar movido correctamente',
        'ejemplar' => $ejemplarActualizado,
        'estanteriaOrigen' => $ejemplar['estanteria_actual'],
        'filaOrigen' => $ejemplar['fila_actual']
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Función para actualizar contadores de filas
function actualizarContadores($conexion, $filaID) {
    $query = "UPDATE fila f
             SET LibrosActuales = (
                 SELECT COUNT(*) 
                 FROM ejemplares e 
                 WHERE e.filaID = f.FilaID AND e.delete_at = 1
             )
             WHERE f.FilaID = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $filaID);
    $stmt->execute();
}
?>