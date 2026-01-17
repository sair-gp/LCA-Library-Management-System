<?php
include '../../../app/config/database.php';

$conn = conexion();

header('Content-Type: application/json');

function traducirFecha($fecha) {
    $meses = array(
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    );

    // Intentar crear un objeto DateTime con la fecha proporcionada
    $fecha_obj = date_create($fecha);

    // Verificar si la fecha es válida
    if ($fecha_obj !== false) {
        // Formatear la fecha en el formato deseado
        $fecha_formateada = $fecha_obj->format('Y-m-d H:i:s');

        $dia = $fecha_obj->format('j');
        $mes = $meses[(int)$fecha_obj->format('n')];
        $año = $fecha_obj->format('Y');
        $hora = $fecha_obj->format('H');
        $minutos = $fecha_obj->format('i');

        return $dia . ' de ' . $mes . ' del ' . $año . ' a las ' . $hora . ':' . $minutos;
    } else {
        // Si la fecha no es válida, devolver el valor original
        return $fecha;
    }
}


$input = json_decode(file_get_contents("php://input"), true);
$cedula = $input["cedulaAsistencia"] ?? "";

if (!$cedula) {
    echo json_encode(["error" => "Cédula no proporcionada"]);
    exit;
}


if (!$conn) {
    echo json_encode(["error" => "Error de conexión: " . mysqli_connect_error()]);
    exit;
}



$query = "SELECT (SELECT nombre FROM visitantes WHERE cedula = '" . mysqli_real_escape_string($conn, $cedula) . "') AS nombre, origen, descripcion, fecha FROM asistencias WHERE cedula_visitante = '" . mysqli_real_escape_string($conn, $cedula) . "'";
$result = mysqli_query($conn, $query);

$visitante = "";
$tbody = "";

if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        //if (!isset($visitante)){
            $visitante = $row['nombre'];
        //}
        $tbody .= "<tr>";
        $tbody .= "<td>" . htmlspecialchars($row['origen']) . "</td>";
        $tbody .= "<td>" . htmlspecialchars($row['descripcion']) . "</td>";
        $tbody .= "<td>" . htmlspecialchars(traducirFecha($row['fecha'])) . "</td>";
        $tbody .= "</tr>";
    } 
} else {
    $tbody .= "<tr>";
    $tbody .= "<td colspan='3'><div class='alert alert-warning text-center'>No se encontraron asistencias para este visitante.</div></td>";
    $tbody .= "</tr>";
}



mysqli_close($conn);

echo json_encode(["tbody" => $tbody, "visitante" => $visitante]);
?>
