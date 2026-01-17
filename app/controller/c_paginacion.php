<?php 

include "app/config/database.php";
include "app/model/cargar_select.php";
include "app/model/imprimirRegistros.php";
$select = new selects();
$paginacion = new imprimirRegistros();
$conexion = conexion();


$returned = $paginacion->obtenerPaginacion($conexion, $consultaCount, $consultaPaginacion, $registros_por_pagina = 10);

$resultado = $returned["registros"];
$total_registros = $returned["total_registros"];
$total_paginas = $returned["total_paginas"];
$pagina_actual = $returned["pagina_actual"];