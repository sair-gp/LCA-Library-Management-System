<?php
include "../model/analisisBiblioteca.php";
require_once('tcpdf/tcpdf.php'); // Asegúrate de tener TCPDF en tu proyecto
date_default_timezone_set('America/Caracas');
// Variables simuladas (reemplázalas con consultas a la base de datos)
$anio = date("Y");
$mes = match (date("m")) {
  "01" => "Enero",
  "02" => "Febrero",
  "03" => "Marzo",
  "04" => "Abril",
  "05" => "Mayo",
  "06" => "Junio",
  "07" => "Julio",
  "08" => "Agosto",
  "09" => "Septiembre",
  "10" => "Octubre",
  "11" => "Noviembre",
  "12" => "Diciembre",
  default => "Mes no válido",
};

class MYPDF extends TCPDF
{
  // Encabezado
  public function Header()
  {
    // Logo izquierdo
    $image_file = './img/lc.jpg';
    $this->Image($image_file, 15, 5, 22, 25, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Logo derecho
    $path = dirname(__FILE__);
    $logo = $path . '/img/red_biblio.jpg';
    $this->Image($logo, 175, 5, 22, 25, 'JPG');

    // Fuente del encabezado
    $this->SetFont('helvetica', '', 12);
    $this->SetY(10);

    // Título principal
    $this->Cell(0, 5, 'Biblioteca Pública "Luisa Cáceres de Arismendi"', 0, 1, 'C');
    $this->Cell(0, 5, 'Comunidad Cruz Salmerón Acosta, Calle Punta de Mata', 0, 1, 'C');
    $this->Cell(0, 5, 'Al lado de la U.E "ESTADO MONAGAS"', 0, 1, 'C');
    $this->Cell(0, 5, 'Cumaná, Edo. Sucre - Municipio Sucre, Parroquia Santa Inés', 0, 1, 'C');

    // Fecha actual
    $fecha_actual = date("d-m-Y");
    $this->SetFont('helvetica', 'B', 8);
    $this->SetXY(145, 32);
    $this->SetTextColor(34, 68, 136);
    $this->Write(0, 'Cumaná - Sucre ' . $fecha_actual);
  }

  public function Footer()
  {
    $anio = date('Y');
    // Position at 15 mm from bottom
    $this->SetY(-20);
    $this->SetX(50);

    // Set font
    $this->SetFont('helvetica', 'I', 8);

    $this->SetX(50);
    $this->writeHTML("
📞 <b>Contacto:</b> +XX XXXX-XXXX | 📧 contacto@biblioteca.com | 🌐 www.biblioteca.com<br>");
    $this->SetY(-15);
    $this->SetX(80);
    $this->writeHTML("📅 <b>Fecha:</b> 1 de febrero de $anio");


    // Page number
    $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
  }
}

// Crear PDF en formato vertical
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Biblioteca');
$pdf->SetTitle('Informe Mensual');
$pdf->SetSubject('Reporte de Actividades');

// Configurar márgenes y agregar página
$pdf->SetMargins(10, 50, 10); // Margen superior más alto para el header
$pdf->AddPage('', 'LETTER'); // Agrega una página en formato vertical

$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Contenido del informe
$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(10);
$pdf->Write(0, "Estimados usuarios,\n\n");
$pdf->Write(0, "Con gran satisfacción presentamos el Informe Mensual de Actividades de la Biblioteca correspondiente al mes de $mes.\n");
$pdf->Write(0, "Agradecemos a todos los usuarios por su constante apoyo y participación.\n\n");
$pdf->SetX(100);
$pdf->Write(0, "Atentamente");
$pdf->SetX(100);
$pdf->Write(35, "[Tu Nombre]");
$pdf->SetX(98);
$pdf->Write(45, "Bibliotecario(a)");

$pdf->SetX(40);
$pdf->Ln(5);
$pdf->SetXY(90, 100);
$pdf->Write(0, "__________________\n");
$pdf->Ln(15);


/*
// Datos ficticios (ajusta con tus variables dinámicas)
$prestamos = 120;
$revistas = 30;
$desincorporados = 5;
$actividades = 4;
$asistencia = 250;
$aumento_asistencia = 11; // En porcentaje
$donaciones = 10;




// Insertar estadísticas con iconos en Unicode
$pdf->Ln(10);
$pdf->WriteHTML("
<b>📚 Préstamos:</b> $prestamos libros, $revistas revistas<br>
<b>🛑 Libros desincorporados:</b> $desincorporados<br>
<b>👥 Libros donados:</b> $donaciones libros<br>
<b>📅 Actividades:</b> $actividades eventos (charlas y talleres)<br>
<b>👥 Asistencia:</b> $asistencia usuarios (+$aumento_asistencia% respecto al mes anterior)<br>


");
*/
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Análisis y Tendencias", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML($analisis);

// Generar PDF en el navegador
$pdf->Output('informe_mensual.pdf', 'I');
