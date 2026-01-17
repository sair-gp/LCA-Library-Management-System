<?php
include "../../config/database.php";
$conexion = conexion();

// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$termino = $_POST['termino'] ?? '';

// Consulta de búsqueda con protección contra SQL injection
$sql = "SELECT 
            libros.portada, 
            libros.isbn, 
            libros.titulo, 
            GROUP_CONCAT(DISTINCT CONCAT(autores.nombre) SEPARATOR ', ') AS autores, 
            libros.anio, 
            editorial.nombre AS editorialN, 
            libros.volumen, 
            libros.edicion, 
            GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, 
            (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares 
        FROM libros 
        JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro 
        JOIN autores ON libro_autores.id_autor = autores.id 
        JOIN editorial ON libros.editorial = editorial.id 
        JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro 
        JOIN categorias ON libro_categoria.id_categoria = categorias.id 
        WHERE 
            libros.titulo LIKE ? OR 
            autores.nombre LIKE ? OR 
            editorial.nombre LIKE ? 
        GROUP BY libros.isbn 
        ORDER BY libros.titulo";

$stmt = $conexion->prepare($sql);
$terminoLike = "%$termino%";
$stmt->bind_param("sss", $terminoLike, $terminoLike, $terminoLike);
$stmt->execute();
$result = $stmt->get_result();

$libros = [];
while ($row = $result->fetch_assoc()) {
    $libros[] = $row;
}

// Devolver JSON con resultados
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'libros' => $libros,
    'total' => count($libros)
]);
?>