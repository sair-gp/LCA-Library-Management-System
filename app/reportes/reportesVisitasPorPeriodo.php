<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de incluir la ruta correcta a TCPDF
require_once "../config/database.php";
$conexion = conexion();
require_once "../model/reporteGeneral.php";
session_start();

// Iniciar objeto para generar reporte
$reporte = new ReporteGeneral($conexion);

// Obtener parámetros del formulario
$periodo = $_POST["horarioPeriodo"] ?? 'mes'; // Año, mes, semana o día
$sexo = $_POST["horarioSexo"] ?? 'todos';
$horario = $_POST["horarioVisitas"] ?? 'todos';
$visitante = $_POST["visitante"] ?? 'todos'; // Asume que hay un campo para seleccionar visitante

// Validar y calcular fechas según el período
$fechaActual = date("Y-m-d");
$fechaInicioActual = '';
$fechaFinActual = '';
$fechaInicioAnterior = '';
$fechaFinAnterior = '';

switch ($periodo) {
    case 'anio':
        $fechaInicioActual = date("Y-01-01");
        $fechaFinActual = date("Y-12-31");
        $fechaInicioAnterior = date("Y-01-01", strtotime("-1 year"));
        $fechaFinAnterior = date("Y-12-31", strtotime("-1 year"));
        break;
    case 'mes':
        $fechaInicioActual = date("Y-m-01");
        $fechaFinActual = date("Y-m-t");
        $fechaInicioAnterior = date("Y-m-01", strtotime("-1 month"));
        $fechaFinAnterior = date("Y-m-t", strtotime("-1 month"));
        break;
    case 'semana':
        $fechaInicioActual = date("Y-m-d", strtotime("last Monday"));
        $fechaFinActual = date("Y-m-d", strtotime("next Sunday"));
        $fechaInicioAnterior = date("Y-m-d", strtotime("last Monday -1 week"));
        $fechaFinAnterior = date("Y-m-d", strtotime("next Sunday -1 week"));
        break;
    case 'dia':
        $fechaInicioActual = $fechaActual;
        $fechaFinActual = $fechaActual;
        $fechaInicioAnterior = date("Y-m-d", strtotime("-1 day"));
        $fechaFinAnterior = date("Y-m-d", strtotime("-1 day"));
        break;
    default:
        throw new Exception("Período no válido.");
}

// Construir filtro dinámico
$subFiltro = "";
$placeholders = [];

if ($sexo != "todos") {
    $subFiltro .= " AND vi.sexo = ?";
    $placeholders[] = $sexo;
}

if ($horario != "todos") {
    $subFiltro .= " AND HOUR(asi.fecha) BETWEEN ? AND ?";
    if ($horario == "manana") {
        $placeholders[] = 6;
        $placeholders[] = 12;
    } elseif ($horario == "tarde") {
        $placeholders[] = 13;
        $placeholders[] = 18;
    } elseif ($horario == "noche") {
        $placeholders[] = 19;
        $placeholders[] = 23;
    }
}

if ($visitante != "todos") {
    $subFiltro .= " AND vi.cedula = ?";
    $placeholders[] = $visitante;
}

// Obtener datos para el período actual
$filtroActual = "WHERE asi.fecha BETWEEN ? AND ? $subFiltro";
$valuesActual = array_merge([$fechaInicioActual, $fechaFinActual], $placeholders);
$datosActual = $reporte->obtenerDatosParaReporte("visitasPorHorario", $filtroActual, $valuesActual);

// Obtener datos para el período anterior
$filtroAnterior = "WHERE asi.fecha BETWEEN ? AND ? $subFiltro";
$valuesAnterior = array_merge([$fechaInicioAnterior, $fechaFinAnterior], $placeholders);
$datosAnterior = $reporte->obtenerDatosParaReporte("visitasPorHorario", $filtroAnterior, $valuesAnterior);

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
$pdf->SetTitle('Reporte de Visitas por Horario');
$pdf->SetSubject('Reporte de Visitas por Horario');
$pdf->SetKeywords('TCPDF, PDF, visitas, reporte, horario');

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
$resumen = "<h3 style='text-align:center;'>Reporte de Visitas por Horario</h3>";
$resumen .= "<p style='text-align:justify;'>Este reporte detalla las visitas registradas en el sistema, comparando el período actual con el período anterior. A continuación, se presenta un análisis detallado de los resultados:</p>";
$pdf->writeHTML($resumen, true, false, true, false, '');

// Texto dinámico según los filtros
$textoFiltros = "";

// Visitante específico o no
if ($visitante != "todos") {
    $nombreVisitante = $datosActual[0]['nombre'] ?? 'Desconocido';
    $textoFiltros .= "El reporte se ha generado específicamente para el visitante <strong>$nombreVisitante</strong>. ";
}

// Horario específico o no
if ($horario != "todos") {
    $textoHorario = ($horario == "manana") ? "mañana" : (($horario == "tarde") ? "tarde" : "noche");
    $textoFiltros .= "El análisis se ha realizado considerando únicamente el horario de la <strong>$textoHorario</strong>. ";
}

// Sexo específico o no
if ($sexo != "todos") {
    $textoSexo = ($sexo == "1") ? "masculino" : "femenino";
    $textoFiltros .= "Se ha considerado únicamente el sexo <strong>$textoSexo</strong>. ";
}

// Período seleccionado
$textoPeriodo = ($periodo == "anio") ? "año" : (($periodo == "mes") ? "mes" : (($periodo == "semana") ? "semana" : "día"));
$textoFiltros .= "El período de análisis corresponde al <strong>$textoPeriodo</strong>.";

// Escribir el texto de los filtros
$pdf->writeHTML("<p style='text-align:justify;'>$textoFiltros</p>", true, false, true, false, '');

// Resumen de datos
$totalActual = count($datosActual);
$totalAnterior = count($datosAnterior);

// Manejo de singulares y plurales
$textoVisitasActual = ($totalActual == 1) ? "1 visita" : "$totalActual visitas";
$textoVisitasAnterior = ($totalAnterior == 1) ? "1 visita" : "$totalAnterior visitas";

// Texto dinámico para "se registró" o "se registraron"
$textoRegistroActual = ($totalActual == 1) ? "se registró" : "se registraron";
$textoRegistroAnterior = ($totalAnterior == 1) ? "se registró" : "se registraron";

$textoResumen = "Durante el período actual, $textoRegistroActual <strong>$textoVisitasActual</strong>. ";
if ($totalAnterior > 0) {
    $diferencia = $totalActual - $totalAnterior;
    if ($diferencia > 0) {
        $textoResumen .= "Esto representa un incremento de <strong>$diferencia visitas</strong> en comparación con el período anterior, en el que $textoRegistroAnterior <strong>$textoVisitasAnterior</strong>. ";
    } elseif ($diferencia < 0) {
        $textoResumen .= "Esto representa una disminución de <strong>" . abs($diferencia) . " visitas</strong> en comparación con el período anterior, en el que $textoRegistroAnterior <strong>$textoVisitasAnterior</strong>. ";
    } else {
        $textoResumen .= "Esto es igual al número de visitas registradas en el período anterior. ";
    }
} else {
    $textoResumen .= "No hay datos disponibles para el período anterior. ";
}

// Escribir el resumen de datos
$pdf->writeHTML("<p style='text-align:justify;'>$textoResumen</p>", true, false, true, false, '');

// Detalles de las visitas
if ($totalActual > 0) {
    $pdf->writeHTML("<h4 style='text-align:center;'>Detalles de las Visitas</h4>", true, false, true, false, '');

    // Variable para contar registros por página
    $recordsPerPage = 12;
    $recordCount = 0;

    foreach ($datosActual as $index => $visita) {
        if ($recordCount % $recordsPerPage == 0) {
            // Iniciar tabla en cada nueva página
            if ($recordCount > 0) {
                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Agregar nueva página
                $html = '<h3 style="text-align:center;">Detalles de las Visitas (continuación)</h3>';
            }
            $html = '<table border="1" cellpadding="5" style="border-collapse:collapse; width:100%;">
                <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th style="border:1px solid #000; text-align:center; width: 30px;">#</th>
                        <th style="border:1px solid #000; text-align:center;">Nombre</th>
                        <th style="border:1px solid #000; text-align:center;">Cédula</th>
                        <th style="border:1px solid #000; text-align:center;">Fecha</th>
                        <th style="border:1px solid #000; text-align:center;">Origen</th>
                        <th style="border:1px solid #000; text-align:center;">Descripción</th>
                    </tr>
                </thead>
                <tbody>';
        }

        $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9';
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="border:1px solid #000; text-align:center; width: 30px;">' . ($index + 1) . '</td>
            <td style="border:1px solid #000;">' . $visita['nombre'] . '</td>
            <td style="border:1px solid #000; text-align:center;">' . $visita['cedula_visitante'] . '</td>
            <td style="border:1px solid #000; text-align:center;">' . $visita['fecha'] . '</td>
            <td style="border:1px solid #000;">' . $visita['origen'] . '</td>
            <td style="border:1px solid #000;">' . $visita['descripcion'] . '</td>
        </tr>';

        $recordCount++;
    }

    // Cerrar la última tabla
    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    $pdf->writeHTML("<p style='text-align:center; color:red;'>No se registraron visitas en el período actual.</p>", true, false, true, false, '');
}

// Cerrar y generar el PDF
$pdf->Output('reporte_visitas_horario.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar
?>