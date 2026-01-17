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
    $actividad = new actividades();
    $historial = new Historial($conexion);
    $accion = 8; // Acción para suspender actividad

    // Validación de datos requeridos
    if (!isset($_POST["motivo_suspension"], $_POST["idSuspender"])) {
        throw new Exception("Datos incompletos para suspender la actividad");
    }

    $motivoSuspension = $_POST["motivo_suspension"];
    $id = $_POST['idSuspender'];

    // 1. Suspender la actividad
    $resultado = $actividad->suspenderActividad($conexion, $id, $motivoSuspension);
    if (!$resultado) {
        throw new Exception("Error al suspender la actividad");
    }

    // 2. Obtener datos para el historial
    $query = "SELECT descripcion, encargado FROM registro_actividades WHERE id = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al consultar datos de la actividad");
    }

    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 0) {
        throw new Exception("No se encontró la actividad especificada");
    }

    $fila = $resultado->fetch_assoc();
    $descripcionActividad = $fila["descripcion"];
    $encargado = $fila["encargado"];

    // 3. Registrar en historial
    $detalles = "Actividad: $descripcionActividad. Encargado: $encargado. Motivo de suspension: $motivoSuspension.";
    $hoy = date('Y-m-d');

    if (!$historial->registrar_accion($responsable, $detalles, $hoy, $accion)) {
        throw new Exception("Historial no registrado", 1); // Código 1 para error no crítico
    }

    // Si todo es exitoso, confirmamos la transacción
    $conexion->commit();
    
    header("Location: ../../../index.php?vista=actividades&toast=success&mensaje=La actividad ha sido suspendida correctamente.");
    exit;

} catch (Exception $e) {
    // Revertimos la transacción en caso de error
    $conexion->rollback();
    
    $tipo = "error";
    $mensaje = urlencode($e->getMessage());
    
    // Manejo especial para errores no críticos
    if ($e->getCode() == 1) {
        $tipo = "warning";
        $mensaje = urlencode("La actividad se suspendió pero no se registró en el historial. Detalles: ".$e->getMessage());
    }
    
    header("Location: ../../../index.php?vista=actividades&toast=$tipo&mensaje=$mensaje");
    exit;
}