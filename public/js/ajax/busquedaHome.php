<?php
// Archivo: buscar_libros.php
// Este archivo se encarga de manejar la búsqueda de libros en la base de datos
include '../../../app/config/database.php';
$conexion = conexion();
session_start();

// Conexión a la base de datos
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Si se recibe una consulta de búsqueda, se ejecuta la búsqueda en la base de datos
if (isset($_GET['query'])) {
    $query = "%" . $_GET['query'] . "%";

    // Consulta SQL para buscar libros por cualquier campo relevante
    $consulta = $conexion->prepare("SELECT libros.portada, libros.isbn, libros.titulo, 
        GROUP_CONCAT(DISTINCT CONCAT(autores.nombre) SEPARATOR ', ') AS autores, 
        libros.anio, libros.volumen, editorial.nombre AS editorialN, libros.edicion, 
        GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, 
        (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares 
        FROM libros 
        JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro 
        JOIN autores ON libro_autores.id_autor = autores.id 
        JOIN editorial ON libros.editorial = editorial.id 
        JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro 
        JOIN categorias ON libro_categoria.id_categoria = categorias.id 
        WHERE (libros.isbn LIKE ? OR libros.titulo LIKE ? OR 
        autores.nombre LIKE ? OR libros.anio LIKE ? OR 
        editorial.nombre LIKE ? OR libros.edicion LIKE ? OR categorias.nombre LIKE ?) 
        GROUP BY libros.isbn ORDER BY libros.isbn;");

    // Enlazamos los parámetros de la consulta
    $consulta->bind_param("sssssss", $query, $query, $query, $query, $query, $query, $query);
    $consulta->execute();
    $resultado = $consulta->get_result();

    // Construimos un array con los resultados
    $libros = [];
    while ($fila = $resultado->fetch_assoc()) {
        $libros[] = $fila;
    }

    // Devolvemos los resultados en formato JSON
    echo json_encode($libros);
    exit;
}
