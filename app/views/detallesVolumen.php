<?php
// Configuración para mostrar errores (solo para desarrollo)
error_reporting(E_ALL); // Reportar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla

if (session_status() == PHP_SESSION_NONE) {
    // La sesión no está activa, podemos iniciarla
    session_start();
    //echo "Sesión iniciada correctamente.";
  } else if (session_status() == PHP_SESSION_ACTIVE) {
    // La sesión ya está activa, no es necesario iniciarla
   // echo "La sesión ya está activa.";
  } else if (session_status() == PHP_SESSION_DISABLED){
    //echo "Las sesiones estan deshabilitadas";
  }
  
  $rol = $_SESSION["rol"];



try {
    // Incluir la configuración de la base de datos
    include "app/config/database.php";
$conexion = conexion();
    // Verificar si la conexión a la base de datos se estableció correctamente
    if (!isset($conexion) || !$conexion) {
        throw new Exception("Error de conexión: No se pudo conectar a la base de datos. Revisa la configuración.");
    }

    // Obtener el ID del volumen desde la URL, con un valor predeterminado de 1
    $volumeId = $_GET['volumeId'] ?? 1;

    // Validar que el volumeId sea un número entero válido
    if (!filter_var($volumeId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        throw new Exception("Error de validación: El ID del volumen no es válido. Debe ser un número entero mayor o igual a 1.");
    }

    // Consulta SQL para obtener los detalles del volumen (primera consulta)
    $query = "SELECT l.titulo, v.id, v.extension,
                     (SELECT COUNT(*) FROM ejemplares JOIN volumen ON ejemplares.isbn_vol = volumen.id WHERE volumen.id = ? AND ejemplares.delete_at = 1 AND volumen.id = ejemplares.isbn_vol) AS copias, 
                     (SELECT COUNT(*) FROM prestamos JOIN ejemplares ON prestamos.cota = ejemplares.id WHERE prestamos.estado = 1 AND ejemplares.isbn_vol = v.id AND ejemplares.delete_at = 1 ) AS circulacion, 
                     v.isbn_obra, v.isbn_vol, v.numero, v.nombre, v.anio, v.portada, 
                     GROUP_CONCAT(DISTINCT CONCAT(a.nombre) SEPARATOR ', ') AS autores, 
                     e.nombre AS editorial, l.edicion, 
                     GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ', ') AS categorias, 
                     ej.cota as ddc, CONCAT('Estante ', ub.numero, ', seccion de ', ub.seccion) AS ubicacion
              FROM volumen v 
              JOIN libros l ON v.isbn_obra = l.isbn 
              JOIN libro_autores la ON l.isbn = la.isbn_libro 
              JOIN autores a ON la.id_autor = a.id 
              JOIN editorial e ON e.id = l.editorial 
              JOIN libro_categoria lc ON l.isbn = lc.isbn_libro 
              JOIN categorias c ON lc.id_categoria = c.id 
              JOIN ejemplares ej ON ej.isbn_copia = l.isbn 
              JOIN ubicacion_ejemplares ub ON ej.ubicacion = ub.id
              WHERE v.id = ? AND ej.isbn_vol = v.id AND ej.delete_at = 1;";

    // Preparar la primera consulta
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        throw new Exception("Error en la consulta SQL: No se pudo preparar la consulta. Detalles: " . $conexion->error);
    }

    // Vincular parámetros y ejecutar la primera consulta
    $stmt->bind_param("ii", $volumeId, $volumeId);
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    // Obtener resultados de la primera consulta
    $resultado = $stmt->get_result();
    if (!$resultado) {
        throw new Exception("Error al obtener resultados: " . $stmt->error);
    }

    // Verificar si se encontraron resultados
    $fila = $resultado->fetch_assoc();
    if ($fila["id"] == NULL) {
        // Si no hay resultados, ejecutar la segunda consulta
        //Si no hay resultados significa que no hay copias!
        $query2 = "SELECT l.titulo, v.id, v.extension, 
                          (SELECT COUNT(*) FROM ejemplares JOIN volumen ON ejemplares.isbn_vol = volumen.id WHERE volumen.id = ? AND ejemplares.delete_at = 1) AS copias, 
                          (SELECT COUNT(*) FROM prestamos JOIN ejemplares ON prestamos.cota = ejemplares.id WHERE prestamos.estado = 1 AND ejemplares.isbn_vol = v.id AND ejemplares.delete_at = 1) AS circulacion, 
                          v.isbn_obra, v.isbn_vol, v.numero, v.nombre, v.anio, v.portada, 
                          GROUP_CONCAT(DISTINCT CONCAT(a.nombre) SEPARATOR ', ') AS autores, 
                          e.nombre AS editorial, l.edicion, 
                          GROUP_CONCAT(DISTINCT c.nombre SEPARATOR ', ') AS categorias, 
                          l.cota as ddc 
                   FROM volumen v 
                   JOIN libros l ON v.isbn_obra = l.isbn 
                   JOIN libro_autores la ON l.isbn = la.isbn_libro 
                   JOIN autores a ON la.id_autor = a.id 
                   JOIN editorial e ON e.id = l.editorial 
                   JOIN libro_categoria lc ON l.isbn = lc.isbn_libro 
                   JOIN categorias c ON lc.id_categoria = c.id 
                   WHERE v.id = ?";

        // Preparar la segunda consulta
        $stmt2 = $conexion->prepare($query2);
        if (!$stmt2) {
            throw new Exception("Error en la consulta SQL: No se pudo preparar la segunda consulta. Detalles: " . $conexion->error);
        }

        // Vincular parámetros y ejecutar la segunda consulta
        $stmt2->bind_param("ii", $volumeId, $volumeId);
        if (!$stmt2->execute()) {
            throw new Exception("Error al ejecutar la segunda consulta: " . $stmt2->error);
        }

        // Obtener resultados de la segunda consulta
        $resultado = $stmt2->get_result();
        if (!$resultado) {
            throw new Exception("Error al obtener resultados de la segunda consulta: " . $stmt2->error);
        }

        // Verificar si se encontraron resultados en la segunda consulta
        $fila = $resultado->fetch_assoc();
        if (!$fila) {
            throw new Exception("Error: No se encontró el volumen con el ID proporcionado.");
        }

        // Cerrar la segunda declaración
        $stmt2->close();
    }

    // Cerrar la primera declaración
    $stmt->close();

    // Procesar los datos del volumen
    $volumeData = [
        "id" => $fila["id"],
        "isbn_obra" => $fila['isbn_obra'],
        "isbn_vol" => $fila['isbn_vol'],
        "numero" => numeroARomano($fila['numero']),
        "titulo" => $fila['nombre'],
        "tituloObra" => $fila['titulo'],
        "anio" => $fila['anio'],
        "localizacion" => "estante",
        "portada" => $fila['portada'],
        "autores" => $fila['autores'],
        "editorial" => $fila['editorial'],
        "edicion" => $fila['edicion'],
        "ddc" => $fila['ddc'],
        "ubicacion" => $fila['ubicacion'] ?? NULL,
        "copias" => $fila['copias'] ?? 0,
        "circulacion" => $fila['circulacion'] ?? 0,
        "disponibles" => ($fila['copias'] ?? 0) - ($fila['circulacion'] ?? 0),
        "categorias" => $fila['categorias'] ?? "No se especificaron categorias para este libro.",
        "extension" => $fila['extension'] ?? "No se tiene descripcion de este libro."
    ];
    //para el modal de desincorporar ejemplar
    $ddcDes = $volumeData["ddc"];
    $isbnVolDes = $volumeData["isbn_vol"];
    $tituloDes = $volumeData["titulo"];
} catch (Exception $e) {
    // Mostrar el tipo de error y el mensaje específico
    die("<strong>¡Error!</strong><br><br>
        <strong>Tipo de error:</strong> " . get_class($e) . "<br>
        <strong>Mensaje:</strong> " . $e->getMessage() . "<br>
        <strong>Archivo:</strong> " . $e->getFile() . "<br>
        <strong>Línea:</strong> " . $e->getLine());
}

// Función para convertir números a romanos
function numeroARomano($numero) {
    $valores = [
        1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
        100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
        10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'
    ];

    $resultado = '';

    foreach ($valores as $valor => $romano) {
        while ($numero >= $valor) {
            $resultado .= $romano;
            $numero -= $valor;
        }
    }

    return $resultado;
}
?>
<div id="detallesVolumenDiv">

    <div id="detallesVolumenBody">
        <div class="book-details-container">
            <div class="book-header">
                <div class="book-image">
                    <img src="<?php echo $volumeData['portada'] ?>" alt="Volume Image" id="volume-image">
                    <div class="copias-info">
                    <p id="copiasVolP"><strong>Copias:</strong> <?php echo $volumeData['copias'] ?></p>
                    <p id="circulacionVolP"><strong>En Circulación:</strong> <?php echo $volumeData['circulacion'] ?></p>
                    <p id="disponiblesVolP"><strong>Disponibles:</strong> <?php echo $volumeData['disponibles'] ?></p>
                    </div>
                
                </div>
                <div class="book-info">
                    <h1 id="volume-title"><?php echo $volumeData['tituloObra'] . " " . $volumeData["numero"]; ?>
                    </h1>
                    <p>"<strong><?php echo $volumeData['titulo'] ?></strong>"</p>
                    <p class="author"><strong>Por:</strong> <?php echo $volumeData['autores'] ?></p>
                    <p><strong>Editorial:</strong> <?php echo $volumeData['editorial'] ?></p>
                    <p><strong>Edición:</strong> <?php echo $volumeData['edicion'] ?></p>
                    <p><strong>Extensión:</strong> <?php echo $volumeData['extension'] ?></p>
                    <p><strong>ISBN (Obra completa):</strong> <?php echo $volumeData['isbn_obra'] ?></p>
                    <p><strong>ISBN (Volumen):</strong> <?php echo $volumeData['isbn_vol'] ?></p>
                    <p><strong>Categorías:</strong> <?php echo $volumeData['categorias'] ?></p>
                    <!--p><strong>Clasificación DDC:</strong> <?php //echo preg_replace('/\.e.*/', '', $volumeData["ddc"]) ?></p-->
                </div>
            </div>

            
            <?php 
            //display dependiendo del rol
            $displayRol = $rol !== 'Bibliotecario' ? 'display: flex;' : "display: block;"; ?>
            <div class="volume-info" style="<?= $displayRol ?>; align-items: center; justify-content: flex-end;">
                <?php
                
                echo $volumeData["ubicacion"] != NULL ? "<p style='margin: 0;'><strong>Localización actual: </strong>". $volumeData['ubicacion'] ."</p>" : "";
                if ($volumeData["copias"] > 0 && $rol !== 'Bibliotecario'){
                    echo <<<HTML
                    <button class="btn btn-danger" style="margin-left: auto;" data-bs-toggle="modal" data-bs-target="#desincorporarModal">Desincorporar ejemplar.</button>
                    HTML;
                }
                ?>
                
                
                
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="public/css/detallesVolumen.css">
<?php
include_once "modal/desincorporarEjemplar.php";
?>