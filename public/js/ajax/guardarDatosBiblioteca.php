<?php

include '../../../app/config/database.php';
$conn = conexion();

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $nombreBiblioteca = $_POST['nombreBiblioteca'] ?? '';
    $abreviacionDeNombreBiblioteca = $_POST['abreviacionBiblioteca'] ?? '';
    $direccionBiblioteca = $_POST['direccionBiblioteca'] ?? '';
    $telefonoBiblioteca = $_POST['telefonoBiblioteca'] ?? '';
    $correo = $_POST['emailBiblioteca'] ?? '';

    // Manejo de logo
    $logo = $_SESSION["datos_biblioteca"]["logo"] ?? 'public/img/libros/default.jpg';
    if (!empty($_FILES['logoBiblioteca']['name'])) {
        
        $directorio_logo = '../../../public/img/logo/';
        $nombre_archivo = time() . '_' . basename($_FILES['logoBiblioteca']['name']);
        $ruta_archivo = $directorio_logo . $nombre_archivo;

        if (move_uploaded_file($_FILES['logoBiblioteca']['tmp_name'], $ruta_archivo)) {
            $logo = 'public/img/logo/' . $nombre_archivo;
            //echo $portada;
        }
    }

    $sql = "UPDATE `datos_biblioteca` SET `nombre`= ?, `abreviacion`= ?,`logo`= ?,`direccion`= ?,`telefono`= ?,`correo`= ? WHERE id = 1";

    //$sql = "INSERT INTO regla_de_circulacion (periodo_prestamo, unidades, periodo_renovaciones, renovaciones_permitidas, peticiones_permitidas, peticiones_diarias, peticiones_por_registro) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombreBiblioteca, $abreviacionDeNombreBiblioteca, $logo, $direccionBiblioteca, $telefonoBiblioteca, $correo);
    
    if ($stmt->execute()){


        $_SESSION["datos_biblioteca"] = [
            "nombre" => $nombreBiblioteca ?? "",
            "abreviacion" => $abreviacionDeNombreBiblioteca ?? "",
            "logo" => $logo ?? "",
            "direccion" => $direccionBiblioteca ?? "",
            "telefono" => $telefonoBiblioteca ?? "",
            "correo" => $correo ?? ""
        ];




        echo json_encode(["success" => true, "message" => "Datos actualizados correctamente."]);
    } else {
        echo json_encode(["succes" => false, "message" => "No se han podido insertar los datos."]);
    }

    $stmt->close();

} else {
    echo json_encode(["succes" => false, "message" => "Metodo no permitido."]);
}