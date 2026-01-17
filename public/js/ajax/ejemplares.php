<?php
include '../../../app/config/database.php';
$conexion = conexion();
$data = json_decode(file_get_contents('php://input'), true);
if ($data) {
    $isbn = $data['currentISBN'];
    $sql = "SELECT libros.titulo, ejemplares.cota, estado_ejemplar.estado_ejemplar
            FROM libros
            JOIN ejemplares ON libros.isbn = ejemplares.isbn_copia
            JOIN estado_ejemplar ON ejemplares.estado = estado_ejemplar.id
            WHERE ejemplares.isbn_copia = '$isbn';";
    $resultado = $conexion->query($sql);
    if ($resultado) {
        // Send a response back to the client

        $num_rows = $resultado->num_rows;
        $html = '';

        if ($num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $html .= '<tr>';
                $html .= '<td>' . $row['titulo'] . '</td>';
                $html .= '<td>' . $row['cota'] . '</td>';
                $html .= '<td>' . $row['estado_ejemplar'] . '</td>';
                $html .= '</tr>';
            }
        } else {


            $html .= '<tr>';
            $html .= '<td colspan="3">No hay ejemplares registardos de este libro</td>';
            $html .= '</tr>';

        }

    }

    echo json_encode($html, JSON_UNESCAPED_UNICODE);


} else {
    // Handle invalid data or no data sent
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
}