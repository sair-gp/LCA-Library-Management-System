<?php
include '../../model/prestamos.php';
include '../../config/database.php';
include '../../model/historial.php';

$conexion = conexion();
session_start();

$renovar = new prestamos();
$historial = new Historial($conexion);

$responsable = $_SESSION["cedula"];

if (isset($_POST['idPrestamo'], $_POST["nuevaFecha"])) {
    $idPrestamo = $_POST['idPrestamo'];
    $fechaRenovacion = $_POST["nuevaFecha"];

    // Usamos una consulta preparada para mayor seguridad
    $query = "SELECT l.titulo, v.nombre, e.cota, p.fecha_inicio, p.fecha_fin FROM prestamos p JOIN ejemplares e ON p.cota = e.id JOIN libros l ON l.isbn = e.isbn_copia JOIN visitantes v ON v.cedula = p.lector WHERE p.id = ?";

    if ($stmt = mysqli_prepare($conexion, $query)) {
        // Vinculamos el parámetro
        mysqli_stmt_bind_param($stmt, "i", $idPrestamo);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            $titulo = $fila["titulo"];
            $cota = $fila["cota"];
            $fechaInicio = $fila["fecha_inicio"];
            $fechaFin = $fila["fecha_fin"];
            $lector = $fila["nombre"];

            // Renovamos el préstamo
            if ($renovar->renovarPrestamo($conexion, $idPrestamo, $fechaRenovacion)) {

                // Correcta concatenación de los detalles
                $detalles = "Titulo: $titulo. Extendido desde: $fechaFin hasta: $fechaRenovacion. Lector: $lector. ID del prestamo: $idPrestamo.";
                $hoy = date('Y-m-d');
                $accion = 11; // Renovación de préstamo

                // Registramos la acción en el historial
                if ($historial->registrar_accion($responsable, $detalles, $hoy, $accion)) {
                    mysqli_stmt_close($stmt); // Cerrar el statement
                    mysqli_close($conexion); // Cerrar la conexión
                    header("Location: ../../../index.php?vista=prestamos&toast=success&mensaje=El préstamo ha sido renovado exitosamente.");
                    exit();
                } else {
                    mysqli_stmt_close($stmt); // Cerrar el statement
                    mysqli_close($conexion); // Cerrar la conexión
                    header("Location: ../../../index.php?vista=prestamos&toast=warning&mensaje=El préstamo ha sido renovado.");
                    exit();
                }
            } else {
                mysqli_stmt_close($stmt); // Cerrar el statement
                mysqli_close($conexion); // Cerrar la conexión
                header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=El préstamo no ha podido ser renovado. Por favor, intente nuevamente.");
                exit();
            }
        } else {
            mysqli_stmt_close($stmt); // Cerrar el statement
            mysqli_close($conexion); // Cerrar la conexión
            header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=No se ha logrado obtener los datos del ejemplar. Por favor, intente nuevamente.");
            exit();
        }
    } else {
        // Error en la preparación de la consulta
        mysqli_close($conexion); // Cerrar la conexión
        header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=Error de conexión. Por favor, intente nuevamente.");
        exit();
    }
}
