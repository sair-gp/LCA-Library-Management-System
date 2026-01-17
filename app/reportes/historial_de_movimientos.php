<?php
include '../controller/c_historial.php';

// Include the main TCPDF library (search for installation path).
require_once 'tcpdf/tcpdf.php';
date_default_timezone_set('America/Caracas');
//session_start();
//$fecha_actual = date("Y-m-d H:i:s");
//var_dump($conexion);


// Capturar el tipo de reporte
$reporte = $_POST['reporte'] ?? null;
$datos = []; // Aquí cargarás los datos según el tipo de reporte

switch ($reporte) {
  case 'fechas':
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    // Filtrar datos por rango de fechas
    $datos = $historial->filtrarPorFechas($fechaInicio, $fechaFin);
    break;

  case 'acciones':
    $tipoAccion = $_POST['tipoAccion'];
    $fechaInicio = $_POST['accionFechaInicio'] ?? null;
    $fechaFin = $_POST['accionFechaFin'] ?? null;
    // Filtrar datos por tipo de acción y fechas opcionales
    $datos = $historial->filtrarPorAccion($tipoAccion, $fechaInicio, $fechaFin);
    break;

  case 'responsable':
    $responsable = $_POST['responsable'];
    $fechaInicio = $_POST['responsableFechaInicio'] ?? null;
    $fechaFin = $_POST['responsableFechaFin'] ?? null;
    // Filtrar datos por responsable y fechas opcionales
    $datos = $historial->filtrarPorResponsable($responsable, $fechaInicio, $fechaFin);
    break;

  default:
    echo "Error: Tipo de reporte no válido.";
    exit;
}



// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{

  //Page header
  public function Header()
  {
    // Logo
    $image_file = './img/lc.jpg';
    $this->Image($image_file, 50, 5, 22, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $this->Ln(5);
    /**Logo Derecho $this->Image('src imagen', Eje X, Eje Y, Tamaño de la Imagen );*/
    $path = dirname(__FILE__);
    $logo = $path . '/img/red_biblio.jpg';
    $this->Image($logo, 210, 2, 27);

    // Set font
    $this->SetFont('helvetica', '', 12);
    $this->SetX(0);
    // Title
    $this->Cell(0, 15, 'Biblioteca Publica "Luisa Caceres de Arismendi"', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
    $this->SetY(15);
    $this->SetX(0);
    // $this->SetX(33);
    $this->Cell(0, 15, 'Comunidad Cruz Salmeron Acosta, Calle Punta de Mata', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $this->SetY(20);
    $this->SetX(0);

    $this->Cell(0, 15, 'Al lado de la U.E "ESTADO MONAGAS"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    $this->SetY(25);
    $this->SetX(0);

    $this->Cell(0, 15, 'Cumana, Edo. Sucre - Municipio Sucre, Parroquia Santa Ines', 0, false, 'C', 0, '', 0, false, 'M', 'M');

    $fecha_actual = date("d-m-Y H:i:s");

    $this->SetFont('helvetica', 'B', 8); //Tipo de fuente y tamaño de letra
    $this->SetXY(145, 29);
    $this->SetTextColor(34, 68, 136);
    $this->Write(0, 'Cumana - Sucre ' . $fecha_actual);
  }

  // Page footer
  public function Footer()
  {
    // Position at 15 mm from bottom
    $this->SetY(-15);
    // Set font
    $this->SetFont('helvetica', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
  }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($_SESSION["nombre"]);
$pdf->SetTitle('HISTORIAL DE MOVIMIENTOS');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
  require_once(dirname(__FILE__) . '/lang/eng.php');
  $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page HORIZONTAL
$pdf->AddPage('LANDSCAPE', 'A4');
//$pdf->AddPage();
$pdf->Ln(10);

// set some text to print
//$txt = <<<EOD HISTORIAL DE MOVIMIENTOS EOD;

// print a block of text using Write()
//$pdf->Write(0, "", '', 0, 'C', true, 0, false, false, 0);
//$pdf->Ln(5);


$html = '';

if (count($datos) > 0) {
    // Calcular el resumen
    $totalRegistros = count($datos);

    // Resumen en la primera página
    $resumen = "<h3 style='text-align:center;'>Resumen del Historial</h3>";
    $resumen .= "<p style='text-align:justify;'>Este reporte muestra un total de <strong>$totalRegistros</strong> registros de acciones realizadas. A continuación, se detallan las acciones registradas en el sistema:</p>";
    $pdf->writeHTML($resumen, true, false, true, false, '');

    // Contenido del reporte
    $html .= '<h3 style="text-align:center;">Detalles del Historial</h3>';

    // Variable para contar registros por página
    $recordsPerPage = 4;
    $recordCount = 0;

    foreach ($datos as $index => $row) {
        if ($recordCount % $recordsPerPage == 0) {
            // Iniciar tabla en cada nueva página
            if ($recordCount > 0) {
                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Agregar nueva página
                $pdf->Ln(10); // Agregar espacio entre el encabezado y la tabla
                $html = '<h3 style="text-align:center;">Detalles del Historial (continuación)</h3>';
            }
            $html .= '<table border="0.5" cellpadding="5" style="border-collapse:collapse; width:100%;" split_table_row="true">
                <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th style="border:0.5px solid #ddd; text-align:center; width: 100px;">FECHA</th>
                        <th style="border:0.5px solid #ddd; text-align:center; width: 100px;">ACCIÓN</th>
                        <th style="border:0.5px solid #ddd; text-align:center; width: 650px">DETALLES</th>
                        <th style="border:0.5px solid #ddd; text-align:center; width: 100px">RESPONSABLE</th>
                    </tr>
                </thead>
                <tbody>';
        }

        // Alternar colores de fondo para las filas
        $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9';
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="border:0.5px solid #ddd; text-align:center; width: 100px">' . $row['fecha'] . '</td>
            <td style="border:0.5px solid #ddd; text-align:center; width: 100px">' . $row['accion'] . '</td>
            <td style="border:0.5px solid #ddd; width: 650px">' . nl2br(htmlspecialchars($row['detalles'])) . '</td>
            <td style="border:0.5px solid #ddd; text-align:center; width: 100px">' . $row['nombre'] . '</td>
        </tr>';

        $recordCount++;
    }

    // Cerrar la última tabla
    $html .= '</tbody></table>';
}

// Escribir el contenido HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Mostrar el total de registros como texto en negrita alineado a la derecha
$pdf->writeHTML('<p style="text-align:right; font-weight:bold;">Total de Registros: ' . count($datos) . '</p>', true, false, true, false, '');

// Cerrar y generar el PDF
$pdf->Output('reporte_historial.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar
//============================================================+
// END OF FILE
//============================================================+
