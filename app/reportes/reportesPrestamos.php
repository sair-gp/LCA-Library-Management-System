<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de incluir la ruta correcta a TCPDF
require_once "../config/database.php";
$conexion = conexion();
require_once "../model/reporteGeneral.php";
session_start();
// Iniciar objeto para generar reporte
$reporte = new ReporteGeneral($conexion);

// Validar fechas
$fechaInicio = $_POST["fechaInicioGeneral"] ?? Date("Y-m-d");
$fechaFin = $_POST["fechaFinGeneral"] ?? Date("Y-m-d");
if (!strtotime($fechaInicio) || !strtotime($fechaFin)) {
    throw new Exception("Las fechas proporcionadas no son válidas.");
}
if ($fechaInicio > $fechaFin) {
    throw new Exception("La fecha de inicio no puede ser mayor que la fecha de fin.");
}

// Datos para el reporte
$lector = $_POST["lectorPrestamos"] ?? 'todos';
$parametroEspecial = $_POST["parametroEspecial"] ?? ''; // Asegúrate de usarlo si es necesario.

// Construir filtro dinámico
$subFiltro = "";
$confirmarQueHayLector = false;
$placeholders = [];
if ($lector != "todos") {
    $subFiltro = "AND v.cedula = ?";
    $placeholders[] = $lector;
    $confirmarQueHayLector = true;
}

$filtro = "WHERE p.fecha_inicio BETWEEN DATE(?) AND DATE(?) $subFiltro";
$values = array_merge([$fechaInicio, $fechaFin], $placeholders);

// Obtener datos del reporte
try {
    $datos = $reporte->obtenerDatosParaReporte("prestamos", $filtro, $values);

    $lectorIndividual = $confirmarQueHayLector && isset($datos[0]["nombre"]) ?  " realizados por <strong>" . $datos[0]['nombre'] . "</strong>, titular de la cédula <strong>" . $datos[0]["cedula"] . "</strong>" : "";
    

    //$displayNone = $lectorIndividual ? "display: none;" : "";

    // Extiende la clase TCPDF para personalizar el encabezado y pie de página
    class MYPDF extends TCPDF
{

  //Page header
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

  // Pie de página
  public function Footer() {
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
    $pdf->SetTitle('Reporte de Préstamos');
    $pdf->SetSubject('Reporte de Préstamos');
    $pdf->SetKeywords('TCPDF, PDF, préstamos, reporte');

    // Establecer márgenes
    $pdf->SetMargins(06, 40, 10); // Márgenes más ajustados
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);

    // Saltos de página automáticos
    $pdf->SetAutoPageBreak(TRUE, 15);

    // Establecer la fuente
    $pdf->SetFont('helvetica', '', 10);

    // Agregar una página
    $pdf->AddPage();

   

    $html = '';
    if (count($datos) > 0) {
         // Calcular el resumen
    $totalPrestamos = count($datos);
    $fechaInicioFormateada = date("d/m/Y", strtotime($fechaInicio));
    $fechaFinFormateada = date("d/m/Y", strtotime($fechaFin));

    // Resumen en la primera página
    $resumen = "<h3 style='text-align:center;'>Resumen de Prestamos</h3>";
    $resumen .= "<p style='text-align:justify;'>Este reporte muestra un total de <strong>$totalPrestamos</strong> prestamos realizados entre el <strong>$fechaInicioFormateada</strong> y el <strong>$fechaFinFormateada</strong>$lectorIndividual. A continuación, se detallan los prestamos registrados en el sistema:</p>";
    $pdf->writeHTML($resumen, true, false, true, false, '');
    
   

    
   // Contenido del reporte

    $html = '<h3 style="text-align:center;">Detalles de Prestamos</h3>';

   // Tabla de préstamos
   // Tabla de préstamos con estilo similar a Excel
   // Variable para contar registros por página
    $recordsPerPage = 12;
    $recordCount = 0;

    foreach ($datos as $index => $prestamo) {
        if ($recordCount % $recordsPerPage == 0) {
            //Iniciar tabla en cada nueva pagina
            if ($recordCount > 0){
                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Agregar nueva página
                $html = '<h3 style="text-align:center;">Detalles de Prestamos (continuación)</h3>';
            }
   $html .= '
   <table border="0.5" cellpadding="5" style="border-collapse:collapse; width:100%;">
       <thead>
           <tr style="background-color:#f2f2f2;">
               <th style="border:0.5px solid #ddd; text-align:center; width: 30px;">ID</th>';

               if (!$confirmarQueHayLector) {
                $html .= '
               <th style="border:0.5px solid #ddd; text-align:center; width: 100px;">Título</th>';
               } else {
                $html .= '
               <th style="border:0.5px solid #ddd; text-align:center; width: 150px;">Título</th>';
               }
               

               $html .= '
               <th style="border:0.5px solid #ddd; text-align:center;">ISBN</th>
               <th style="border:0.5px solid #ddd; text-align:center;">Cota</th>';


               if (!$confirmarQueHayLector) {
                $html .= '
                       <th style="border:0.5px solid #ddd; text-align:center; ">Nombre</th>
                       <th style="border:0.5px solid #ddd; text-align:center; ">Cédula</th>';
               }
                 
                 
        $html .= '
               <th style="border:0.5px solid #ddd; text-align:center;">Fecha de Préstamo</th>
               <th style="border:0.5px solid #ddd; text-align:center;">Fecha de Devolución</th>
               <th style="border:0.5px solid #ddd; text-align:center; width: 64px;">Estado</th>
           </tr>
       </thead>
       <tbody>';
        }
   // Llenar la tabla con los datos obtenidos
   

    $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9'; // Colores alternos
    $html .= '
    <tr style="background-color:' . $bgColor . ';">
        <td style="border:0.5px solid #ddd; text-align:center; width: 30px;">#' . $prestamo['id'] . '</td>';

        if (!$confirmarQueHayLector) {
            $html .= '
            <td style="border:0.5px solid #ddd; white-space: nowrap; width: 100px;">' . nl2br(htmlspecialchars(str_replace(".", "", $prestamo["titulo"]))) . '</td>';
           } else {
            $html .= '
             <td style="border:0.5px solid #ddd; white-space: nowrap; width: 150px;">' . nl2br(htmlspecialchars(str_replace(".", "", $prestamo["titulo"]))) . '</td>';
           }

    

    $html .= '
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['isbn'] . '</td>
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['cota'] . '</td>';

// Condición para mostrar u ocultar las columnas "Nombre" y "Cédula"
if (!$confirmarQueHayLector) {
 $html .= '
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['nombre'] . '</td>
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['cedula'] . '</td>';
}

$html .= '
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['fecha_inicio'] . '</td>
        <td style="border:0.5px solid #ddd; text-align:center;">' . $prestamo['fecha_fin'] . '</td>
        <td style="border:0.5px solid #ddd; text-align:center; width: 64px; font-weight:bold; color:' . ($prestamo['estado'] == 'vencido' || $prestamo['estado'] == 'Devolución tardía' ? '#FF0000' : '#00FF00') . ';">' . ucfirst(strtolower($prestamo['estado'])) . '</td>
    </tr>';
       
        $recordCount++;
    }
   $html .= '
       </tbody>
   </table>';
       
    }
   // Mostrar el total de préstamos como texto en negrita alineado a la derecha
   $html .= '<p style="text-align:right; font-weight:bold;">Total de Préstamos: ' . count($datos) . '</p>';
    // Escribir el contenido HTML
    $pdf->writeHTML($html, true, false, true, false, '');

    // Cerrar y generar el PDF
    $pdf->Output('reporte_prestamos.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conexion->close(); // Cerrar la conexión.
}
?>