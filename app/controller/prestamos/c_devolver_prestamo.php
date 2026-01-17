<?php

// Incluir archivos necesarios
require_once '../../config/database.php';
require_once '../../model/prestamos.php';
require_once '../../model/historial.php';

// Configuración inicial
date_default_timezone_set('America/Caracas');
session_start();

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=Método no permitido");
    exit();
}

// Verificar sesión y permisos
if (!isset($_SESSION['cedula'])) {
    header("Location: ../../../index.php?vista=login&toast=error&mensaje=Debe iniciar sesión");
    exit();
}

// Inicializar objetos
$conexion = conexion();
$prestamo = new Prestamos();
$historial = new Historial($conexion);

// Validar acción
if (!isset($_POST['accion']) || $_POST['accion'] !== 'devolver') {
    header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=Acción no válida");
    exit();
}

// Validar parámetros requeridos
if (!isset($_POST['idPrestamo'], $_POST['estadoPrestamo'])) {
    header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=Parámetros incompletos");
    exit();
}

// Sanitizar y validar datos de entrada
$idPrestamo = filter_var($_POST['idPrestamo'], FILTER_VALIDATE_INT);
$estadoPrestamo = in_array($_POST['estadoPrestamo'], ['vencido', 'devuelto']) ? $_POST['estadoPrestamo'] : 'devuelto';
$fechaActual = date('Y-m-d');
$responsable = $_SESSION['cedula'];

if ($idPrestamo === false || $idPrestamo <= 0) {
    header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=ID de préstamo no válido");
    exit();
}

// Procesar devolución
try {
    $resultado = $prestamo->devolverPrestamo($conexion, $idPrestamo, $estadoPrestamo, $fechaActual);
    
    if (!$resultado["success"]) {
        throw new Exception($resultado["message"] ?? "Error al devolver el préstamo");
    }

    // Obtener detalles del préstamo usando consulta preparada
    $query = "SELECT v.nombre AS nombre_lector, l.titulo, l.isbn, e.cota 
              FROM prestamos AS p 
              JOIN visitantes AS v ON p.lector = v.cedula 
              JOIN ejemplares AS e ON p.cota = e.id 
              JOIN libros AS l ON e.isbn_copia = l.isbn 
              WHERE p.id = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $idPrestamo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($fila = $result->fetch_assoc()) {
        $nombreLector = htmlspecialchars($fila["nombre_lector"]);
        $tituloLibro = htmlspecialchars($fila["titulo"]);
        $isbn = htmlspecialchars($fila["isbn"]);
        $cota = htmlspecialchars($fila["cota"]);

        $detalles = sprintf("Título: %s, ISBN: %s, Cota: %s, Lector: %s", 
                           $tituloLibro, $isbn, $cota, $nombreLector);

        // Determinar acción para el historial
        $accion = ($estadoPrestamo === "vencido") ? 15 : 12;
        
        if (!$historial->registrar_accion($responsable, $detalles, $fechaActual, $accion)) {
            throw new Exception("Error al registrar en el historial");
        }
        
        $mensaje = "Ejemplar devuelto correctamente";
        header("Location: ../../../index.php?vista=prestamos&toast=success&mensaje=" . urlencode($mensaje));
    } else {
        throw new Exception("No se encontraron detalles del préstamo");
    }
} catch (Exception $e) {
    $errorMessage = "Hubo un error al procesar la devolución: " . $e->getMessage();
    header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=" . urlencode($errorMessage));
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conexion->close();
}