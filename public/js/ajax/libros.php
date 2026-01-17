<?php
include '../../../app/config/database.php';
include '../../../app/model/historial.php';
$conexion = conexion();

session_start();

//Para el modal detalleLibro
if (isset($_GET["isbn"])) {

    // Obtener el valor ISBN desde el parámetro GET o desde el input oculto en el frontend
    $isbn = $_GET['isbn'];

    // Consultar la base de datos para obtener los detalles del libro con ese ISBN
    $query = "SELECT libros.isbn, libros.titulo, libros.fecha_registro, GROUP_CONCAT(DISTINCT CONCAT(autores.nombre, ' ', autores.apellido) SEPARATOR ', ') AS autores, libros.anio, editorial.nombre AS editorialN, libros.edicion, GROUP_CONCAT(DISTINCT categorias.nombre SEPARATOR ', ') AS categorias, (SELECT COUNT(*) FROM ejemplares WHERE ejemplares.isbn_copia = libros.isbn) AS cantidad_ejemplares, (SELECT COUNT(*) FROM prestamos JOIN ejemplares ON prestamos.cota = ejemplares.id WHERE ejemplares.isbn_copia = libros.isbn AND prestamos.estado = 1) AS en_circulacion, (SELECT COUNT(*) FROM ejemplares JOIN estado_ejemplar ON ejemplares.estado = estado_ejemplar.id WHERE estado_ejemplar.id = 3 AND ejemplares.isbn_copia = libros.isbn) AS copias_danadas FROM libros JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro JOIN autores ON libro_autores.id_autor = autores.id JOIN editorial ON libros.editorial = editorial.id JOIN libro_categoria ON libros.isbn = libro_categoria.isbn_libro JOIN categorias ON libro_categoria.id_categoria = categorias.id WHERE libros.isbn = ? GROUP BY libros.isbn ORDER BY libros.isbn;";
    $stmt = $conexion->prepare($query);
    // Aplicamos el filtro para ISBN similar, si es necesario
    $stmt->bind_param('s', $isbn);  // 's' indica que el parámetro es de tipo string
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si se encontró el libro
    $book = $resultado->fetch_assoc();

    if ($book) {
        // Devolver los detalles como respuesta en formato JSON
        echo json_encode($book);
    } else {
        echo json_encode(['error' => 'Libro no encontrado']);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conexion->close();
}


//para modal detalles de volumenes
if (isset($_GET["isbnVolumen"])) {

    // Obtener el valor ISBN desde el parámetro GET o desde el input oculto en el frontend
    $isbn = $_GET['isbnVolumen'];

    // Consultar la base de datos para obtener los detalles del libro con ese ISBN
    $query = "SELECT 
    CONCAT(volumen.isbn_vol, ' (Volumen ', volumen.numero, ')') AS volID, 
    CONCAT(libros.titulo, ' \"', volumen.nombre, '\"') AS titulo, 
    volumen.numero, 
    volumen.anio, 
    CONCAT(libros.isbn, ' (Obra completa)') AS isbn, 
    GROUP_CONCAT(DISTINCT CONCAT(autores.nombre, ' ', autores.apellido) SEPARATOR ', ') AS autores 
FROM libros 
JOIN libro_autores ON libros.isbn = libro_autores.isbn_libro 
JOIN autores ON libro_autores.id_autor = autores.id 
JOIN ejemplares ON libros.isbn = ejemplares.isbn_copia 
JOIN volumen ON volumen.id = ejemplares.isbn_vol  -- Asegurar que esta relación sea correcta
WHERE libros.isbn = ? 
GROUP BY volumen.id  -- Agrupar por volumen para evitar datos incorrectos
ORDER BY libros.isbn, volumen.numero;";


    $stmt = $conexion->prepare($query);
    // Aplicamos el filtro para ISBN similar, si es necesario
    $stmt->bind_param('s', $isbn);  // 's' indica que el parámetro es de tipo string
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Verificar si se encontró el libro
    $book = $resultado->fetch_assoc();

    if ($book) {
        // Devolver los detalles como respuesta en formato JSON
        echo json_encode($book);
    } else {
        echo json_encode(['error' => 'Libro no encontrado']);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conexion->close();
}
















//Para agregar un nuevo ejemplar dinamicamente
if (isset($_GET["cota"]) && isset($_GET["fuente"]) && isset($_GET["isbnDetalles"]) && isset($_GET["titulo"])) {



    // Obtener el valor ISBN desde el parámetro GET o desde el input oculto en el frontend
    $cota = $_GET['cota'];
    $condicion = 1;
    $isbn = $_GET['isbnDetalles'];
    $titulo = $_GET["titulo"];
    $fuente = $_GET["fuente"];

    // Consultar la base de datos para obtener los detalles del libro con ese ISBN
    $query = "INSERT INTO ejemplares (isbn_copia, cota, estado, delete_at, fuente_suministro, isbn_vol, ubicacion) VALUES (?, ?, ?, 1, ?, NULL, 1)";
    $stmt = $conexion->prepare($query);
    // Aplicamos el filtro para ISBN similar, si es necesario
    $stmt->bind_param('ssii', $isbn, $cota, $condicion, $fuente);  // 's' indica que el parámetro es de tipo string
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        $historial = new Historial($conexion);
        $suministrador = $fuente == 1 ? "Red de bibliotecas" : "Donación";
        $detalles = "Titulo: {$titulo}. ISBN: {$isbn}. Cota: {$cota}. Suministrado mediante: {$suministrador}";
        $accion = 2; // Accion 2 es registro de ejemplar de libro
        $fechaActual = date('Y-m-d');
        $responsable = $_SESSION["cedula"];
        if ($historial->registrar_accion($responsable, $detalles, $fechaActual, $accion)) {
            echo json_encode(["success" => "Ejemplar añadido"]);
        } else {
            echo json_encode(["error" => "No se pudo registrar en el historial"]);
        }
    } else {
        echo json_encode(["error" => "No se pudo añadir el ejemplar"]);
    }

    $stmt->close();
    $conexion->close();
}

//Para desincorporar un ejemplar dinamicamente
if (isset($_GET["cotaDeshabilitar"]) && isset($_GET["isbnDeshabilitar"]) && isset($_GET["tituloDeshabilitar"])) {



    // Obtener el valor ISBN desde el parámetro GET o desde el input oculto en el frontend
    $idCota = $_GET['cotaDeshabilitar'];
    $isbn = $_GET['isbnDeshabilitar'];
    $titulo = $_GET["tituloDeshabilitar"];

    // Consultar la base de datos para obtener los detalles del libro con ese ISBN
    $query = "UPDATE ejemplares SET delete_at = 0, estado = 2 WHERE id = ?";

    $stmt = $conexion->prepare($query);
    // Aplicamos el filtro para ISBN similar, si es necesario
    $stmt->bind_param('s', $idCota);  // 's' indica que el parámetro es de tipo string
    $stmt->execute();

    //echo "$query<br>$cota<br>$titulo<br>$isbn";

    if ($stmt->affected_rows > 0) {
        $historial = new Historial($conexion);
        $detalles = "Titulo: {$titulo}, ISBN: {$isbn}, Cota: {$idCota}";
        $accion = 5; // Accion 5 es desincorporacion de ejemplar de libro
        $fechaActual = date('Y-m-d');
        $responsable = $_SESSION["cedula"];
        if ($historial->registrar_accion($responsable, $detalles, $fechaActual, $accion)) {
            echo json_encode(["success" => "Ejemplar desincorporado"]);
        } else {
            echo json_encode(["error" => "No se pudo registrar en el historial"]);
        }
    } else {
        echo json_encode(["error" => "No se pudo desincorporar el ejemplar"]);
    }

    $stmt->close();
    $conexion->close();
}
