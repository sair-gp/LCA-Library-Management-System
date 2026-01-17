<?php
include '../../model/asistencias.php';
include '../../model/historial.php';
include '../../config/database.php';

$conn = conexion();
session_start();
$asistencia = new asistencias();
$historial = new Historial($conn);

if (isset($_POST['cedulaVisitante']) && isset($_POST['origen']) && isset($_POST['descripcion'])) {

    $cedula = $_POST['cedulaVisitante'];
    $origen = $_POST['origen'];
    $descripcion = $_POST['descripcion'];
    //$fecha = date("d-m-Y H:i:s");


    if ($asistencia->registrarAsistencia($cedula, $origen, $descripcion, $conn)) {

        $query = "SELECT nombre FROM visitantes WHERE cedula = $cedula";

        if (!$resultado = mysqli_query($conn, $query)) {
            header("Location: ../../../index.php?vista=asistencias&pagina=1&alerta=errorHistorial");
            exit;
        }

        $fila = $resultado->fetch_assoc();
        $nombreVisitante = $fila["nombre"];

        $responsable = $_SESSION["cedula"];


        date_default_timezone_set('America/Caracas'); // Establece la zona horaria (opcional, pero recomendado)
        $hora_actual = date("g:i:s A"); // Obtiene la hora actual en formato de 12 horas con AM/PM
        $hoy = Date("Y-m-d");
        $accion = 7; //Registro de asistencia

        $detalles = "Visitante: $nombreVisitante. Hora: $hora_actual.";

        if ($historial->registrar_accion($responsable, $detalles, $hoy, $accion)) {
            header("Location: ../../../index.php?vista=visitantes&pagina=1&alerta=exito");
        } else {
            header("Location: ../../../index.php?vista=visitantes&pagina=1&alerta=error");
        }
    }
}
