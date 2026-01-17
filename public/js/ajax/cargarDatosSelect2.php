<?php

include '../../../app/config/database.php';
$conexion = conexion();

//para registro de libros

if (isset($_GET["nombreCategoria"])) {
    $nombreC = mysqli_real_escape_string($conexion, $_GET['nombreCategoria']); // Sanitize input

    // SQL query to fetch the user details
    $sql = "SELECT id, nombre FROM categorias WHERE nombre LIKE '$nombreC%' LIMIT 10"; // Limiting to 10 results

    // Execute the query
    $resultado = mysqli_query($conexion, $sql);

    // If there are results, format them for Select2
    if (mysqli_num_rows($resultado) > 0) {
        $data = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $data[] = [
                'id' => $row['id'], // ID of the user
                'text' => $row['nombre'] // Display name of the user
            ];
        }
        // Return the data as a JSON response
        echo json_encode(['results' => $data]);
    } else {
        // If no results found, return an empty array
        echo json_encode(['results' => []]);
    }
}


if (isset($_GET["nombreAutor"])) {
    $term = $_GET['nombreAutor'];

    $sql = "SELECT id, nombre FROM autores WHERE nombre LIKE ? LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $searchTerm = '%' . $term . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data['results'][] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido']
        ];
    }

    echo json_encode($data);
}



if (isset($_GET["nombreEditorial"])) {
    $term = $_GET['nombreEditorial'];

    $sql = "SELECT id, nombre FROM editorial WHERE nombre LIKE ? LIMIT 10";
    $stmt = $conexion->prepare($sql);
    $searchTerm = '%' . $term . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data['results'][] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];
    }

    echo json_encode($data);
}





//para ???
if (isset($_GET["nombreCota"])) {
    $term = '%' . $_GET['nombreCota'] . '%';
    $stmt = $conexion->prepare("SELECT e.id, 
    CASE
        WHEN l.es_obra_completa = 1 THEN
            CONCAT(l.titulo, '\. ', l.edicion, ' | ', e.cota)
        ELSE
            CASE
                WHEN REGEXP_REPLACE(v.nombre, '[0-9]', '') = l.titulo THEN
                    CONCAT(l.titulo, ' \. ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
                ELSE
                    CONCAT(l.titulo, ' \"', v.nombre, '\". ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
            END
    END AS text 
FROM ejemplares e 
INNER JOIN libros l ON e.isbn_copia = l.isbn 
INNER JOIN volumen v ON v.id = e.isbn_vol
WHERE e.cota LIKE ? AND e.estado = 1 AND e.delete_at = 1 AND v.isbn_obra = l.isbn
LIMIT 10;");
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['id' => $row['id'], 'text' => $row['text']];
    }
    echo json_encode(['results' => $data]);
    exit;
}

//para desincorporar libro
if (isset($_GET["nombreCotaDes"], $_GET["isbnDes"])) {
    $term = '%' . $_GET['nombreCotaDes'] . '%';
    $isbn = $_GET["isbnDes"];
    $stmt = $conexion->prepare("SELECT e.cota, 
    CASE
        WHEN l.es_obra_completa = 1 THEN
            CONCAT(l.titulo, '\. ', l.edicion, ' | ', e.cota)
        ELSE
            CASE
                WHEN REGEXP_REPLACE(v.nombre, '[0-9]', '') = l.titulo THEN
                    CONCAT(l.titulo, ' \. ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
                ELSE
                    CONCAT(l.titulo, ' \"', v.nombre, '\". ', l.edicion, ' volumen ', v.numero, ' | ', e.cota)
            END
    END AS text 
FROM ejemplares e 
INNER JOIN libros l ON e.isbn_copia = l.isbn 
INNER JOIN volumen v ON v.id = e.isbn_vol
WHERE e.cota LIKE ? AND e.estado = 1 AND e.delete_at = 1 AND v.isbn_vol = ?
LIMIT 10;");
    $stmt->bind_param("ss", $term, $isbn);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['id' => $row['cota'], 'text' => $row['text']];
    }
    echo json_encode(['results' => $data]);
    exit;
}


if (isset($_GET["responsable"])) {
    $term = '%' . $_GET['responsable'] . '%';
    $stmt = $conexion->prepare("
        SELECT u.cedula, CONCAT(u.nombre, ' ', u.apellido, ' | ', u.cedula) AS responsable 
        FROM usuarios u 
        WHERE u.cedula LIKE ? OR u.nombre LIKE ? 
        LIMIT 10
    ");
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['id' => $row['cedula'], 'text' => $row['responsable']];
    }
    echo json_encode(['results' => $data]);
    exit;
}

//cuando se registra una asistencia
if (isset($_GET["cedulaVisitanteAsistencia"])) {
    $term = '%' . $_GET["cedulaVisitanteAsistencia"] . '%';

    $stmt = $conexion->prepare("
        SELECT v.cedula, CONCAT(v.nombre, ' | ', v.cedula) AS responsable FROM visitantes v
        WHERE v.cedula LIKE ? OR v.nombre LIKE ?
        LIMIT 10
    ");
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['id' => $row['cedula'], 'text' => $row['responsable']];
    }
    echo json_encode(['results' => $data]);
    exit;
}












//para obtener ubicación
if (isset($_GET["ubicacion"])) {
    $term = '%' . $_GET['ubicacion'] . '%';
    $stmt = $conexion->prepare("
        SELECT u.id, CONCAT (u.numero, ' | ', u.seccion) AS text 
        FROM ubicacion_ejemplares u 
        WHERE u.numero LIKE ? OR u.seccion LIKE ? 
        LIMIT 10
    ");
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['id' => $row['id'], 'text' => $row['text']];
    }
    echo json_encode(['results' => $data]);
    exit;
}

if (isset($_GET["autor"])) {
    $term = $_GET['autor'] ?? '';

    $query = $conexion->prepare("SELECT id, nombre FROM autores WHERE LOWER(nombre) LIKE (?)");
    $term = "%" . $term . "%"; // Añade los comodines directamente al término
    $query->bind_param("s", $term); // Vincula el parámetro

    $query->execute(); // Ejecuta la consulta

    $resultados = [];

    $query->bind_result($id, $nombre); // Vincula las columnas de resultado

    while ($query->fetch()) { // Obtén los resultados
        $resultados[] = ["id" => $id, "text" => $nombre];
    }

    $query->close(); // Cierra la sentencia
    $conexion->close(); // Cierra la conexión (buena práctica)

    echo json_encode($resultados);
}


if (isset($_GET["editorial"])) {

    $term = $_GET['editorial'] ?? '';

    $query = $conexion->prepare("SELECT id, nombre, origen FROM editorial WHERE LOWER(nombre) LIKE LOWER(?)");
    $term = "%" . $term . "%"; // Add wildcards directly to the term
    $query->bind_param("s", $term); // Bind the parameter

    $query->execute();

    $resultados = [];

    $query->bind_result($id, $nombre, $origen); // Bind result columns

    while ($query->fetch()) {
        $resultados[] = ["id" => $id, "text" => $nombre, "origen" => $origen];
    }

    $query->close();
    $conexion->close();

    echo json_encode($resultados);
}


if (isset($_GET["categoria"])) {
    $term = $_GET['categoria'] ?? '';

    $query = $conexion->prepare("SELECT c.id, c.nombre, d.Codigo 
                                FROM categorias c
                                JOIN dewey d ON c.dewey = d.DeweyID
                                WHERE LOWER(c.nombre) LIKE LOWER(?)");
    $term = "%" . $term . "%";
    $query->bind_param("s", $term);
    $query->execute();

    $resultados = [];
    $query->bind_result($id, $nombre, $codigo);

    while ($query->fetch()) {
        $resultados[] = ["id" => $id, "text" => $nombre, "codigo" => $codigo];
    }

    $query->close();
    $conexion->close();

    echo json_encode($resultados);
}


//parte del registro de libros
if (isset($_GET["tipoDeMedio"])) {

   // $term = $_GET['tipoDeMedio'] ?? '';

    $query = $conexion->prepare("SELECT id, nombre FROM tipo_medio"); 
    $query->execute();

    $resultados = [];

    $query->bind_result($id, $nombre);

    while ($query->fetch()) {
        $resultados[] = ["id" => $id, "text" => $nombre];
    }

    $query->close();
    $conexion->close();

    echo json_encode($resultados);
}



//mostrar prestamo para las multas
if (isset($_GET["terminoPrestamo"], $_GET["cedulaLector"]) && !empty($_GET["terminoPrestamo"] && !empty($_GET["cedulaLector"]))) {
    $termPrestamo = $_GET['terminoPrestamo'] ?? "";
    $cedula = $_GET["cedulaLector"] ?? "";

    $query = $conexion->prepare("SELECT
        p.id AS id,
        l.titulo AS nombre
    FROM
        prestamos p
    JOIN
        visitantes v ON v.cedula = p.lector
    LEFT JOIN
        multas m ON m.prestamo_id = p.id
    LEFT JOIN estado_prestamo as ep ON ep.id = p.estado
    LEFT JOIN ejemplares as ej ON ej.id = p.cota
    LEFT JOIN libros as l ON l.isbn = ej.isbn_copia
    WHERE
        p.estado IN (3, 5)
    AND
        m.id IS NULL
    AND p.id = ? AND v.cedula = ?");
    
    $query->bind_param("ii", $termPrestamo, $cedula);

    if (!$query->execute()) {
        echo json_encode(["error" => "Error en la consulta SQL"]);
        exit;
    }

    $resultados = [];

    $query->bind_result($id, $nombre);

    while ($query->fetch()) {
        $resultados[] = ["id" => $id, "text" => $nombre];
    }

    $query->close();
    $conexion->close();

    echo json_encode($resultados);
}