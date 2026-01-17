<?php
include '../../config/database.php';

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Acceso no permitido', 405);
    }

    // Validar datos requeridos
    if (!isset($_POST['observacion'], $_POST['idActividad'])) {
        throw new Exception('Datos incompletos', 400);
    }

    $observacion = trim($_POST['observacion']);
    $id = (int)$_POST['idActividad'];

    // Validar ID
    if ($id <= 0) {
        throw new Exception('ID inválido', 400);
    }

    // Obtener conexión
    $conexion = conexion();
    if (!$conexion) {
        throw new Exception('Error de conexión con la base de datos', 500);
    }

    // CONSULTA ACTUALIZADA CON BACKTICKS Y ESTRUCTURA EXACTA
    $sql = "UPDATE `registro_actividades` SET `observacion` = ? WHERE `id` = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception('Error al preparar la consulta: '.$conexion->error, 500);
    }

    $stmt->bind_param('si', $observacion, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al ejecutar la consulta: '.$stmt->error, 500);
    }

    // Verificar si se actualizó realmente
    if ($stmt->affected_rows === 0) {
        throw new Exception('No se actualizó ningún registro. Verifica el ID', 404);
    }

    // Redirección exitosa
    header("Location: ../../../index.php?vista=actividades&toast=success&mensaje=Se actualizó la información de la actividad.");
    exit();

} catch (Exception $e) {
    // Registrar error para depuración
    error_log('Error en actualización: '.$e->getMessage());
    
    // Redirección con error
    $mensaje = urlencode($e->getMessage());
    header("Location: ../../../index.php?vista=actividades&toast=error&mensaje=$mensaje");
    exit();
} finally {
    // Cerrar recursos
    if (isset($stmt)) $stmt->close();
    if (isset($conexion)) $conexion->close();
}