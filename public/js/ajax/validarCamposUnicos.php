<?php
include '../../../app/config/database.php';

$conexion = conexion();

// Leer los datos JSON del cuerpo de la solicitud
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data["validarCedulaVisitante"])) {

    $cedulaVisitante = $data["validarCedulaVisitante"];

    $sql = "SELECT (SELECT v.cedula from visitantes v WHERE v.cedula = ?) AS vced, (SELECT cedula FROM usuarios u WHERE u.cedula = ?) AS uced;";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $cedulaVisitante, $cedulaVisitante); //bind the parameter
    $stmt->execute();
    $resultados = $stmt->get_result();

    if ($resultados->num_rows > 0) {
        $fila = $resultados->fetch_assoc();
        
        if ($fila["vced"] != NULL || $fila["uced"] != NULL){
            echo json_encode(["class" => "red", "message" => "¡Esta cédula ya está registrada!"]);
            exit;
        }
        
    }
    if (intval($cedulaVisitante) >= 100000){
        echo json_encode(["class" => "green", "message" => "Formato de cédula válido."]);
        exit;
    }
    }

    echo json_encode(["class" => "red", "message" => "Formato de cédula no válido."]);
    exit;
   
?>