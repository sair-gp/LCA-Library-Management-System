<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de incluir la ruta correcta a TCPDF
require_once "../config/database.php";
$conexion = conexion();
require_once "../model/reporteGeneral.php";
session_start();

// Iniciar objeto para generar reporte
$reporte = new ReporteGeneral($conexion);

// Validar fechas
$fechaInicio = $_POST["fechaInicioVisitas"] ?? Date("Y-m-d");
$fechaFin = $_POST["fechaFinVisitas"] ?? Date("Y-m-d");
if (!strtotime($fechaInicio) || !strtotime($fechaFin)) {
    throw new Exception("Las fechas proporcionadas no son válidas.");
}
if ($fechaInicio > $fechaFin) {
    throw new Exception("La fecha de inicio no puede ser mayor que la fecha de fin.");
}

// Obtener parámetros del formulario
$horario = $_POST["horarioVisitas"] ?? 'todos';
$sexo = $_POST["horarioSexo"] ?? 'todos';

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

$filtro = "WHERE asi.fecha BETWEEN ? AND ? $subFiltro ORDER BY asi.fecha";
$values = array_merge([$fechaInicio, $fechaFin], $placeholders);

// Obtener datos del reporte
try {
    $datos = $reporte->obtenerDatosParaReporte("visitasPorHorario", $filtro, $values);

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
    $pdf->SetTitle('Reporte de Visitas');
    $pdf->SetSubject('Reporte de Visitas');
    $pdf->SetKeywords('TCPDF, PDF, visitas, reporte');

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

    // Formatear fechas
    $fechaInicioFormateada = date("d/m/Y", strtotime($fechaInicio));
    $fechaFinFormateada = date("d/m/Y", strtotime($fechaFin));

    // Determinar el texto del horario
    $horarioTexto = "";
    if ($horario != "todos") {
        $horarioTexto = "en el horario de la " . ($horario == "manana" ? "mañana" : ($horario == "tarde" ? "tarde" : "noche"));
    }

    // Resumen en la primera página
    $resumen = "<h3 style='text-align:center;'>Resumen de Visitas</h3>";
    $resumen .= "<p style='text-align:justify;'>Este reporte muestra las visitas $horarioTexto entre las fechas de <strong>$fechaInicioFormateada</strong> y <strong>$fechaFinFormateada</strong>. A continuación, se detallan las visitas registradas en el sistema:</p>";
    $pdf->writeHTML($resumen, true, false, true, false, '');

    // Contenido del reporte
    $html = '<h3 style="text-align:center;">Detalles de Visitas</h3>';

    // Variable para contar registros por página
    $recordsPerPage = 12;
    $recordCount = 0;

    foreach ($datos as $index => $visita) {
        if ($recordCount % $recordsPerPage == 0) {
            // Iniciar tabla en cada nueva página
            if ($recordCount > 0) {
                $html .= '</tbody></table>';
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Agregar nueva página
                $html = '<h3 style="text-align:center;">Detalles de Visitas (continuación)</h3>';
            }
            $html .= '<table border="0.5" cellpadding="5" style="border-collapse:collapse; width:100%;" split_table_row="true">
                <thead>
                    <tr style="background-color:#f2f2f2;">
                        <th style="border:0.5px solid #ddd; text-align:center; width: 30px;">ID</th>
                        <th style="border:0.5px solid #ddd; text-align:center;">Nombre</th>
                        <th style="border:0.5px solid #ddd; text-align:center;">Cédula</th>
                        <th style="border:0.5px solid #ddd; text-align:center;">Hora</th>
                        <th style="border:0.5px solid #ddd; text-align:center;">Origen</th>
                        <th style="border:0.5px solid #ddd; text-align:center; width: 155px;">Descripción</th>
                    </tr>
                </thead>
                <tbody>';
        }

        // Formatear la hora
        $hora = date("H:i:s", strtotime($visita['fecha']));

        $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9';
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="border:0.5px solid #ddd; text-align:center; width: 30px;">#' . $visita['id'] . '</td>
            <td style="border:0.5px solid #ddd;">' . $visita['nombre'] . '</td>
            <td style="border:0.5px solid #ddd; text-align:center;">' . $visita['cedula_visitante'] . '</td>
            <td style="border:0.5px solid #ddd; text-align:center;">' . $hora . '</td>
            <td style="border:0.5px solid #ddd;">' . $visita['origen'] . '</td>
            <td style="border:0.5px solid #ddd; width: 155px;">' . $visita['descripcion'] . '</td>
        </tr>';

        $recordCount++;
    }

    // Cerrar la última tabla
    $html .= '</tbody></table>';

    // Escribir el contenido HTML
    $pdf->writeHTML($html, true, false, true, false, '');

    // Mostrar el total de visitas como texto en negrita alineado a la derecha
    $pdf->writeHTML('<p style="text-align:right; font-weight:bold;">Total de Visitas: ' . count($datos) . '</p>', true, false, true, false, '');

    // Cerrar y generar el PDF
    $pdf->Output('reporte_visitas.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conexion->close(); // Cerrar la conexión.
}
?>