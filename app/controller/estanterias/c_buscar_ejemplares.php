<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$query = $_GET['query'] ?? '';
$filtro = $_GET['filtro'] ?? 'all';

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$conexion = conexion();
$sql = "SELECT 
    e.id,
    e.cota,
    l.titulo,
    l.isbn,
    l.portada,
    GROUP_CONCAT(DISTINCT a.nombre SEPARATOR ', ') AS autor,
    es.codigo AS estanteria,
    es.descripcion AS tematica,
    f.NumeroFila AS fila
FROM ejemplares e
JOIN libros l ON e.isbn_copia = l.isbn
JOIN libro_autores la ON l.isbn = la.isbn_libro
JOIN autores a ON la.id_autor = a.id
JOIN fila f ON e.filaID = f.FilaID
JOIN estanterias es ON f.EstanteriaID = es.id
WHERE e.delete_at = 1 AND (";

// Construir condiciones de búsqueda según el filtro
$conditions = [];
$params = [];

switch ($filtro) {
    case 'titulo':
        $conditions[] = "l.titulo LIKE ?";
        $params[] = "%$query%";
        break;
    case 'isbn':
        $conditions[] = "l.isbn LIKE ?";
        $params[] = "%$query%";
        break;
    case 'autor':
        $conditions[] = "a.nombre LIKE ?";
        $params[] = "%$query%";
        break;
    case 'cota':
        $conditions[] = "e.cota LIKE ?";
        $params[] = "%$query%";
        break;
    default: // 'all'
        $conditions = [
            "l.titulo LIKE ?",
            "l.isbn LIKE ?",
            "a.nombre LIKE ?",
            "e.cota LIKE ?"
        ];
        $params = array_fill(0, 4, "%$query%");
        break;
}

$sql .= implode(" OR ", $conditions) . ")
GROUP BY e.id
ORDER BY l.titulo, e.cota
LIMIT 50";

$stmt = $conexion->prepare($sql);

if (count($params) > 0) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$ejemplares = [];

while ($row = $result->fetch_assoc()) {
    $ejemplares[] = $row;
}

echo json_encode($ejemplares);
?>