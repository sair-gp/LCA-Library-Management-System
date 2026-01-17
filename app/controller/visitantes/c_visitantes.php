<?php
include '../../model/visitantes.php';
include '../../config/database.php';

$conn = conexion();
$visitante = new visitantes();

if (isset($_POST['cedulaVisitante']) && isset($_POST['nombreVisitante']) && isset($_POST['numeroVisitante']) && isset($_POST['dirVisitante']) && isset($_POST['sexoVisitante'])) {

    $cedula = $_POST['cedulaVisitante'];
    $nombre = $_POST['nombreVisitante'];
    $telefono = $_POST['prefijoVisitante'] . $_POST['numeroVisitante'];
    $direccion = $_POST['dirVisitante'];
    $sexo = $_POST['sexoVisitante'];
    $correo = $_POST['correoVisitante'];
    $hoy = Date("Y-m-d");
    
    // Procesar la imagen si fue subida
    $fotoNombre = null;
    if (isset($_FILES['fotoVisitante']) && $_FILES['fotoVisitante']['error'] === UPLOAD_ERR_OK) {
        // Obtener información del archivo
        $fotoNombre = 'public/img/visitantes/default.jpg';
    if (!empty($_FILES['fotoVisitante']['name'])) {
        
        $directorio_pfp = '../../../public/img/visitantes/';
        $nombre_archivo = time() . '_' . basename($_FILES['fotoVisitante']['name']);
        $ruta_archivo = $directorio_pfp . $nombre_archivo;

        if (move_uploaded_file($_FILES['fotoVisitante']['tmp_name'], $ruta_archivo)) {
            $fotoNombre = 'public/img/visitantes/' . $nombre_archivo;
            //echo $portada;
        }
    }

    }
    
    if ($visitante->agregarVisitante($cedula, $nombre, $telefono, $direccion, $sexo, $conn, $hoy, $fotoNombre, $correo)) {
        header("Location: ../../../index.php?vista=visitantes&toast=success&mensaje=Visitante registrado correctamente.");
    } else {
        // Si falla, eliminar la imagen subida (si existe)
        if ($fotoNombre) {
            @unlink($uploadDir . $fotoNombre);
        }
        header("Location: ../../../index.php?vista=visitantes&toast=error&mensaje=No se ha podido registrar al visitante. Por favor, intente nuevamente.");
    }
}