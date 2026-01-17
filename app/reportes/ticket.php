<?php
// Evitar salida de datos antes del PDF
ob_start();

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');
require_once("../config/database.php");
session_start();
// Conectar a la base de datos
$conexion = conexion();


//obtener nombre de la biblioteca
// 1. Obtenemos el valor de la variable de sesión "nombre" dentro de "datos_biblioteca".
// Si no existe, asignamos una cadena vacía (""). Esto evita errores si la clave no está definida.
$nombre = $_SESSION["datos_biblioteca"]["nombre"] ?? "";

// 2. Buscamos la posición de la primera comilla doble (") en la cadena.
// La función `strpos` devuelve la posición (índice) de la primera ocurrencia de la comilla.
// Si no encuentra la comilla, devuelve `false`.
$posicion = strpos($nombre, '"');

// 3. Verificamos si se encontró una comilla en la cadena.
// Si `strpos` no devuelve `false`, significa que hay al menos una comilla.
if ($posicion !== false) {
    // 4. Si hay una comilla, dividimos la cadena en dos partes:
    //    - La primera parte es desde el inicio de la cadena hasta justo antes de la comilla.
    //    - La segunda parte es desde el carácter después de la comilla hasta el final de la cadena.
    // Luego, unimos las dos partes con `<br>"` en el medio.
    $nombre_con_salto = substr($nombre, 0, $posicion) . '<br>"' . substr($nombre, $posicion + 1);
} else {
    // 5. Si no se encuentra ninguna comilla, no hacemos cambios a la cadena.
    $nombre_con_salto = $nombre;
}

// 6. Finalmente, mostramos el resultado dentro de un div con la clase "header".
// Si se encontró una comilla, la cadena tendrá un salto de línea (`<br>`) antes de la comilla.
// Si no se encontró ninguna comilla, la cadena se mostrará sin cambios.



// Obtener el ID del préstamo
$idPrestamo = $_GET["idPrestamo"];
$responsable = $_SESSION["nombre"] . " " . $_SESSION["apellido"];

$sql = "SELECT l.isbn, ej.cota, CASE
        WHEN l.es_obra_completa = 1 THEN
            l.titulo
        ELSE
            CASE
                WHEN REGEXP_REPLACE(v.nombre, '[0-9]', '') = l.titulo THEN
                    CONCAT(l.titulo, ' ', 'volumen ', v.numero)
                ELSE
                    CONCAT(l.titulo, ' \"', v.nombre, '\". ')
            END
    END AS titulo, GROUP_CONCAT(DISTINCT CONCAT(a.nombre) SEPARATOR ', ') AS autores, v.isbn_vol, p.fecha_inicio , p.fecha_fin, vi.nombre as visitanteLector, vi.cedula, l.es_obra_completa FROM libros l JOIN volumen v ON l.isbn = v.isbn_obra JOIN ejemplares ej ON ej.isbn_vol = v.id JOIN prestamos p ON p.cota = ej.id JOIN visitantes vi ON vi.cedula = p.lector JOIN libro_autores la ON la.isbn_libro = l.isbn JOIN autores a ON a.id = la.id_autor WHERE p.id = ?;";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param("i", $idPrestamo);
    $stmt->execute();

    $datos = [];

    if ($resultado = $stmt->get_result()){
        $fila = $resultado->fetch_assoc();

        $datos = [
            "isbn" => $fila["isbn"],
            "cota" => $fila["cota"],
            "titulo" => $fila["titulo"],
            "autores" => $fila["autores"],
            "isbn_vol" => $fila["isbn_vol"],
            "fecha_inicio" => $fila["fecha_inicio"],
            "fecha_fin" => $fila["fecha_fin"],
            "visitanteLector" => $fila["visitanteLector"],
            "cedula" => $fila["cedula"],
            "es_obra_completa" => intval($fila["es_obra_completa"]) ?? 1
        ];

        if ($datos["es_obra_completa"] === 1 ) {
            $isbnTr = '
            <tr>
                <td><strong>ISBN:</strong></td>
                <td>978-3-16-148410-0</td>
            </tr>
            ';
        } else {
            $isbnTr = '
            <tr>
                <td><strong>ISBN(obra):</strong></td>
                <td>'. ($datos["isbn"] ?? "") .'</td>
            </tr>
            <tr>
                <td><strong>ISBN(volumen):</strong></td>
                <td>'. ($datos["isbn_vol"] ?? "") .'</td>
            </tr>
            ';
        }


    }





// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(80, 125), true, 'UTF-8', false);

// Configurar información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Biblioteca Central');
$pdf->SetTitle('Ticket de Préstamo de Libro');
$pdf->SetSubject('Ticket de Préstamo');
$pdf->SetKeywords('préstamo, libro, biblioteca');

// Configurar márgenes
$pdf->SetMargins(3, 5, 3); // Márgenes pequeños
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// Configurar auto page breaks
$pdf->SetAutoPageBreak(TRUE, 5);

// Configurar fuente
$pdf->SetFont('helvetica', '', 8); // Fuente legible pero compacta

// Añadir una página
$pdf->AddPage();

// Contenido del ticket (HTML con diseño mejorado)
$html = '
<style>
    .header {
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        margin-bottom: 2px;
        color: #333;
    }
    .section {
        margin-bottom: 0px;
    }
    .section-title {
        font-size: 8px;
        font-weight: bold;
        margin-bottom: 2px;
        color: #555;
    }
    .section-content {
        font-size: 7px;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table td {
        padding: 2px;
        border-bottom: 1px solid #ddd;
    }
    .footer {
        text-align: center;
        font-size: 7px;
        margin-top: 0px;
        color: #777;
    }
</style>

<div class="header">'. ($nombre_con_salto ?? "Biblioteca") .'</div>
<hr style="border-top: 1px solid #000;">

<div class="section">
    <div class="section-title">DETALLES DEL LIBRO</div>
    <div class="section-content">
        <table>
            <tr>
                <td><strong>Título:</strong></td>
                <td>'. ($datos["titulo"] ?? "") .'</td>
            </tr>
            <tr>
                <td><strong>Autor(es):</strong></td>
                <td>'. ($datos["autores"] ?? "") .'</td>
            </tr>
            '. $isbnTr .'
            <tr>
                <td><strong>Cota:</strong></td>
                <td>'. ($datos["cota"] ?? "") .'</td>
            </tr>
        </table>
    </div>
</div>
<hr style="border-top: 1px solid #000;">

<div class="section">
    <div class="section-title">DATOS DEL PRÉSTAMO</div>
    <div class="section-content">
        <table>
            <tr>
                <td><strong>ID Préstamo:</strong></td>
                <td>#'. ($idPrestamo ?? "") .'</td>
            </tr>
            <tr>
                <td><strong>Encargado:</strong></td>
                <td>'. $responsable .'</td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong></td>
                <td>'. ($datos["fecha_inicio"] ?? "") .'</td>
            </tr>
            <tr>
                <td><strong>Fecha Devolución:</strong></td>
                <td>2'. ($datos["fecha_fin"] ?? "") .'</td>
            </tr>
        </table>
    </div>
</div>
<hr style="border-top: 1px solid #000;">

<div class="section">
    <div class="section-title">DATOS DEL USUARIO</div>
    <div class="section-content">
        <table>
            <tr>
                <td><strong>Nombre:</strong></td>
                <td>'. ($datos["visitanteLector"] ?? "") .'</td>
            </tr>
            <tr>
                <td><strong>Cédula:</strong></td>
                <td>'. ($datos["cedula"] ?? "") .'</td>
            </tr>
        </table>
    </div>

    <div class="footer">Gracias por utilizar nuestros servicios. ¡Disfrute su lectura!</div>
</div>



';

// Escribir el contenido HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Limpiar el buffer de salida
ob_end_clean();

// Enviar el PDF al navegador
$pdf->Output('ticket_prestamo_libro.pdf', 'I');