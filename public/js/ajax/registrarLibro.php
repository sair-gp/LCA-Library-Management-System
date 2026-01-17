<?php
require_once '../config/database.php'; // Asegúrate de incluir la conexión a la base de datos
require_once '../model/libros.php'; // Modelo para registrar libros y volúmenes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $editorial = $_POST['editorial'] ?? '';
    $anio_publicacion = $_POST['anio'] ?? '';
    $isbn_general = $_POST['isbn'] ?? '';
    $isbn_volumen = $_POST['isbn_volumen'] ?? '';
    $numero_volumen = $_POST['numero_volumen'] ?? '';
    $ejemplares = $_POST['ejemplares'] ?? '';
    $cota = $_POST['ddc'] ?? '';
    $edicion = $_POST['edicion'] ?? '';
    $categorias = $_POST['categoria'] ?? '';

    $portada = 'default_cover.jpg'; // Imagen por defecto
    if (!empty($_FILES['portada']['name'])) {
        $directorio_subida = '../uploads/portadas/';
        $nombre_archivo = basename($_FILES['portada']['name']);
        $ruta_archivo = $directorio_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['portada']['tmp_name'], $ruta_archivo)) {
            $portada = $nombre_archivo;
        }
    }

    // Array para almacenar los datos de los volúmenes
    $volumenes = [];

    // Obtener los datos de los volúmenes desde $_POST
    $isbn_volumenes = $_POST['volumen_isbn'];
    $titulos_volumenes = $_POST['volumen_titulo'];
    $numeros_volumenes = $_POST['volumen_numero'];
    $anios_volumenes = $_POST['volumen_anio'];
    $portadas_volumenes = $_FILES['volumen_portada']['tmp_name'];

    // Combinar los datos en un array asociativo por cada volumen
    foreach ($isbn_volumenes as $key => $isbn) {
        $volumen = [
            'isbn' => $isbn,
            'titulo' => $titulos_volumenes[$key],
            'numero' => $numeros_volumenes[$key],
            'anio' => $anios_volumenes[$key],
            'portada' => $portadas_volumenes[$key]
        ];

        // Agregar el volumen al array de volúmenes
        $volumenes[] = $volumen;
    }

    $hoy = date("Y-m-d");

    $modeloLibro = new Libros();

    if (!empty($volumenes)) {
        //se registra el libro con sus volumenes
        $resultado = $modeloLibro->RegistrarLibroVolumenEjemplar(
            $isbn_general,
            $titulo,
            $anio_publicacion,
            $edicion,
            $autor,
            $editorial,
            $categorias,
            $hoy,
            $conexion,
            $cota,
            $volumenes,
            $portada
        );
    } else {
        // Se registra solo el libro
        $resultado = $modeloLibro->RegistrarLibro(
            $isbn_general,
            $titulo,
            $anio_publicacion,
            $edicion,
            $autor,
            $editorial,
            $categorias,
            $hoy,
            $conexion

        );
    }


    if ($resultado) {
        echo json_encode(['status' => 'success', 'message' => 'Libro registrado correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar el libro.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
