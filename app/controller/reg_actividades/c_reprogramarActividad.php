<?php

include '../../model/actividades.php';
include '../../model/historial.php';
include '../../config/database.php';

session_start();
$conexion = conexion();

// Iniciamos transacción
$conexion->begin_transaction();

try {
    $responsable = $_SESSION["cedula"];
    $accion = 9; //equivale a reprogramar actividad

    $actividad = new actividades();
    $historial = new Historial($conexion);

    if (!isset($_POST["motivo_suspension"], $_POST["nuevaFechaInicioActividad"], $_POST["nuevaFechaFinActividad"], $_POST['idActividad'])) {
        throw new Exception("Datos incompletos");
    }

    $motivoSuspension = $_POST["motivo_suspension"];
    $id = $_POST['idActividad'];
    $fechaInicio = $_POST["nuevaFechaInicioActividad"];
    $fechaFin = $_POST["nuevaFechaFinActividad"];

    // 1. Reprogramar actividad
    $resultado = $actividad->reprogramarActividad($conexion, $id, $motivoSuspension, $fechaInicio, $fechaFin);
    if (!$resultado) {
        throw new Exception("Error al reprogramar actividad");
    }

    // 2. Obtener datos para el historial
    $query = "SELECT descripcion, encargado FROM registro_actividades WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if (!$resultado || $resultado->num_rows === 0) {
        throw new Exception("Error al obtener datos de la actividad");
    }

    $fila = $resultado->fetch_assoc();
    $descripcionActividad = $fila["descripcion"];
    $encargado = $fila["encargado"];

    // 3. Registrar en historial
    $detalles = "Actividad: $descripcionActividad. Encargado: $encargado. Motivo de suspension: $motivoSuspension. Desde: $fechaInicio hasta $fechaFin";
    $hoy = date('Y-m-d');

    if (!$historial->registrar_accion($responsable, $detalles, $hoy, $accion)) {
        throw new Exception("Error al registrar en historial");
    }

    // Si todo sale bien, hacemos commit
    $conexion->commit();
    header("Location: ../../../index.php?vista=actividades&toast=success&mensaje=La actividad ha sido reprogramada correctamente.");
    exit;

} catch (Exception $e) {
    // Si hay algún error, hacemos rollback
    $conexion->rollback();
    
    $mensaje_error = urlencode($e->getMessage());
    $tipo_error = "error";
    
    // Mensajes personalizados según el tipo de error
    if ($e->getMessage() == "Datos incompletos") {
        $mensaje_error = "Datos incompletos. Por favor, complete todos los campos.";
    } elseif ($e->getMessage() == "Error al registrar en historial") {
        $tipo_error = "warning";
        $mensaje_error = "La actividad se reprogramó pero no se registró en el historial.";
    }
    
    header("Location: ../../../index.php?vista=actividades&toast=$tipo_error&mensaje=$mensaje_error");
    exit;
}