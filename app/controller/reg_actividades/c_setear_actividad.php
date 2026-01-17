<?php

date_default_timezone_set('America/Caracas');
$hoy = date("Y-m-d");
$registrosVariable = 10;
$consultaCount = "SELECT COUNT(*) AS total FROM registro_actividades;";
$consultaPaginacion = "SELECT r.id, r.descripcion, r.encargado, r.fecha_inicio, r.fecha_fin, e.estado, r.duracion, r.observacion FROM registro_actividades r JOIN estado_actividad e ON e.id = r.estado LIMIT ?, ?";


include "app/controller/c_paginacion.php";
include "app/views/modal/registrar_actividad.php";
require_once "app/model/actividades.php";

$actividad = new actividades;

$actividad->cambiarEstadoAutomatico($conexion);

