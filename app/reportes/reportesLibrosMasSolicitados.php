<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de incluir la ruta correcta a TCPDF
require_once "../config/database.php";
$conexion = conexion();
require_once "../model/reporteGeneral.php";
session_start();

// Iniciar objeto para generar reporte
$reporte = new ReporteGeneral($conexion);

// Validar fechas
$fechaInicio = $_POST["fechaInicioLibros"] ?? Date("Y-m-d");
$fechaFin = $_POST["fechaFinLibros"] ?? Date("Y-m-d");
if (!strtotime($fechaInicio) || !strtotime($fechaFin)) {
    throw new Exception("Las fechas proporcionadas no son válidas.");
}
if ($fechaInicio > $fechaFin) {
    throw new Exception("La fecha de inicio no puede ser mayor que la fecha de fin.");
}

// Datos para el reporte
$categoria = $_POST["categoriaLibros"] ?? 'todos';

// Construir filtro dinámico
$subFiltro = "";
$placeholders = [];
$confirmarHayCategoria = false;
if ($categoria != "todos") {
    $subFiltro .= " AND c.id = ?";
    $placeholders[] = $categoria;
    $confirmarHayCategoria = true;

}

$filtro = "WHERE p.fecha_inicio BETWEEN DATE(?) AND DATE(?) $subFiltro GROUP BY l.isbn, l.titulo, vo.nombre, vo.numero, l.es_obra_completa ORDER BY total_prestamos DESC";
$values = array_merge([$fechaInicio, $fechaFin], $placeholders);

// Obtener datos del reporte
try {
    $datos = $reporte->obtenerDatosParaReporte("librosMasSolicitados", $filtro, $values);

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
    $pdf->SetTitle('Reporte de Libros Más Solicitados');
    $pdf->SetSubject('Reporte de Libros Más Solicitados');
    $pdf->SetKeywords('TCPDF, PDF, libros, reporte');

    // Establecer márgenes
    $pdf->SetMargins(10, 40, 10); // Márgenes más ajustados
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
        $totalLibros = count($datos);
        $fechaInicioFormateada = date("d/m/Y", strtotime($fechaInicio));
        $fechaFinFormateada = date("d/m/Y", strtotime($fechaFin));

        $categoriaInfo = "";
        if ($confirmarHayCategoria) {
            $categoriaInfo = " en la categoría <strong>" . $datos[0]["categoria"] . "</strong>";
        }

        $libroTexto = $totalLibros > 1 ? "los <strong>$totalLibros</strong> libros más solicitados" : "el libro más solicitado";

        // Resumen en la primera página
        $resumen = "<h3 style='text-align:center;'>Resumen de Libros Más Solicitados</h3>";
        $resumen .= "<p style='text-align:justify;'>Este reporte muestra $libroTexto$categoriaInfo entre el <strong>$fechaInicioFormateada</strong> y el <strong>$fechaFinFormateada</strong>. A continuación, se detallan los libros más solicitados registrados en el sistema:</p>";
        $pdf->writeHTML($resumen, true, false, true, false, '');

        // Contenido del reporte
        $html .= '<h3 style="text-align:center;">Detalles de Libros Más Solicitados</h3>';

        // Variable para contar registros por página
        $recordsPerPage = 12;
        $recordCount = 0;

        foreach ($datos as $index => $libro) {
            if ($recordCount % $recordsPerPage == 0) {
                // Iniciar tabla en cada nueva página
                if ($recordCount > 0) {
                    $html .= '</tbody></table>';
                    $pdf->writeHTML($html, true, false, true, false, '');
                    $pdf->AddPage(); // Agregar nueva página
                    $html = '<h3 style="text-align:center;">Detalles de Libros Más Solicitados (continuación)</h3>';
                }
                $html .= '<table border="0.5" cellpadding="5" style="border-collapse:collapse; width:100%;" split_table_row="true">
                    <thead>
                        <tr style="background-color:#f2f2f2;">
                            <th style="border:0.5px solid #ddd; text-align:center; width: 30px;">#</th>
                            <th style="border:0.5px solid #ddd; text-align:center;">ISBN</th>
                            <th style="border:0.5px solid #ddd; text-align:center; width: 150px;">Título</th>
                            <th style="border:0.5px solid #ddd; text-align:center; width: 140px;">Categorías</th>
                            <th style="border:0.5px solid #ddd; text-align:center;">Total Préstamos</th>
                        </tr>
                    </thead>
                    <tbody>';
            }

            $bgColor = ($index % 2 == 0) ? '#ffffff' : '#f9f9f9';
            $html .= '<tr style="background-color:' . $bgColor . ';">
                <td style="border:0.5px solid #ddd; text-align:center; width: 30px;">' . ($index + 1) . '</td>
                <td style="border:0.5px solid #ddd; text-align:center;">' . $libro['isbn'] . '</td>
                <td style="border:0.5px solid #ddd; width: 150px;">' . $libro['titulo'] . '</td>
                <td style="border:0.5px solid #ddd; text-align:center; width: 140px;">' . $libro['categoria'] . '</td>
                <td style="border:0.5px solid #ddd; text-align:center;">' . $libro['total_prestamos'] . '</td>
            </tr>';

            $recordCount++;
        }
        // Cerrar la última tabla
        $html .= '</tbody></table>';
    } 
    // Escribir el contenido HTML
    $pdf->writeHTML($html, true, false, true, false, '');
    $mensajeFinal = "";
    if (count($datos) > 0){
        $mensajeFinal .= '<p style="text-align:right; font-weight:bold;">Total de Libros: ' . count($datos) . '</p>';
    } else {
        $fechaInicioFormateada = date("d/m/Y", strtotime($fechaInicio));
        $fechaFinFormateada = date("d/m/Y", strtotime($fechaFin));
        $categoriaSeleccionada = $confirmarHayCategoria ? "No se han encontrado libros de la categoria seleccionada" : "No se han encontrado libros";
        $mensajeFinal .= '<p style="text-align:center; font-weight:bold;">' . $categoriaSeleccionada .' entre el <strong>'. $fechaInicioFormateada .'</strong> y el <strong>'. $fechaFinFormateada .'</strong>.</p>';
    }
    // Mostrar el total de libros como texto en negrita alineado a la derecha
    $pdf->writeHTML($mensajeFinal, true, false, true, false, '');

    // Cerrar y generar el PDF
    $pdf->Output('reporte_libros_mas_solicitados.pdf', 'I'); // 'I' para abrir en el navegador, 'D' para descargar

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} finally {
    $conexion->close(); // Cerrar la conexión.
}
?>