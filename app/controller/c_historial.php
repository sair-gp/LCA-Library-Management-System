<?php
include "../config/database.php";
include '../model/historial.php';
$conexion = conexion();
session_start();
//var_dump($conexion);
$historial = new Historial($conexion);
$resultado = $historial->MostrarHistorial(); // Almacenamos los datos
$_SESSION['resultado'] = $resultado;


//header("Location: ../../index.php?vista=historial_de_acciones");
