<?php

require_once '../../config/database.php';
require_once '../../model/volumenes.php';
require_once '../../model/historial.php';

$conn = conexion();
$volModel = new Volumenes();
$historial = new Historial($conn);
session_start();
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulos = $_POST['titulo_vol_serie'] ?? [];
    $numeraciones = $_POST['numeracion_serie'] ?? [];
    $isbn_series = $_POST['isbn_serie'] ?? [];
    $isbn_completos = $_POST['isbn_completo'] ?? [];
    $portadas = $_FILES['portada_volumen'] ?? [];
    $anio = $_POST['anio_publicacion'] ?? [];
    $extension = $_POST['extension'] ?? [];
    
    foreach ($titulos as $index => $titulo) {
        $isbn = $isbn_completos[$index] ?? '';
        $isbn_serie = $isbn_series[$index] ?? '';
        $numeracion_serie = $numeraciones[$index] ?? '';
        $extension_serie = $extension[$index] ?? '';
        $anio_serie = $anio[$index] ?? 'Desconocido';
        
        // Procesar la portada
        $portadaVol = "public/img/volumenes/default.jpg"; // Por defecto
        
        if (!empty($portadas['name'][$index])) {
       

            $uploadDir = "../../../public/img/volumenes/";
            $fileName = time() . "_" . basename($portadas['name'][$index]);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($portadas['tmp_name'][$index], $uploadFile)) {
                $portadaVol = 'public/img/volumenes/' . $fileName;
            }
           
        }

        $datos = [
            "isbn_obra" => $conn->real_escape_string($isbn),
            "isbn_vol" => !empty($isbn_serie) ? $conn->real_escape_string($isbn_serie) : $conn->real_escape_string($isbn),
            "numero" => $conn->real_escape_string($numeracion_serie),
            "nombre" => $conn->real_escape_string($titulo),
            "anio" => $conn->real_escape_string($anio_serie), // Si necesitas otro valor, cámbialo aquí
            "portada" => $conn->real_escape_string($portadaVol),
            "extension" =>  $conn->real_escape_string($extension_serie)
        ];
        
        if ($volModel->ejecutarConsulta("INSERT", "volumen", $datos, "", $conn)){
        $usuarioResponsable = $_SESSION['cedula'] ?? 'Usuario desconocido';
        $detalles = sprintf('Título: "%s", ISBN (obra completa): "%s", ISBN (volumen): "%s".', $datos["nombre"], $datos["isbn_obra"], $datos["isbn_vol"]);
        $hoy = Date('Y-m-d');
        $accion = 17; //Registro de volumen

        $historial->registrar_accion($usuarioResponsable, $detalles, $hoy, $accion);
        }
    }
    
    header("Location: ../../../index.php?vista=fichaLibro&isbn=" . $datos['isbn_obra'] . "&toast=success&mensaje=Volumen registrado correctamente.");

} else {
    header("Location: ../../../index.php?vista=registrar_volumen&toast=error&mensaje=El volumen no ha podido ser registrado. Por favor, intente nuevamente.");

}

$conn->close();
?>