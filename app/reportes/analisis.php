<?php
// Variables simuladas (reemplázalas con consultas a la base de datos)
$prestamos = 120;
$devoluciones = 90;
$desincorporados = 8;
$libros_perdidos = 3;
$actividades = 4;
$asistencia_total = 250;
$asistencia_masculino = 140;
$asistencia_femenino = 110;
$prestamos_vencidos = 25;
$prestamos_con_multa = 15;
$libros_donados = 18;
$libros_proveidos_red = 12;
$nuevos_usuarios = 15;

// Función para generar análisis
function generarAnalisis()
{
    global $prestamos, $devoluciones, $desincorporados, $libros_perdidos, $actividades,
        $asistencia_total, $asistencia_masculino, $asistencia_femenino, $prestamos_vencidos,
        $prestamos_con_multa, $libros_donados, $libros_proveidos_red, $nuevos_usuarios;

    $analisis = [];

    // Análisis de Préstamos y Devoluciones
    if ($prestamos > 100 && $devoluciones >= ($prestamos * 0.8)) {
        $analisis[] = "Este mes se registró un alto número de préstamos ($prestamos), con un buen índice de devoluciones ($devoluciones), lo que indica una rotación eficiente del material bibliográfico.";
    } elseif ($prestamos > 100 && $devoluciones < ($prestamos * 0.5)) {
        $analisis[] = "Aunque los préstamos han sido elevados este mes ($prestamos), el número de devoluciones es bajo ($devoluciones).";
    } elseif ($prestamos < 30) {
        $analisis[] = "El número de préstamos ha sido bajo este mes ($prestamos).";
    }

    // Análisis de Libros Desincorporados
    if ($desincorporados >= 10) {
        $analisis[] = "Se han desincorporado $desincorporados libros este mes, indicando desgaste del material bibliográfico.";
    }
    if ($libros_perdidos >= 5) {
        $analisis[] = "Este mes se han reportado $libros_perdidos libros extraviados. Es recomendable revisar los procedimientos de control.";
    }

    // Análisis de Actividades y Asistencia
    if ($asistencia_total >= 100 && $actividades >= 5) {
        $analisis[] = "Las actividades organizadas ($actividades) tuvieron una gran acogida con $asistencia_total asistentes.";
    } elseif ($asistencia_total < 50 && $actividades >= 3) {
        $analisis[] = "Se realizaron $actividades actividades, pero la asistencia fue baja ($asistencia_total).";
    } elseif ($actividades == 0) {
        $analisis[] = "No se llevaron a cabo actividades este mes.";
    }

    // Análisis de Préstamos Vencidos y Multas
    if ($prestamos_vencidos >= 20) {
        $analisis[] = "Hay un número significativo de préstamos vencidos ($prestamos_vencidos).";
    }
    if ($prestamos_con_multa >= 10) {
        $analisis[] = "Se han aplicado $prestamos_con_multa multas por retraso en la devolución de libros.";
    }

    // Análisis de Asistencia por Género
    if ($asistencia_total >= 200) {
        $analisis[] = "Este mes, la biblioteca recibió un alto número de visitantes ($asistencia_total).";
    } elseif ($asistencia_total < 50) {
        $analisis[] = "La asistencia fue baja ($asistencia_total).";
    }
    if (abs($asistencia_masculino - $asistencia_femenino) >= 50) {
        $analisis[] = "Desbalance en la asistencia: $asistencia_masculino hombres y $asistencia_femenino mujeres.";
    }

    // Análisis de Libros Donados y Proveídos por la Red
    if ($libros_donados >= 20) {
        $analisis[] = "Se recibieron $libros_donados libros en donaciones.";
    } elseif ($libros_donados < 5) {
        $analisis[] = "Las donaciones de libros fueron escasas ($libros_donados).";
    }
    if ($libros_proveidos_red >= 30) {
        $analisis[] = "La red de bibliotecas ha proveído $libros_proveidos_red libros.";
    } elseif ($libros_proveidos_red < 10) {
        $analisis[] = "El suministro de libros por la red de bibliotecas fue bajo ($libros_proveidos_red).";
    }

    return $analisis;
}

// Mostrar el análisis generado
$analisis = generarAnalisis();
foreach ($analisis as $mensaje) {
    echo $mensaje . "<br>";
}
