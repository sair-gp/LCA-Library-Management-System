<?php
// Conectar a la base de datos (reemplaza con tus credenciales)
include '../../../app/config/database.php';
$conexion = conexion();


if (isset($_POST['cota'])) {
    $cota = $_POST['cota'];

    // Realizar la consulta a la base de datos
    $sql = "SELECT
    e.id,
    l.titulo
FROM
    ejemplares e
INNER JOIN libros l ON e.isbn_copia = l.isbn
WHERE
    e.cota LIKE '$cota';";

    //$sql = "SELECT id FROM ejemplares WHERE cota LIKE '%$cota%'";
    $resultado = mysqli_query($conexion, $sql);

    // Si se encuentra un resultado, enviar el ID en formato JSON
    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        echo json_encode([
            'id' => $row['id'],
            'titulo' => $row['titulo']
        ]);
    } else {
        // Si no se encuentra ningún resultado, puedes enviar un mensaje o dejar el input oculto vacío
        echo json_encode([
            'id' => "",
            'titulo' => ""
        ]);
    }
}


// Assuming you have a connection to the database already established in $conexion

// Check if the query parameter 'cedulaLector' is set
if (isset($_GET["cedulaLector"])) {
    $cLector = mysqli_real_escape_string($conexion, $_GET['cedulaLector']); // Sanitize input

    // SQL query to fetch the user details
    $sql = "SELECT cedula, CONCAT (nombre, ' | ', cedula) AS cedulaYnombre FROM visitantes WHERE cedula LIKE '%$cLector%' OR nombre LIKE '$cLector%' LIMIT 10"; // Limiting to 10 results

    // Execute the query
    $resultado = mysqli_query($conexion, $sql);

    // If there are results, format them for Select2
    if (mysqli_num_rows($resultado) > 0) {
        $data = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $data[] = [
                'id' => $row['cedula'],  // ID of the user
                'text' => $row['cedulaYnombre'] // Display name of the user
            ];
        }
        // Return the data as a JSON response
        echo json_encode(['results' => $data]);
    } else {
        // If no results found, return an empty array
        echo json_encode(['results' => []]);
    }
}






// Cerrar la conexión a la base de datos
mysqli_close($conexion);
