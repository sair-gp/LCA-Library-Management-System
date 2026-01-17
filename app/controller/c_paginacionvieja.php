<?php
include "app/config/database.php";
include "app/model/cargar_select.php";
include "app/model/libros.php";
$select = new selects();
$libros = new Libros();
$conexion = conexion();

/*$retorno[] = $libros->paginacion($libros->consultaCount, $libros->registros_por_pagina, $conexion);

$total_registros = $retorno[0];
$total_paginas = $retorno[1];

$resultado = $libros->imprimir($libros->registros_por_pagina, $libros->consultaPaginacion, $conexion); */





// ... Código de conexión a la base de datos ...

// Parámetros de paginación
// Parámetros de paginación
























$registros_por_pagina = $registrosVariable;
$pagina_actual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;

// Calcula el desplazamiento (offset) para la consulta
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Consulta para obtener el total de registros (usar prepared statement)
$stmt = $conexion->prepare($consultaCount);
$stmt->execute();
$resultado_total = $stmt->get_result();
$row_total = $resultado_total->fetch_assoc();
$total_registros = $row_total['total'];

// Calcula el total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para obtener los registros de la página actual (usar prepared statement)
$stmt = $conexion->prepare($consultaPaginacion);
$stmt->bind_param("ii", $inicio, $registros_por_pagina);
//$sql = $stmt;
$stmt->execute();
$resultado = $stmt->get_result();
?>