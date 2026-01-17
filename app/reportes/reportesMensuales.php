<?php
require_once('tcpdf/tcpdf.php');
require_once "../config/database.php";
$conn = conexion();

// Datos del coordinador
$nombreCoordinador = "Tania Maneiro";
$cedulaCoordinador = "V-9982336";

// Iniciar objeto para generar reporte
class ReporteResumen extends TCPDF
{
    public $datosResumen;
    public $introduccion;
    public $nombreCoordinadorFirma;
    public $cedulaCoordinadorFirma;

    public function __construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa, $datosResumen, $introduccion, $nombreCoordinadorFirma, $cedulaCoordinadorFirma)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->datosResumen = $datosResumen;
        $this->introduccion = $introduccion;
        $this->nombreCoordinadorFirma = $nombreCoordinadorFirma;
        $this->cedulaCoordinadorFirma = $cedulaCoordinadorFirma;
    }

    // Page header
    public function Header()
    {
        // Logo Izquierdo
        $image_file_left = './img/lc.jpg';
        if (file_exists($image_file_left)) {
            $this->Image($image_file_left, 15, 5, 25, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // Logo Derecho
        $image_file_right = './img/red_biblio.jpg';
        if (file_exists($image_file_right)) {
            $this->Image($image_file_right, 165, 5, 25, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // Set font
        $this->SetFont('helvetica', '', 12);
        $this->SetX(0);
        $this->SetY(15);
        // Title
        $this->Cell(0, 15, 'Biblioteca Publica "Luisa Caceres de Arismendi"', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetY(20);
        $this->SetX(20);
        $this->Cell(0, 15, 'Comunidad Cruz Salmeron Acosta, Calle Punta de Mata', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        
        $this->SetY(25);
        $this->SetX(20);
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
        $this->SetY(-40);
        $this->Line(50, $this->GetY(), 150, $this->GetY());
        $this->Ln(2);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'Coordinador(a)', 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(0, 5, $this->nombreCoordinadorFirma, 0, 1, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'C.I.: ' . $this->cedulaCoordinadorFirma, 0, 1, 'C');

        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    // Contenido del resumen
    public function generarContenidoResumen()
    {
      
        $this->SetX(85);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'INFORME DEL MES', 0, 1, 'L');
        $this->Ln(5);
        $this->SetY(50);
        $this->SetFont('helvetica', '', 11);
        $this->writeHTML($this->introduccion, true, false, true, false, '');
        $this->Ln(5);

        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Totalización de Resultados', 0, 1, 'L');
        $this->Ln(5);
        $this->SetFont('helvetica', '', 11);

        if ($this->datosResumen) {
            $this->Cell(60, 8, 'Total de Préstamos:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 8, $this->datosResumen['total_prestamos'], 0, 1, 'L');
            $this->SetFont('helvetica', '', 11);

            $this->Cell(60, 8, 'Total de Asistencias:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 8, $this->datosResumen['total_asistencias'], 0, 1, 'L');
            $this->SetFont('helvetica', '', 11);

            $this->Cell(60, 8, 'Total de Actividades:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 8, $this->datosResumen['total_actividades'], 0, 1, 'L');
            $this->SetFont('helvetica', '', 11);

            $this->Cell(60, 8, 'Total de Libros Desincorporados:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(0, 8, $this->datosResumen['total_desincorporaciones'], 0, 1, 'L');
            $this->SetFont('helvetica', '', 11);
        } else {
            $this->SetTextColor(255, 0, 0);
            $this->Cell(0, 10, 'No se encontraron datos para el resumen.', 0, 1, 'L');
            $this->SetTextColor(0, 0, 0);
        }
    }
}

// Obtener la fecha de inicio del formulario
if (isset($_POST['fechaInicioMensual'])) {
    $fechaInicioForm = $_POST['fechaInicioMensual'];

    // Intenta convertir la cadena de fecha a un timestamp
    $timestamp = strtotime($fechaInicioForm);

    // Verifica si la conversión fue exitosa
    if ($timestamp !== false) {
        // Extraer el número del mes
        $numeroMes = date('m', $timestamp);
        $anio = date('Y', $timestamp);

        // Obtener el nombre del mes en español
        $nombresMesesEspanol = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];
        $nombreMesEspanol = $nombresMesesEspanol[$numeroMes] ?? 'Mes Desconocido';

        // Preparar las fechas de inicio y fin del mes para la consulta SQL
        $fechaInicioMes = date('Y-m-01', $timestamp);
        $fechaFinMes = date('Y-m-t', $timestamp);
    } else {
        $nombreMesEspanol = 'Fecha Inválida';
        $fechaInicioMes = null;
        $fechaFinMes = null;
    }
} else {
    $nombreMesEspanol = 'Mes No Seleccionado';
    $fechaInicioMes = null;
    $fechaFinMes = null;
}

// Crear la introducción del reporte
$introduccionReporte = "<p style='text-align:justify;'>El presente reporte tiene como objetivo ofrecer una visión general de la actividad de la biblioteca durante el período correspondiente al mes de $nombreMesEspanol de " . ($anio ?? date('Y')) . ". A través de la recopilación y el análisis de datos clave, se busca proporcionar información relevante sobre la utilización de los servicios, la participación en actividades y la gestión del acervo bibliográfico. Los resultados presentados a continuación resumen los principales indicadores de gestión, ofreciendo una base para la evaluación y la toma de decisiones futuras.</p>";

// Crear una nueva instancia de ReporteResumen
$pdf = new ReporteResumen(
    PDF_PAGE_ORIENTATION,
    PDF_UNIT,
    'Letter',
    true,
    'UTF-8',
    false,
    false,
    null,
    $introduccionReporte,
    $nombreCoordinador,
    $cedulaCoordinador
);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Biblioteca');
$pdf->SetTitle('Reporte Resumen Mensual - ' . $nombreMesEspanol . ' ' . ($anio ?? date('Y')));
$pdf->SetSubject('Resumen de Datos de la Biblioteca');
$pdf->SetKeywords('TCPDF, PDF, biblioteca, resumen, reporte, ' . $nombreMesEspanol . ', ' . ($anio ?? date('Y')));

// **CONFIGURACIÓN DEL ENCABEZADO**
$pdf->SetPrintHeader(true);
$pdf->SetHeaderMargin(10);
$pdf->SetMargins(15, 35, 15);
$pdf->SetFooterMargin(50);
$pdf->SetAutoPageBreak(TRUE, 55);
$pdf->SetFont('helvetica', '', 12);

// Consulta SQL
$sql = "SELECT
            (SELECT COUNT(*) FROM prestamos WHERE fecha_inicio >= '$fechaInicioMes' AND fecha_inicio <= '$fechaFinMes') AS total_prestamos,
            (SELECT COUNT(*) FROM asistencias WHERE fecha >= '$fechaInicioMes' AND fecha <= '$fechaFinMes') AS total_asistencias,
            (SELECT COUNT(*) FROM registro_actividades WHERE fecha_inicio >= '$fechaInicioMes' AND fecha_inicio <= '$fechaFinMes') AS total_actividades,
            (SELECT COUNT(*) FROM historial WHERE fecha >= '$fechaInicioMes' AND fecha <= '$fechaFinMes' AND accion_id = 5) AS total_desincorporaciones;";

$result = $conn->query($sql);

$datosResumen = null;
if ($result && $result->num_rows > 0) {
    $datosResumen = $result->fetch_assoc();
}

$pdf->datosResumen = $datosResumen;

// Agregar una página
$pdf->AddPage();

// Generar el contenido
$pdf->generarContenidoResumen();

// Cerrar conexión
$conn->close();

// Generar PDF
$pdf->Output('reporte_resumen_' . strtolower($nombreMesEspanol) . '_' . ($anio ?? date('Y')) . '.pdf', 'I');
?>