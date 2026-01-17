<?php
require_once '../../model/libros.php';
require_once '../../model/historial.php';
require_once '../../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = conexion();
    $libroModel = new Libros();
    $historial = new Historial($conn);

    // Validar y sanitizar entrada
    $isbn = trim($_POST['isbn'] ?? "");
    $isbn_serie = trim($_POST['isbn_serie'] ?? "");
    $titulo = trim($_POST['titulo'] ?? "");
    $anio = $_POST['fecha_publicacion'] ?? "";
    $edicion = trim($_POST['mencion_edicion'] ?? "");
    $autores = $_POST['autor'] ?? [];
    $editorial = trim($_POST['editorial'] ?? "");
    $categorias = $_POST['categoria'] ?? [];
    $fechaActual = date('Y-m-d');

    $forma_contenido = $_POST['forma_contenido'] ?? '';
    $calificacion_contenido = $_POST['calificacion_contenido'] ?? '';
    $tipo_de_medio = $_POST['tipo_de_medio'] ?? '';
    $titulo_paralelo = $_POST['titulo_paralelo'] ?? '';
    $extension = $_POST['extension'] ?? '';
    $detalles_fisicos = $_POST['detalles_fisicos'] ?? '';
    $dimensiones = $_POST['dimensiones'] ?? '';
    $titulo_serie = $_POST['titulo_serie'] ?? '';
    $notas = $_POST['notas'] ?? '';
    $cota = $_POST['cota'] ?? '';
    $numeracion_serie = $_POST['numeracion_serie'] ?? '';
    $lugar_publicacion = $_POST["lugar_publicacion"] ?? '';
    $checkLugarPublicacion = $_POST["checkLugarPublicacion"] ?? 0;

    //manejo de ejemplares

    $estanteriaId = $_POST["estanteria"];
    $filaId = $_POST["fila"];
    $cantidad = $_POST["cantidad"];

    // Manejo de portada
    $portada = 'public/img/libros/default.jpg';
    if (!empty($_FILES['portada']['name'])) {
        
        $directorio_libros = '../../../public/img/libros/';
        $nombre_archivo = time() . '_' . basename($_FILES['portada']['name']);
        $ruta_archivo = $directorio_libros . $nombre_archivo;

        if (move_uploaded_file($_FILES['portada']['tmp_name'], $ruta_archivo)) {
            $portada = 'public/img/libros/' . $nombre_archivo;
            //echo $portada;
        }
    }

    //portada del volumen
    $portadaVol = 'public/img/volumenes/default.jpg';
    if (!empty($_FILES['portada_volumen']['name'])) {
        
        $directorio_libros = '../../../public/img/volumenes/';
        $nombre_archivo = time() . '_' . basename($_FILES['portada_volumen']['name']);
        $ruta_archivo = $directorio_libros . $nombre_archivo;

        if (move_uploaded_file($_FILES['portada_volumen']['tmp_name'], $ruta_archivo)) {
            $portadaVol = 'public/img/volumenes/' . $nombre_archivo;
            //echo $portada;
        }
    }
    //echo "segunda vez $portada";


    $datos = [
        "isbn_obra" => $conn->real_escape_string($isbn),
        "isbn_vol" => !empty($isbn_serie) ? $conn->real_escape_string($isbn_serie) : $conn->real_escape_string($isbn),
        "numero" => $conn->real_escape_string($numeracion_serie),
        "nombre" => $conn->real_escape_string($titulo),
        "anio" => $conn->real_escape_string($anio),
        "portada" => $titulo_serie != "" ? $conn->real_escape_string($portadaVol) : $conn->real_escape_string($portada),
        "extension" => $conn->real_escape_string($extension)
    ];
    




    if ($libroModel->RegistrarLibro($isbn, $titulo, $anio, $edicion, $autores, $editorial, $categorias, $fechaActual, $conn, $portada, $forma_contenido, $calificacion_contenido, $tipo_de_medio, $titulo_paralelo, $extension, $detalles_fisicos, $dimensiones, $titulo_serie, $notas, $cota, $lugar_publicacion, $checkLugarPublicacion)) {

        //Insertar volumen por defecto de esa obra
            $idVol = $libroModel->ejecutarConsulta("INSERT", "volumen", $datos, "", $conn);
        
        //insertar ejemplares

          // Obtener cantidad de ejemplares existentes
          $sql = "SELECT COUNT(*) AS totalEjemplares FROM ejemplares WHERE isbn_copia = ?";
          $stmt = $conn->prepare($sql);
          if (!$stmt) {
              throw new Exception("Error en la preparación de la consulta COUNT.");
          }
  
          $stmt->bind_param("s", $isbn);
          $stmt->execute();
          $resultado = $stmt->get_result();
          $fila = $resultado->fetch_assoc();
          $totalEjemplares = ($fila && $fila["totalEjemplares"] > 0) ? intval($fila["totalEjemplares"]) : 1;
  
          // Preparar la consulta de inserción una vez
          $sql = "INSERT INTO ejemplares (isbn_copia, isbn_vol, cota, filaID) VALUES (?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          if (!$stmt) {
              throw new Exception("Error en la preparación de la consulta INSERT.");
          }
  
          // Insertar múltiples ejemplares con una sola preparación
          for ($i = 0; $i < $cantidad; $i++) {
              $cota_formateada = "{$cota}.e{$totalEjemplares}";
              $stmt->bind_param("sssi", $isbn, $idVol, $cota_formateada, $filaId);
              $stmt->execute();
              $totalEjemplares++;
          }

        


        $usuarioResponsable = $_SESSION['cedula'] ?? 'Usuario desconocido';
        $autorConcat = obtenerNombresAutores($autores, $conn);
        $detalles = sprintf('Título: "%s", Autor: "%s", ISBN: "%s".', $titulo, $autorConcat ?: 'Desconocido', $isbn);
        $hoy = Date('Y-m-d');
        $accion = 1; //Registro de libro

        $historial->registrar_accion($usuarioResponsable, $detalles, $hoy, $accion);

        header("Location: ../../../index.php?vista=fichaLibro&isbn=$isbn&toast=success&mensaje=Libro registrado exitosamente.");
        exit;
    } else {
        header("Location: ../../../index.php?vista=libros&toast=error&mensaje=Error al registrar libro. Intente nuevamente.");
        exit;
    }
}

function obtenerNombresAutores(array $autorIds, $conn)
{
    if (empty($autorIds))
        return null;
    $autorIdsEscapados = array_map('intval', $autorIds);
    $idsConcatenados = implode(',', $autorIdsEscapados);

    $query = "SELECT nombre FROM autores WHERE id IN ($idsConcatenados)";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $autores = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $autores[] = $row['nombre'] . " " . $row['apellido'];
        }
        return implode(', ', $autores);
    }
    return null;
}
