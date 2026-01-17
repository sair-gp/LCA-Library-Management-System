<?php
include '../../model/visitantes.php';
include '../../model/historial.php';
include '../../config/database.php';

$conn = conexion();
$visitante = new visitantes();
$historial = new Historial($conn);

session_start();

if (!isset($_POST["cedula"], $_POST["nombre"], $_POST["accion"])){
    header("Location: ../../../index.php?vista=visitantes&toast=error&mensaje=Datos incompletos.");
    exit;
}

$estadoHabilitacion = $_POST['accion'] == "habilitar" ? 1 : 0;

if (!$visitante->habilitacionDeVisitante($conn, $_POST["cedula"], $estadoHabilitacion)){
    header("Location: ../../../index.php?vista=visitantes&toast=error&mensaje=No se ha podido deshabilitar al visitante. Por favor, intente nuevamente.");
    exit;
}

$accionHistorial = $estadoHabilitacion === 1 ? 21 : 4; //21 corresponde a habilitacion de usuario, 4 a la deshabilitacion
$responsable = $_SESSION["cedula"];
$hoy = Date('Y-m-d');
$visitante = $_POST["nombre"];
$cedula = $_POST["cedula"];
$habilitaciontxt =  $_POST['accion'] == "habilitar" ? "habilitado" : "deshabilitado";

$detalles = "Se ha $habilitaciontxt al visitante de nombre $visitante, portador de la C.I: $cedula.";

if (!$historial->registrar_accion($responsable, $detalles, $hoy, $accionHistorial)){
    header("Location: ../../../index.php?vista=visitantes&toast=warning&mensaje=$visitante ha sido $habilitaciontxt.");
    exit;
}


header("Location: ../../../index.php?vista=visitantes&toast=success&mensaje=$visitante ha sido $habilitaciontxt correctamente.");
exit;