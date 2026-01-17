<?php

include '../../../app/config/database.php';
$conn = conexion();

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener datos del formulario
    $periodo_prestamo = $_POST['periodoPrestamo'] ?? '';
    $unidades = $_POST['unidadesPrestamo'] ?? '';
    $periodo_renovaciones = $_POST['periodoRenovaciones'] ?? '';
    $renovaciones_permitidas = $_POST['renovacionesPermitidas'] ?? '';
    $peticiones_permitidas = $_POST['peticionesPermitidas'] ?? '';
    $peticiones_diarias = $_POST['peticionesDiarias'] ?? '';
    $peticiones_por_registro = $_POST['peticionesPorRegistro'] ?? '';
    $dolarBCV = $_POST['dolarBCV'] ?? '';
    $montoRetraso = $_POST['retraso'] ?? '';
    $montoPerdida = $_POST['perdida'] ?? '';
    $montoDano = $_POST['dano'] ?? '';

    // Validar datos
    if (empty($periodo_prestamo) || empty($unidades) || empty($periodo_renovaciones) || empty($renovaciones_permitidas) || empty($peticiones_permitidas) || empty($peticiones_diarias) || empty($peticiones_por_registro) || empty($dolarBCV) || empty($montoRetraso) || empty($montoDano) || empty($montoPerdida)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    if (!is_numeric($periodo_prestamo) || !is_numeric($periodo_renovaciones) || !is_numeric($renovaciones_permitidas) || !is_numeric($peticiones_permitidas) || !is_numeric($peticiones_diarias) || !is_numeric($peticiones_por_registro) || !is_numeric($dolarBCV) || !is_numeric($montoRetraso) || !is_numeric($montoDano) || !is_numeric($montoPerdida)) {
        echo json_encode(["success" => false, "message" => "Los campos deben ser numéricos."]);
        exit;
    }

    // Preparar la consulta SQL
    $sql = "UPDATE `regla_de_circulacion` SET `periodo_prestamo`= ?, `unidades`= ?, `periodo_renovaciones`= ?, `renovaciones_permitidas`= ?, `peticiones_permitidas`= ?, `peticiones_diarias`= ?, `peticiones_por_registro`= ?, `dolarBCV`= ?, `monto_por_dia_retraso`= ?, `monto_por_danio`= ?, `monto_por_perdida_material`= ? WHERE id = 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }

    // Vincular parámetros
    $stmt->bind_param("isiiiiidddd", $periodo_prestamo, $unidades, $periodo_renovaciones, $renovaciones_permitidas, $peticiones_permitidas, $peticiones_diarias, $peticiones_por_registro, $dolarBCV, $montoRetraso, $montoDano, $montoPerdida);


        $_SESSION["periodo_prestamo"] = $periodo_prestamo;
        $_SESSION["unidades"] = $unidades; 
        $_SESSION["periodo_renovaciones"] = $periodo_renovaciones;
        $_SESSION["renovaciones_permitidas"] = $renovaciones_permitidas; 
        $_SESSION["peticiones_permitidas"] = $peticiones_permitidas; 
        $_SESSION["peticiones_diarias"] = $peticiones_diarias; 
        $_SESSION["peticiones_por_registro"] = $peticiones_por_registro;
        $_SESSION["dolarBCV"] = $dolarBCV;
        $_SESSION["monto_por_perdida_material"] = $montoPerdida;
        $_SESSION["monto_por_danio"] = $montoDano;
        $_SESSION["monto_por_dia_retraso"] = $montoRetraso;



    // Ejecutar la consulta
    try {
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Datos actualizados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar los datos: " . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}