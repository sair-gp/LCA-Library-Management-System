<?php
require_once('tcpdf/tcpdf.php'); // Asegúrate de tener TCPDF en tu proyecto
date_default_timezone_set('America/Caracas');
// Variables simuladas (reemplázalas con consultas a la base de datos)
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


$anio = date("Y");
$prestamos = 520;
$revistas = 85;
$desincorporados = 10;
$actividades = 5;
$asistencia = 980;
$nuevos_usuarios = 40;
$consultas_digitales = 150;
$sugerencias = 25;
$aumento_asistencia = 12; // % respecto al mes anterior
$aumento_digitales = 15; // % respecto al mes anterior

// Análisis automático basado en reglas
$analisis = "";
if ($aumento_asistencia > 10) {
  $analisis .= "✔ Se observa un incremento del $aumento_asistencia% en la asistencia respecto al mes anterior.<br>";
}
if ($aumento_digitales > 10) {
  $analisis .= "✔ El uso de recursos digitales creció un $aumento_digitales%, indicando mayor interés en consultas en línea.<br>";
}
if ($sugerencias > 20) {
  $analisis .= "✔ Se recibieron $sugerencias sugerencias, principalmente sobre ampliación de horarios y adquisición de literatura juvenil.<br>";
}

// Recomendaciones automáticas
$recomendaciones = "";
if ($sugerencias > 20) {
  $recomendaciones .= "🔹 Evaluar la posibilidad de extender el horario de atención.<br>";
}
if ($aumento_digitales > 10) {
  $recomendaciones .= "🔹 Continuar promoviendo los recursos digitales para mantener su crecimiento.<br>";
}
if ($desincorporados > 5) {
  $recomendaciones .= "🔹 Reemplazar los títulos retirados con nuevas adquisiciones relevantes.<br>";
}

// Crear el PDF
$pdf = new TCPDF();
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Título
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Informe Mensual - Biblioteca Central", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "$mes $anio", 0, 1, 'C');
$pdf->Ln(5);

// Resumen Ejecutivo
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Resumen Ejecutivo", 0, 1, 'L');

$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML("
📚 <b>Préstamos:</b> $prestamos libros, $revistas revistas<br>
🛑 <b>Libros desincorporados:</b> $desincorporados<br>
📅 <b>Actividades:</b> $actividades eventos (charlas y talleres)<br>
👥 <b>Asistencia:</b> $asistencia usuarios (+$aumento_asistencia% respecto a diciembre)<br>
📥 <b>Nuevos usuarios:</b> $nuevos_usuarios registrados<br>
🌐 <b>Consultas digitales:</b> $consultas_digitales accesos<br>
💬 <b>Sugerencias:</b> $sugerencias (principalmente sobre horarios y literatura juvenil)<br>
");

// Análisis y Tendencias
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Análisis y Tendencias", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML($analisis);

// Recomendaciones
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Recomendaciones", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->writeHTML($recomendaciones);

// Pie de Página
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 10);
$pdf->writeHTML("
📞 <b>Contacto:</b> +XX XXXX-XXXX | 📧 contacto@biblioteca.com | 🌐 www.biblioteca.com<br>
📅 <b>Fecha:</b> 1 de febrero de $anio
");

// Generar el PDF y descargarlo
$pdf->Output("Informe_Biblioteca_$mes$anio.pdf", 'I');
