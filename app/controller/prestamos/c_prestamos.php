<?php
include '../../model/prestamos.php';
include '../../config/database.php';
include '../../model/historial.php';

//Iniciar sesion para obtener al responsable
session_start();

$conn = conexion();
$prestamo = new prestamos();
$historial = new Historial($conn);


if (isset($_POST['idEjemplar']) && isset($_POST['fecha_fin']) && isset($_POST['lector'])) {

    $cota = $_POST['idEjemplar'];
    $fecha_fin = $_POST['fecha_fin'];
    $lector = $_POST['lector'];
    //$fecha = date("d-m-Y H:i:s");


    if ($prestamo->registrarPrestamo($cota, $fecha_fin, $lector, $conn)) {


        $responsable = $_SESSION["cedula"];
        $accion = 10; //Esto equivale a registro de prestamo
        $fechaActual = date('Y-m-d');

        $query = "SELECT l.titulo AS titulo_libro, v.nombre AS nombre_lector, e.cota, e.isbn_copia AS isbn
        FROM prestamos AS p
        JOIN ejemplares AS e ON p.cota = e.id
        JOIN libros AS l ON e.isbn_copia = l.isbn
        JOIN visitantes AS v ON p.lector = v.cedula
        WHERE e.id = '$cota' AND p.lector = '$lector';";

        //echo $query;

        $result = mysqli_query($conn, $query);

        if ($fila = mysqli_fetch_assoc($result)) {
            $tituloLibro = $fila["titulo_libro"];
            $nombreLector = $fila["nombre_lector"];
            $isbnLibroPrestado = $fila["isbn"];
            $cotaEjemplares = $fila["cota"];

            $detalles = "Título: '$tituloLibro', ISBN: '$isbnLibroPrestado', Cota: '$cotaEjemplares', Lector: '$nombreLector'";

            $historial->registrar_accion($responsable, $detalles, $fechaActual, $accion);

            // Redirigir con mensaje de éxito

            header("Location: ../../../index.php?vista=prestamos&toast=success&mensaje=Préstamo registrado correctamente.");
            exit();
        } else {
            // Manejo en caso de que no se encuentren resultados
            // Redirigir con mensaje de error

            header("Location: ../../../index.php?vista=prestamos&toast=error&mensaje=El préstamo no ha podido ser registrado. Por favor, intente nuevamente.");
            exit();
        }
    }
}
