<?php
include '../../model/actividades.php';
include '../../model/historial.php';
include '../../config/database.php';

session_start();
$conn = conexion();
$actividad = new actividades();
$historial = new Historial($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    $required = ['descripcion', 'encargado', 'fecha_ini', 'fecha_fin', 'duracion_horas', 'duracion_minutos'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            header('Location: ../../../index.php?vista=actividades&toast=warning&mensaje=Advertencia, campos incompletos.');
            exit;
        }
    }

    // Formatear duración (ej: "02h:30m")
    $duracion = sprintf("%2dh %02dm", 
        $_POST['duracion_horas'], 
        $_POST['duracion_minutos']
    );

    // Registrar actividad
    if ($actividad->registrarActividad(
        $_POST['descripcion'],
        $_POST['encargado'],
        $_POST['fecha_ini'],
        $_POST['fecha_fin'],
        1, // estado
        $conn,
        $duracion
    )) {
        // Registrar en historial
        $historial->registrar_accion(
            $_SESSION["cedula"],
            "Nueva actividad: {$_POST['descripcion']} (Duración: $duracion)",
            date("Y-m-d"),
            16 // acción de actividad programada
        );
        
        header("Location: ../../../index.php?vista=actividades&toast=success&mensaje=La actividad ha sido programada.");
    } else {
        header("Location: ../../../index.php?vista=actividades&toast=error&mensaje=Error de conectividad. Verifique su conexión e intente nuevamente.");
    }
}