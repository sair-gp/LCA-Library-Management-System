<?php
include '../../../app/config/database.php';
include '../../../app/model/historial.php';

// Configuración de errores solo para desarrollo
if (getenv('ENVIRONMENT') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

header('Content-Type: application/json');
session_start();

// Verificar sesión activa
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['cedula'])) {
    echo json_encode(["status" => "error", "message" => "Sesión no válida"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar inputs
    $motivo = filter_input(INPUT_POST, 'motivo', FILTER_SANITIZE_STRING);
    $isbn_ejemplar = filter_input(INPUT_POST, 'isbnDes', FILTER_SANITIZE_STRING);
    $cota = filter_input(INPUT_POST, 'cota', FILTER_SANITIZE_STRING);
    $titulo = filter_input(INPUT_POST, 'tituloDes', FILTER_SANITIZE_STRING);

    if (empty($motivo) || empty($isbn_ejemplar) || empty($cota) || empty($titulo)) {
        echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios."]);
        exit;
    }

    $conn = conexion();
    $conn->begin_transaction();

    try {
        // Actualizar ejemplar
        $sql = "UPDATE ejemplares SET delete_at = 0, filaID = NULL WHERE cota = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cota);
        $stmt->execute();

        if ($stmt->affected_rows < 1) {
            throw new Exception("No se encontró el ejemplar o ya estaba desincorporado");
        }

        // Registrar en historial
        $historial = new Historial($conn);
        $detalles = sprintf("Título: %s. ISBN: %s. Cota: %s. Motivo: %s.",
            htmlspecialchars($titulo, ENT_QUOTES),
            htmlspecialchars($isbn_ejemplar, ENT_QUOTES),
            htmlspecialchars($cota, ENT_QUOTES),
            htmlspecialchars($motivo, ENT_QUOTES)
        );

        $historial->registrar_accion(
            $_SESSION['cedula'],
            $detalles,
            date('Y-m-d'),
            5 // Acción: Desincorporación
        );

        $conn->commit();
        echo json_encode([
            "status" => "success",
            "message" => "Ejemplar desincorporado correctamente",
            "isbnVol" => $isbn_ejemplar
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "status" => "error",
            "message" => "Error al desincorporar el ejemplar: " . $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}