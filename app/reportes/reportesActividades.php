<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de incluir la ruta correcta a TCPDF
require_once "../config/database.php";
$conexion = conexion();
require_once "../model/reporteGeneral.php";
session_start();

// Iniciar objeto para generar reporte
$reporte = new ReporteGeneral($conexion);

// Obtener parámetros del formulario
$fechaInicio = $_POST["fechaInicioActividades"] ?? '';
$fechaFin = $_POST["fechaFinActividades"] ?? '';
$estado = $_POST["estadoActividades"] ?? 'todos'; // Todos, Activas, Reprogramadas, Suspendidas

// Construir filtro dinámico
$subFiltro = "";
$placeholders = [];

/*if ($fechaInicio != "" && $fechaFin != "") {
    $subFiltro .= " AND ra.fecha_inicio BETWEEN ? AND ?";
    $placeholders[] = $fechaInicio;
    $placeholders[] = $fechaFin;
}*/

if ($estado != "todos") {
    if ($estado == "activas") {
        $subFiltro .= " AND ea.id = 1";
    } elseif ($estado == "reprogramadas") {
        $subFiltro .= " AND ea.id = 4";
    } elseif ($estado == "suspendidas") {
        $subFiltro .= " AND ea.id = 3";
    }
}

// Obtener datos para el reporte
$filtro = "WHERE ra.fecha_inicio BETWEEN ? AND ? $subFiltro";
$values = array_merge([$fechaInicio, $fechaFin], $placeholders);
$datos = $reporte->obtenerDatosParaReporte("actividades", $filtro, $values);

// Extiende la clase TCPDF para personalizar el encabezado y pie de página
class MYPDF extends TCPDF
{
    // Page header
    public function Header()
    {
        // Logo
        $image_file = './img/lc.jpg';
        $this->Image($image_file, 15, 5, 22, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Ln(5);
        /**Logo Derecho $this->Image('src imagen', Eje X, Eje Y, Tamaño de la Imagen );*/
        $path = dirname(__FILE__);
        $logo = $path . '/img/red_biblio.jpg';
        $this->Image($logo, 165, 2, 27);

        // Set font
        $this->SetFont('helvetica', '', 12);
        $this->SetX(0);
        // Title
        $this->Cell(0, 15, 'Biblioteca Publica "Luisa Caceres de Arismendi"', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetY(15);
        $this->SetX(0);
        $this->Cell(0, 15, 'Comunidad Cruz Salmeron Acosta, Calle Punta de Mata', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetY(20);
        $this->SetX(0);
        $this->Cell(0, 15, 'Al lado de la U.E "ESTADO MONAGAS"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetY(25);
        $this->SetX(0);
        $this->Cell(0, 15, 'Cumana, Edo. Sucre - Municipio Sucre, Parroquia Santa Ines', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $fecha_actual = date("d-m-Y H:i:s");
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(145, 29);
        $this->SetTextColor(34, 68, 136);
        $this->Write(0, 'Cumana - Sucre ' . $fecha_actual);
    }

    // Pie de página
    public function Footer()
    {
        // Posición a 15 mm desde el fondo
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Generado el ' . date('d/m/Y H:i:s') . ' | Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Crear una nueva instancia de MYPDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($_SESSION['datos_biblioteca']['nombre']);
$pdf->SetTitle('Reporte de Actividades');
$pdf->SetSubject('Reporte de Actividades');
$pdf->SetKeywords('TCPDF, PDF, actividades, reporte');

// Establecer márgenes
$pdf->SetMargins(10, 40, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);

// Saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, 15);

// Establecer la fuente
$pdf->SetFont('helvetica', '', 10);

// Agregar una página
$pdf->AddPage();

// Resumen en la primera página
$resumen = "<h3 style='text-align:center;'>Reporte de Actividades</h3>";
$resumen .= "<p style='text-align:justify;'>Este reporte detalla las actividades registradas en el sistema, aplicando los filtros seleccionados. A continuación, se presenta un análisis detallado de los resultados:</p>";
$pdf->writeHTML($resumen, true, false, true, false, '');

// Texto dinámico según los filtros
$textoFiltros = "";

// Fechas específicas o no
if ($fechaInicio != "" && $fechaFin != "") {
    $textoFiltros .= "El reporte se ha generado para el período comprendido entre <strong>$fechaInicio</strong> y <strong>$fechaFin</strong>. ";
}

// Estado específico o no
if ($estado != "todos") {
    $textoEstado = ($estado == "activas") ? "activas" : (($estado == "reprogramadas") ? "reprogramadas" : "suspendidas");
    $textoFiltros .= "Se han considerado únicamente las actividades <strong>$textoEstado</strong>. ";
}

// Escribir el texto de los filtros
$pdf->writeHTML("<p style='text-align:justify;'>$textoFiltros</p>", true, false, true, false, '');

// Resumen de datos
$totalActividades = count($datos);

// Manejo de singulares y plurales
$textoActividades = ($totalActividades == 1) ? "1 actividad" : "$totalActividades actividades";

// Texto dinámico para "se registró" o "se registraron"
$textoRegistro = ($totalActividades == 1) ? "se registró" : "se registraron";

$textoResumen = "En el período seleccionado, $textoRegistro <strong>$textoActividades</strong>. ";

// Escribir el resumen de datos
$pdf->writeHTML("<p style='text-align:justify;'>$textoResumen</p>", true, false, true, false, '');

// Detalles de las actividades
if ($totalActividades > 0) {
    $pdf->writeHTML("<h4 style='text-align:center;'>Detalles de las Actividades</h4>", true, false, true, false, '');

    // Variable para contar registros por página
    $recordsPerPage = 12;
    $recordCount = 0;
    $suspendidasWidth = $estado == "suspendidas" ? "width: 80px" : "width: 150px";
    foreach ($datos as $index => $actividad) {
        if ($recordCount % $recordsPerPage == 0) {
            // Iniciar tabla en cada nueva página
            if ($recordCount > 0) {
                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Agregar nueva página
                $html = '<h3 style="text-align:center;">Detalles de las Actividades (continuación)</h3>';
            }
            $html = '<table border="1" cellpadding="5" style="border-collapse:collapse; width:100%;">
                <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th style="border:1px solid #000; text-align:center; width: 30px;">#</th>
                        <th style="border:1px solid #000; text-align:center; '. $suspendidasWidth .'">Descripción</th>
                        <th style="border:1px solid #000; text-align:center;">Encargado</th>
                        <th style="border:1px solid #000; text-align:center;">Fecha Inicio</th>
                        <th style="border:1px solid #000; text-align:center;">Fecha Fin</th>
                        <th style="border:1px solid #000; text-align:center;">Estado</th>';
                        if ($estado == "suspendidas"){
                        $html .= '
                        <th style="border:1px solid #000; text-align:center;">Motivo</th>';
                        }
                $html .= '
                    </tr>
                </thead>
                <tbody>';
        }

        $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9';
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="border:1px solid #000; text-align:center; width: 30px;">' . ($index + 1) . '</td>
            <td style="border:1px solid #000; '. $suspendidasWidth .'">' . $actividad['descripcion'] . '</td>
            <td style="border:1px solid #000;">' . $actividad['encargado'] . '</td>
            <td style="border:1px solid #000; text-align:center;">' . $actividad['fecha_inicio'] . '</td>
            <td style="border:1px solid #000; text-align:center;">' . $actividad['fecha_fin'] . '</td>
            <td style="border:1px solid #000; text-align:center;">' . $actividad['estado'] . '</td>';
            if ($estado == "suspendidas"){
                $html .= '
            <td style="border:1px solid #000;">' . ($actividad['motivo_suspension'] ?? 'N/A') . '</td>';
            }
            $html .= '</tr>';

        $recordCount++;
    }

    // Cerrar la última tabla
    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    $pdf->writeHTML("<p style='text-align:center; color:red;'>No se registraron actividades en el período seleccionado.</p>", true, false, true, false, '');
}

// Cerrar y generar el PDF
$pdf->Output('reporte_actividades.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar
?>