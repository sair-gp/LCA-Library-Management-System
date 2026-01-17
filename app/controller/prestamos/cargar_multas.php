<?php

//require_once "../../config/database.php";
include_once "app/model/multas.php";
$conexion = conexion();
$multaObj = new Multas($conexion);


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



$multas = [];

$multas = $multaObj->imprimirMultas($_SESSION["dolarBCV"], $_SESSION["monto_por_dia_retraso"], $_SESSION["monto_por_danio"], $_SESSION["monto_por_perdida_material"]);