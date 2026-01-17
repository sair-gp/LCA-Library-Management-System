<?php
require_once "../config/database.php";
$conn = conexion();

// 1. Configuración inicial
$nombreCoordinador = "Tania Maneiro";
$cedulaCoordinador = "V-9982336";

// 2. Procesamiento de fechas
$fechaInicioForm = $_POST['fechaInicioMensual'] ?? date('Y-m-01');
$timestamp = strtotime($fechaInicioForm);
$numeroMes = date('m', $timestamp);
$anio = date('Y', $timestamp);

$nombresMeses = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$nombreMesEspanol = $nombresMeses[$numeroMes] ?? 'Mes Desconocido';

// 3. Consulta a la base de datos
$fechaInicioMes = date('Y-m-01', $timestamp);
$fechaFinMes = date('Y-m-t', $timestamp);

$sql = "SELECT
            (SELECT COUNT(*) FROM prestamos WHERE fecha_inicio >= '$fechaInicioMes' AND fecha_inicio <= '$fechaFinMes') AS total_prestamos,
            (SELECT COUNT(*) FROM asistencias WHERE fecha >= '$fechaInicioMes' AND fecha <= '$fechaFinMes') AS total_asistencias,
            (SELECT COUNT(*) FROM registro_actividades WHERE fecha_inicio >= '$fechaInicioMes' AND fecha_inicio <= '$fechaFinMes') AS total_actividades,
            (SELECT COUNT(*) FROM historial WHERE fecha >= '$fechaInicioMes' AND fecha <= '$fechaFinMes' AND accion_id = 5) AS total_desincorporaciones;";

$result = $conn->query($sql);
$datos = $result->fetch_assoc();

// 4. Configurar encabezados HTTP
header("Content-Type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=reporte_mensual_".strtolower($nombreMesEspanol)."_$anio.doc");
header("Pragma: no-cache");
header("Expires: 0");

// 5. Obtener rutas reales de las imágenes
$rutaLogoIzquierdo = realpath('./img/lc.jpg');
$rutaLogoDerecho = realpath('./img/red_biblio.jpg');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte Mensual - <?= $nombreMesEspanol ?> <?= $anio ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 1cm;
            padding: 0;
            font-size: 11pt;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .logo-cell {
            width: 60px;
        }
        .logo {
            height: 60px;
            width: 60px;
        }
        .header-center {
            text-align: center;
            padding: 0 10px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 10pt;
            margin: 3px 0;
        }
        .content {
            margin: 15px 0;
            text-align: justify;
            line-height: 1.5;
        }
        .result-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0 10px 0;
            color: #333;
        }
        .data-row {
            margin: 8px 0 8px 20px;
        }
        .data-label {
            display: inline-block;
            width: 220px;
            font-weight: normal;
        }
        .data-value {
            font-weight: bold;
        }
        .signature {
            margin-top: 40px;
            text-align: center;
        }
        .signature-line {
            margin-bottom: 5px;
        }
        .footer {
            font-size: 9pt;
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
        .date-info {
            text-align: right;
            font-size: 9pt;
            color: #224488;
            margin-bottom: 15px;
        }
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<!-- ENCABEZADO CON LOGOS PERFECTAMENTE ALINEADOS -->
<table class="header-table">
    <tr>
        <td class="logo-cell" style="text-align: left;">
            <?php if($rutaLogoIzquierdo): ?>
                <img class="logo" src="<?= $rutaLogoIzquierdo ?>" width="60" height="60" alt="Logo Izquierdo"/>
            <?php endif; ?>
        </td>
        <td class="header-center">
            <div class="title">Biblioteca Publica "Luisa Caceres de Arismendi"</div>
            <div class="subtitle">Comunidad Cruz Salmeron Acosta, Calle Punta de Mata</div>
            <div class="subtitle">Cumana, Edo. Sucre - Municipio Sucre, Parroquia Santa Ines</div>
        </td>
        <td class="logo-cell" style="text-align: right;">
            <?php if($rutaLogoDerecho): ?>
                <img class="logo" src="<?= $rutaLogoDerecho ?>" width="60" height="60" alt="Logo Derecho"/>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- Fecha del reporte -->
<div class="date-info">Cumana - Sucre <?= date("d-m-Y H:i:s") ?></div>

<!-- Título del reporte -->
<div class="report-title">INFORME DEL MES</div>

<!-- Introducción -->
<div class="content">
    El presente reporte tiene como objetivo ofrecer una visión general de la actividad de la biblioteca 
    durante el período correspondiente al mes de <?= $nombreMesEspanol ?> de <?= $anio ?>. A través de la 
    recopilación y el análisis de datos clave, se busca proporcionar información relevante sobre la 
    utilización de los servicios, la participación en actividades y la gestión del acervo bibliográfico. 
    Los resultados presentados a continuación resumen los principales indicadores de gestión, ofreciendo 
    una base para la evaluación y la toma de decisiones futuras.
</div>

<!-- Resultados -->
<div class="result-title">Totalización de Resultados</div>

<div class="data-row"><span class="data-label">Total de Préstamos:</span> <span class="data-value"><?= $datos['total_prestamos'] ?></span></div>
<div class="data-row"><span class="data-label">Total de Asistencias:</span> <span class="data-value"><?= $datos['total_asistencias'] ?></span></div>
<div class="data-row"><span class="data-label">Total de Actividades:</span> <span class="data-value"><?= $datos['total_actividades'] ?></span></div>
<div class="data-row"><span class="data-label">Total de Libros Desincorporados:</span> <span class="data-value"><?= $datos['total_desincorporaciones'] ?></span></div>

<!-- Firma -->
<div class="signature">
    <div class="signature-line">____________________________</div>
    <div><?= $nombreCoordinador ?></div>
    <div>C.I.: <?= $cedulaCoordinador ?></div>
    <div>Coordinador(a)</div>
</div>

<!-- Pie de página -->
<div class="footer">Página 1 de 1</div>

</body>
</html>

<?php
$conn->close();
exit;