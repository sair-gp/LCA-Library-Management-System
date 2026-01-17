<?php

include "../config/database.php";
$conexion = conexion();
class BibliotecaAnalisis
{
    private $datosActuales;
    private $datosAnteriores;

    public function __construct($datosActuales, $datosAnteriores)
    {
        $this->datosActuales = $datosActuales;
        $this->datosAnteriores = $datosAnteriores;
    }

    private function calcularVariacion($actual, $anterior)
    {
        if ($anterior == 0) {
            return ($actual > 0) ? 100 : 0; // Evitar división por cero
        }
        return round((($actual - $anterior) / $anterior) * 100, 2);
    }

    public function analizarPrestamos()
    {
        $prestamosActual = $this->datosActuales['prestamos'];
        $prestamosAnterior = $this->datosAnteriores['prestamos'];
        $variacion = $this->calcularVariacion($prestamosActual, $prestamosAnterior);

        return "Este mes se registraron $prestamosActual préstamos en total. " .
            (($variacion >= 1) ? "Esto representa un incremento del " : ($variacion == 0 ? "La cual es la misma cantidad de préstamos realizados el mes anterior." : "Se observa una disminución del ")) .
            ($variacion == 0 ? "" : abs($variacion) . "% respecto al mes anterior ($prestamosAnterior).");
    }

    public function analizarDevoluciones()
    {
        $devATiempoActual = $this->datosActuales['devoluciones_a_tiempo'];
        $devATiempoAnterior = $this->datosAnteriores['devoluciones_a_tiempo'];
        $varATiempo = $this->calcularVariacion($devATiempoActual, $devATiempoAnterior);

        $devExpiradasActual = $this->datosActuales['devoluciones_expiradas'];
        $devExpiradasAnterior = $this->datosAnteriores['devoluciones_expiradas'];
        $varExpiradas = $this->calcularVariacion($devExpiradasActual, $devExpiradasAnterior);

        return "Este mes se devolvieron $devATiempoActual libros dentro del plazo establecido, " .
            "lo que representa un cambio del $varATiempo% en comparación con el mes anterior. " .
            "Por otro lado, $devExpiradasActual préstamos fueron devueltos fuera del plazo, " .
            "lo que refleja un cambio del $varExpiradas% respecto al mes pasado.";
    }

    public function analizarDesincorporaciones()
    {
        $desincActual = $this->datosActuales['desincorporaciones'];
        $desincAnterior = $this->datosAnteriores['desincorporaciones'];
        $variacion = $this->calcularVariacion($desincActual, $desincAnterior);

        return "Se retiraron $desincActual libros del catálogo este mes, " .
            (($variacion >= 0) ? "un incremento del " : "una reducción del ") .
            abs($variacion) . "% en comparación con el mes anterior.";
    }

    public function analizarActividades()
    {
        $actividadesActual = $this->datosActuales['actividades'];
        $actividadesAnterior = $this->datosAnteriores['actividades'];
        $variacion = $this->calcularVariacion($actividadesActual, $actividadesAnterior);

        return "Se llevaron a cabo $actividadesActual actividades en la biblioteca, " .
            (($variacion >= 0) ? "un aumento del " : "una reducción del ") .
            abs($variacion) . "% respecto al mes anterior.";
    }

    public function analizarAsistencias()
    {
        $asistMActual = $this->datosActuales['asistencias_m'];
        $asistMAnterior = $this->datosAnteriores['asistencias_m'];
        $varM = $this->calcularVariacion($asistMActual, $asistMAnterior);

        $asistFActual = $this->datosActuales['asistencias_f'];
        $asistFAnterior = $this->datosAnteriores['asistencias_f'];
        $varF = $this->calcularVariacion($asistFActual, $asistFAnterior);

        return "Este mes asistieron $asistMActual hombres, mostrando un cambio del $varM% respecto al mes pasado, " .
            "y $asistFActual mujeres, con una variación del $varF%.";
    }

    public function analizarSuministroLibros()
    {
        $donadosActual = $this->datosActuales['donados'];
        $donadosAnterior = $this->datosAnteriores['donados'];
        $varDonados = $this->calcularVariacion($donadosActual, $donadosAnterior);

        $redActual = $this->datosActuales['red_bibliotecas'];
        $redAnterior = $this->datosAnteriores['red_bibliotecas'];
        $varRed = $this->calcularVariacion($redActual, $redAnterior);

        return "La biblioteca recibió $donadosActual libros por donación, con un cambio del $varDonados% respecto al mes pasado, " .
            "y $redActual libros provenientes de la red de bibliotecas, con una variación del $varRed%.";
    }

    //public function analizarLibrosAgregados() {}

    public function generarAnalisisCompleto()
    {
        return [
            '<b>Prestamos</b>' => $this->analizarPrestamos(),
            '<br><b>Devoluciones</b>' => $this->analizarDevoluciones(),
            '<br><b>Desincorporaciones</b>' => $this->analizarDesincorporaciones(),
            '<br><b>Actividades</b>' => $this->analizarActividades(),
            '<br><b>Asistencias</b>' => $this->analizarAsistencias(),
            '<br><b>Suministro de libros</b>' => $this->analizarSuministroLibros()
        ];
    }
}


$consultaAsistenciasFem_Masc = "SELECT
    SUM(CASE WHEN v.sexo = 1 AND MONTH(a.fecha) = MONTH(CURRENT_DATE()) THEN 1 ELSE 0 END) AS asistenciaMasculinoEsteMes,
    SUM(CASE WHEN v.sexo = 1 AND MONTH(a.fecha) = MONTH(CURRENT_DATE()) - 1 THEN 1 ELSE 0 END) AS asistenciaMasculinoMesPasado,
    SUM(CASE WHEN v.sexo = 2 AND MONTH(a.fecha) = MONTH(CURRENT_DATE()) THEN 1 ELSE 0 END) AS asistenciaFemeninoEsteMes,
    SUM(CASE WHEN v.sexo = 2 AND MONTH(a.fecha) = MONTH(CURRENT_DATE()) - 1 THEN 1 ELSE 0 END) AS asistenciaFemeninoMesPasado
    FROM visitantes v
    JOIN asistencias a ON v.cedula = a.cedula_visitante
    WHERE (MONTH(a.fecha) = MONTH(CURRENT_DATE()) OR MONTH(a.fecha) = MONTH(CURRENT_DATE()) - 1)
    AND YEAR(a.fecha) = YEAR(CURRENT_DATE());";

$resultadoAsistencias = mysqli_query($conexion, $consultaAsistenciasFem_Masc);

$fila = $resultadoAsistencias->fetch_assoc();

$asistenciaMascEsteMes = $fila["asistenciaMasculinoEsteMes"];
$asistenciaMascMesPasado = $fila["asistenciaMasculinoMesPasado"];
$asistenciaFemEsteMes = $fila["asistenciaFemeninoEsteMes"];
$asistenciaFemMesPasado = $fila["asistenciaFemeninoMesPasado"];

$queryDatosAnteriores = "SELECT 
COUNT(CASE WHEN accion_id = 1 THEN 1 ELSE NULL END) AS registroLibro,
COUNT(CASE WHEN accion_id = 2 THEN 1 ELSE NULL END) AS registroEjemplar,
COUNT(CASE WHEN accion_id = 2 AND detalles LIKE '%Donación%' THEN 1 ELSE NULL END) AS donados,
COUNT(CASE WHEN accion_id = 2 AND detalles LIKE '%Red de bibliotecas%' THEN 1 ELSE NULL END) AS redBiblio,
COUNT(CASE WHEN accion_id = 5 THEN 1 ELSE NULL END) AS desincorporacionEjemplar,
COUNT(CASE WHEN accion_id = 7 THEN 1 ELSE NULL END) AS registroAsistencia,
COUNT(CASE WHEN accion_id = 10 THEN 1 ELSE NULL END) AS prestamosRealizados,
COUNT(CASE WHEN accion_id = 12 THEN 1 ELSE NULL END) AS devolucionAtiempo,
COUNT(CASE WHEN accion_id = 15 THEN 1 ELSE NULL END) AS devolucionTardia,
COUNT(CASE WHEN accion_id = 13 THEN 1 ELSE NULL END) AS prestamosVencidos,
COUNT(CASE WHEN accion_id = 16 THEN 1 ELSE NULL END) AS actividades
FROM `historial` WHERE MONTH(fecha) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) 
    AND YEAR(fecha) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)";

$consultaDatosAnteriores = mysqli_query($conexion, $queryDatosAnteriores);
$datosAnterioresQ = [];
while ($fila = $consultaDatosAnteriores->fetch_assoc()) {
    $datosAnterioresQ = $fila;
}

$queryDatosActuales = "SELECT 
COUNT(CASE WHEN accion_id = 1 THEN 1 ELSE NULL END) AS registroLibro,
COUNT(CASE WHEN accion_id = 2 THEN 1 ELSE NULL END) AS registroEjemplar,
COUNT(CASE WHEN accion_id = 2 AND detalles LIKE '%Donación%' THEN 1 ELSE NULL END) AS donados,
COUNT(CASE WHEN accion_id = 2 AND detalles LIKE '%Red de bibliotecas%' THEN 1 ELSE NULL END) AS redBiblio,
COUNT(CASE WHEN accion_id = 5 THEN 1 ELSE NULL END) AS desincorporacionEjemplar,
COUNT(CASE WHEN accion_id = 7 THEN 1 ELSE NULL END) AS registroAsistencia,
COUNT(CASE WHEN accion_id = 10 THEN 1 ELSE NULL END) AS prestamosRealizados,
COUNT(CASE WHEN accion_id = 12 THEN 1 ELSE NULL END) AS devolucionAtiempo,
COUNT(CASE WHEN accion_id = 15 THEN 1 ELSE NULL END) AS devolucionTardia,
COUNT(CASE WHEN accion_id = 13 THEN 1 ELSE NULL END) AS prestamosVencidos,
COUNT(CASE WHEN accion_id = 16 THEN 1 ELSE NULL END) AS actividades
FROM `historial` WHERE MONTH(fecha) = MONTH(NOW()) AND YEAR(fecha) = YEAR(NOW());";

$consultaDatosResultado = mysqli_query($conexion, $queryDatosActuales);
$datosActualesQ = [];
while ($fila = $consultaDatosResultado->fetch_assoc()) {
    $datosActualesQ = $fila;
}
//var_dump($datosActualesQ["registroLibro"]);


$datosActuales = [
    'prestamos' => $datosActualesQ["prestamosRealizados"] ?? 0,
    'devoluciones_a_tiempo' => $datosActualesQ["devolucionAtiempo"] ?? 0,
    'devoluciones_expiradas' => $datosActualesQ["devolucionTardia"] ?? 0,
    'desincorporaciones' => $datosActualesQ["desincorporacionEjemplar"] ?? 0,
    'actividades' => $datosActualesQ["actividades"] ?? 0,
    'asistencias_m' => $asistenciaMascEsteMes,
    'asistencias_f' => $asistenciaFemEsteMes,
    'donados' => $datosActualesQ["donados"] ?? 0,
    'red_bibliotecas' => $datosActualesQ["redBiblio"] ?? 0
];

$datosAnteriores = [
    'prestamos' => $datosActualesQ["prestamosRealizados"] ?? 0,
    'devoluciones_a_tiempo' => $datosAnterioresQ["devolucionAtiempo"] ?? 0,
    'devoluciones_expiradas' => $datosAnterioresQ["devolucionTardia"] ?? 0,
    'desincorporaciones' => $datosAnterioresQ["desincorporacionEjemplar"] ?? 0,
    'actividades' => $datosAnterioresQ["actividades"] ?? 0,
    'asistencias_m' => $asistenciaMascMesPasado ?? 0,
    'asistencias_f' => $asistenciaFemMesPasado ?? 0,
    'donados' => $datosAnterioresQ["donados"] ?? 0,
    'red_bibliotecas' => $datosAnteriores["redBiblio"] ?? 0
];


// Uso de la clase
$analisisObject = new BibliotecaAnalisis($datosActuales, $datosAnteriores);
$resultados = $analisisObject->generarAnalisisCompleto();

// Mostrar resultados
$analisis = "";
foreach ($resultados as $categoria => $mensaje) {
    $analisis .= ucfirst($categoria) . ": " . $mensaje;
}

//echo $analisis;
