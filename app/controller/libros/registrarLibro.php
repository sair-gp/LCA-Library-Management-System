<?php
require_once '../../config/database.php'; // Asegúrate de incluir la conexión a la base de datos
require_once '../../model/libros.php'; // Modelo para registrar libros y volúmenes

$conexion = conexion();

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

    // 📌 Directorios de almacenamiento
    $directorio_libros = '../../../public/img/libros/';
    $directorio_volumenes = '../../../public/img/libros/volumenes/';

    // 📌 Procesar portada del libro
    $portada = 'default.jpg'; // Imagen por defecto
    if (!empty($_FILES['portada']['name'])) {
        $nombre_archivo = time() . '_' . basename($_FILES['portada']['name']); // Evita nombres duplicados
        $ruta_archivo = $directorio_libros . $nombre_archivo;

        if (move_uploaded_file($_FILES['portada']['tmp_name'], $ruta_archivo)) {
            $portada = 'public/img/libros/' . $nombre_archivo; // Guarda la ruta completa en BD
        }
    }

    // 📌 Array para almacenar los volúmenes
    $volumenes = [];

    if (isset($_POST['volumen_isbn'], $_POST['volumen_titulo'], $_POST['volumen_numero'], $_POST['volumen_anio'], $_FILES['volumen_portada']['tmp_name'])) {
        $isbn_volumenes = $_POST['volumen_isbn'];
        $titulos_volumenes = $_POST['volumen_titulo'];
        $numeros_volumenes = $_POST['volumen_numero'];
        $anios_volumenes = $_POST['volumen_anio'];

        $portadas_volumenes = [];

        if (!empty($_FILES['volumen_portada']['name'][0])) {
            foreach ($_FILES['volumen_portada']['name'] as $key => $nombre) {
                $nombre_archivo = time() . '_' . basename($nombre); // Nombre único
                $ruta_archivo = $directorio_volumenes . $nombre_archivo;

                if (move_uploaded_file($_FILES['volumen_portada']['tmp_name'][$key], $ruta_archivo)) {
                    $portadas_volumenes[$key] = 'public/img/libros/volumenes/' . $nombre_archivo; // Ruta completa
                } else {
                    $portadas_volumenes[$key] = 'public/img/libros/volumenes/default.jpg';
                }
            }
        } else {
            $portadas_volumenes = array_fill(0, count($_POST['volumen_isbn']), 'public/img/libros/volumenes/default.jpg');
        }

        foreach ($isbn_volumenes as $key => $isbn) {
            $volumenes[] = [
                'isbn' => $isbn,
                'titulo' => $titulos_volumenes[$key],
                'numero' => $numeros_volumenes[$key],
                'anio' => $anios_volumenes[$key],
                'portada' => $portadas_volumenes[$key] ?? 'public/img/libros/volumenes/default.jpg'
            ];
        }
    }


    $hoy = date("Y-m-d");

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Datos del Libro</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 20px;
                padding: 20px;
            }
            .container {
                max-width: 800px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #333;
                text-align: center;
            }
            .details {
                margin-bottom: 20px;
                border-bottom: 2px solid #ddd;
                padding-bottom: 10px;
            }
            .details p {
                margin: 5px 0;
            }
            .volumenes {
                margin-top: 20px;
            }
            .volumen {
                background: #f9f9f9;
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 5px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .volumen img {
                max-width: 100px;
                display: block;
                margin-top: 10px;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
    
    <div class="container">
        <h2>Datos del Libro</h2>
        
        <div class="details">
            <p><strong>Título:</strong> {$titulo}</p>
            <p><strong>Autor:</strong>{$ruta_archivo}</p>
            <p><strong>Editorial:</strong> {$editorial}</p>
            <p><strong>Año de Publicación:</strong> {$anio_publicacion}</p>
            <p><strong>ISBN General:</strong> {$isbn_general}</p>
            <p><strong>Categoría:</strong></p>
            <p><strong>Cota (DDC):</strong> {$cota}</p>
            <p><strong>Edición:</strong> {$edicion}</p>
            <p><strong>Ejemplares:</strong> {$ejemplares}</p>
            <p><strong>Fecha de Registro:</strong> {$hoy}</p>
            <p><strong>Portada: {$portada}</strong></p>
            <img src="../../../public/img/libros/{$portada}" alt="Portada del libro">
        </div>
    HTML;

    if (!empty($volumenes)) {
        echo "<h2>Volúmenes</h2><div class='volumenes'>";
        foreach ($volumenes as $volumen) {
            echo <<<HTML
            <div class="volumen">
                <p><strong>ISBN:</strong> {$volumen['isbn']}</p>
                <p><strong>Título:</strong> {$volumen['titulo']}</p>
                <p><strong>Número:</strong> {$volumen['numero']}</p>
                <p><strong>Año:</strong> {$volumen['anio']}</p>
                <p><strong>Portada:</strong></p>
                <img src="../../../public/img/libros/volumenes/{$volumen['portada']}" alt="Portada del volumen">
            </div>
    HTML;
        }
        echo "</div>";
    }

    echo "</div></body></html>";









    $modeloLibro = new Libros();

    //if (!empty($volumenes)) {
    //se registra el libro con sus volumenes
    /*$resultado = $modeloLibro->RegistrarLibroVolumenEjemplar(
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
        );*/
    //} else {
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
        $conexion,
        $portada

    );
    //    }

    if ($resultado && !empty($volumenes)) {
        $sql = "INSERT INTO volumen (isbn_vol, isbn_obra, nombre, numero, anio, portada) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);

        foreach ($volumenes as $volumen) {
            $stmt->bind_param(
                "ssssss",
                $volumen['isbn'],
                $isbn_general, // Relacionado con la obra principal
                $volumen['titulo'],
                $volumen['numero'],
                $volumen['anio'],
                $volumen['portada']
            );

            if (!$stmt->execute()) {
                echo "Error al insertar el volumen: " . $stmt->error;
            } else {
                echo "fino volumen";
            }
        }
        $stmt->close();
    }



    if ($resultado) {
        echo "fino";
    } else {
        echo "no fino";
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}










/*

function obtenerTotal($conexion, $tabla) {
    $sql = "SELECT COUNT(*) AS total FROM $tabla;";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $resultado['total'];
}

$total_libros = obtenerTotal($conexion, 'libros');
$total_categorias = obtenerTotal($conexion, 'categorias');
$total_asistencias_hoy = obtenerTotal($conexion, 'asistencias WHERE DATE(fecha) = CURDATE()');
$total_prestamos = obtenerTotal($conexion, 'prestamos WHERE estado = 1 OR estado = 4');


// Obtener libros populares
$sql = "SELECT l.isbn, l.titulo, l.portada, l.anio, e.nombre AS editorial
        FROM libros l
        LEFT JOIN prestamos p ON p.cota IN (SELECT id FROM ejemplares WHERE isbn_copia = l.isbn)
        LEFT JOIN editorial e ON l.editorial = e.id
        GROUP BY l.isbn
        ORDER BY COUNT(p.id) DESC
        LIMIT 10";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$libros_populares = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

<section class="popular-books">
            <h2>Libros Populares</h2>
            <div class="carousel">
                <?php foreach ($libros_populares as $libro): ?>
                    <div class="carousel-item">
                        <img src="<?php echo $libro['portada']; ?>" alt="<?php echo $libro['titulo']; ?>">
                        <h3><?php echo $libro['titulo']; ?></h3>
                        <p><?php echo $libro['editorial']; ?> (<?php echo $libro['anio']; ?>)</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>




        .popular-books { text-align: center; margin-top: 20px; }
        .carousel { display: flex; justify-content: center; overflow: hidden; }
        .carousel-item { display: none; text-align: center; }
        .carousel-item img { width: 150px; border-radius: 5px; }
        .stats { text-align: center; margin-top: 20px; }