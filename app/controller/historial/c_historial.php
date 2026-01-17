<?php

include '../model/historial.php';
include '../config/database.php';


$historial = new historial();
$conexion = conexion();
$datos = $historial->MostrarHistorial($conexion); // Almacenamos los datos
