<?php
// app/controller/prestamos/c_multas.php

// Habilitar el manejo de errores para detectar problemas durante el desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir la conexión a la base de datos
require_once '../../config/database.php';

date_default_timezone_set("America/Caracas");

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



// Función para devolver una respuesta JSON
function jsonResponse($success, $message, $redirect = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit; // Terminar la ejecución del script
}

try {
    // Iniciar la conexión
    $conexion = conexion();

    // Verificar si la solicitud es POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(false, 'Método no permitido.');
    }

    // Obtener los datos del formulario
    $prestamo_id = intval($_POST['prestamo_id'] ?? 0);
    $motivo = trim($_POST['motivo'] ?? '');
    //$monto = floatval($_POST['monto'] ?? 0.0);
    $fecha_multa = $_POST['fecha'] ?? '';
    $estado = trim($_POST['estado'] ?? '');
    $cedula_visitante = intval($_POST['lector'] ?? 0);

    // Validar que todos los campos estén presentes y sean válidos
    if ($prestamo_id <= 0 || empty($motivo) || empty($fecha_multa) || !in_array($estado, ['pendiente', 'pagada']) || $cedula_visitante <= 0) {
        jsonResponse(false, 'Datos incompletos o inválidos.');
    }

    // Iniciar la transacción
    $conexion->begin_transaction();

    // Preparar la consulta SQL para insertar la multa
    $sql = "INSERT INTO multas (prestamo_id, motivo,fecha_multa, estado, cedula_visitante) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: ' . $conexion->error);
    }

    // Vincular los parámetros
    $stmt->bind_param('iissi', $prestamo_id, $motivo, $fecha_multa, $estado, $cedula_visitante);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception('Error al registrar la multa: ' . $stmt->error);
    }

    // Obtener el ID de la fila recién insertada
    $multa_id = $conexion->insert_id;

    $stmt->close();

    //  Registrar multa en el historial de acciones

    require_once "../../model/historial.php";
    require_once "../../model/multas.php";
    $multasHandler = new Multas($conexion);
    $historial = new Historial($conexion);
    $hoy = Date("Y-m-d");
   
    $responsable = $_SESSION["cedula"];
    $accion = 19;//equivale a registro de multa

    //obtener nombre del visitante que realizo el pago para los detalles del historial
    $datos = $multasHandler->obtenerVisitante($multa_id, "WHERE m.id = ?;");//para obtener de la tabla de pagos
    $detalles = "Titulo: ". $datos["titulo"] ." Visitante: ". $datos["nombre"] .". Tipo de multa: ". $datos["motivo"] .".";
    $historial->registrar_accion($responsable, $detalles, $hoy, $accion);


    // Confirmar la transacción
    $conexion->commit();

    // Respuesta de éxito con redirección
    jsonResponse(true, 'Multa registrada correctamente.', "index.php?vista=prestamos&alerta=exito");

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    if (isset($conexion)) {
        $conexion->rollback();
    }

    // Respuesta de error
    jsonResponse(false, $e->getMessage());

} finally {
    // Cerrar la conexión y liberar recursos
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>