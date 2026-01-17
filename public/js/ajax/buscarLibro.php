<?php

include '../../../app/config/database.php';
$conexion = conexion();

$columsSelect = ["libros.isbn", "libros.titulo", "autores.nombre", "autores.apellido", "libros.anio", "editorial.nombre as editorialN", "libros.edicion", "GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categoria", "(SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares"];

$columsWhere = ["libros.isbn", "libros.titulo", "autores.nombre", "autores.apellido", "libros.anio", "editorial.nombre", "libros.edicion", "categorias.nombre"];

$table = "libros JOIN autores ON libros.autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id";


$where = '';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {

    $termino = $conexion->real_escape_string($data['termino']) ?? null;

    $where = "WHERE (";

    $cont = count($columsWhere);
    for ($i = 0; $i < $cont; $i++) {
        $where .= $columsWhere[$i] . " LIKE '%" . $termino . "%' OR ";
    }

    $where = substr_replace($where, "", -3);
    $where .= ") AND delete_at = 0 GROUP BY libros.isbn ORDER BY libros.isbn LIMIT 0, 9";

} else {
    // Handle invalid data or no data sent
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
}

$sql = "SELECT " . implode(", ", $columsSelect) . "
FROM $table
$where";

$resultado = $conexion->query($sql);
$num_rows = $resultado->num_rows;

$html = '';

if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $html .= "<tr>";
        $html .= "  <td id='isbn' class='notEditable'>{$row['isbn']}</td>";
        $html .= "  <td id='tituloTd' class='editable'>{$row['titulo']}</td>";
        $html .= "  <td id='nombreAutorTd' class='notEditable'>{$row['nombre']} {$row['apellido']}</td>";
        $html .= "  <td id='anioTd' class='editable'>{$row['anio']}</td>";
        $html .= "  <td id='editorialTd' class='notEditable'>{$row['editorialN']}</td>";
        $html .= "  <td id='edicionTd' class='editable'>{$row['edicion']}</td>";
        $html .= "  <td id='categoriaTd' class='notEditable'>{$row['categoria']}</td>";
        $html .= "  <td>";
        $html .= "    <button type='button' class='btn btn-success modalbtnAgregar' data-bs-toggle='modal' data-bs-target='#agregarEjemplar'>";
        $html .= "      <i class=' bi bi-plus'></i>";
        $html .= "    </button>";
        $html .= "    <button type='button' class='btn btn-primary modalbtnMostrarEjemplar' data-bs-toggle='modal' data-bs-target='#modalEjemplares'>";
        $html .= "      <i class='bi bi-list'></i>";
        $html .= "    </button>";
        $html .= "    <button type='button' class='btn btn-warning modalbtnEditar' data-bs-toggle='modal' data-bs-target='#editar'>";
        $html .= "      <i class='bi bi-pencil'></i>";
        $html .= "    </button>";
        $html .= "    <button type='button' class='btn btn-danger modalbtnEliminar' data-bs-toggle='modal' data-bs-target='#eliminar'>";
        $html .= "      <i class=' bi bi-trash'></i>";
        $html .= "    </button>";
        $html .= "  </td>";
        $html .= "</tr>";
    }
} else {

    $html .= '<tr>';
    $html .= '<td colspan="9" style="text-align: center;">Sin resultados</td>';
    $html .= '</tr>';

}

echo json_encode($html, JSON_UNESCAPED_UNICODE);