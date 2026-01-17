<?php
include '../../../app/config/database.php'; // Asegúrate de que este archivo establece la conexión a la base de datos

$conexion = conexion();

session_start();

//para renovacion de prestamos
if (isset($_POST["cedula"]) && !empty($_POST["cedula"])) {

$response = [
    "unidades" => "",
    "periodo_renovaciones" => 0,
    "puedePedirRenovacion" => false,
];

// Validar que la conexión es válida
if (!$conexion) {
    $response["error"] = "Error en la conexión a la base de datos.";
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Validar que se ha recibido la cédula
if (!isset($_POST["cedula"]) || empty($_POST["cedula"])) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$cedula = $_POST["cedula"];
$sql = "SELECT (SELECT COUNT(*) WHERE p.fecha_inicio = CURDATE() AND p.estado = 4) AS renovacionesHoy, v.nombre 
        FROM prestamos p 
        JOIN visitantes v ON v.cedula = p.lector 
        WHERE v.cedula = ? 
        GROUP BY v.nombre";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $cedula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($row = $resultado->fetch_assoc()) {
    if (!isset($_SESSION["renovaciones_permitidas"])) {
        $_SESSION["renovaciones_permitidas"] = 0; // Valor por defecto
    }

    if ($row["renovacionesHoy"] <= $_SESSION["renovaciones_permitidas"]) {
        $response["puedePedirRenovacion"] = true;
    }
}

// Si el usuario puede pedir renovación, obtener las reglas de circulación
if ($response["puedePedirRenovacion"]) {
    $sql = "SELECT unidades, periodo_renovaciones FROM regla_de_circulacion LIMIT 1";
    $resultado = mysqli_query($conexion, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $response["unidades"] = $fila["unidades"];
        $response["periodo_renovaciones"] = (int) $fila["periodo_renovaciones"];
    }
}

// Responder con JSON
header('Content-Type: application/json');
echo json_encode($response);
}











//Para consultar si el usuario puede pedir prestamo

if (isset($_POST["cedulaPrestamo"]) && !empty($_POST["cedulaPrestamo"])){

    header('Content-Type: application/x-www-form-urlencoded');

    $response = [
        "unidades" => "",
        "periodo_prestamo" => 0,
        "puedePedirPrestamo" => false,
    ];
    
    // Validar que la conexión es válida
    if (!$conexion) {
        $response["error"] = "Error en la conexión a la base de datos.";
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // Validar que se ha recibido la cédula
    if (!isset($_POST["cedulaPrestamo"]) || empty($_POST["cedulaPrestamo"])) {
        
        echo json_encode(["error" => "Error al conectarse a la base de datos."]);
        exit;
    }
    
    $cedula = $_POST["cedulaPrestamo"];
    $sql = "SELECT 
    COUNT(CASE WHEN fecha_inicio = CURDATE() AND (estado = 1 OR estado = 4) THEN 1 END) AS prestamosHoy, 
    COUNT(CASE WHEN estado = 1 OR estado = 4 THEN 1 END) AS prestamosActivos, 
    COUNT(CASE WHEN estado = 3 THEN 1 END) AS prestamosVencidos, 
    v.nombre 
FROM prestamos p 
JOIN visitantes v ON v.cedula = p.lector 
WHERE v.cedula = ? 
GROUP BY v.nombre;";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($row = $resultado->fetch_assoc()) {
        if (!isset($_SESSION["periodo_prestamo"], $_SESSION["peticiones_permitidas"], $_SESSION["peticiones_diarias"])) {
            //$_SESSION["periodo_prestamo"] = 0; // Valor por defecto
            //$_SESSION["peticiones_permitidas"] = 0;
            //$_SESSION["peticiones_diarias"] = 0;
        }

        // Validar que los prestamos de hoy sean menores o iguales a la cantidad maxima por dia y que los totales menores o iguales a la cantidad maxima total de prestamo por usuario
        if ($row["prestamosVencidos"] > 0) {
            $mensaje = $row["prestamosVencidos"] > 1 ? "El usuario tiene " . $row["prestamosVencidos"] . " préstamos vencidos sin devolver, por lo que no puede solicitar un nuevo préstamo." : "El usuario tiene " . $row["prestamosVencidos"] . " préstamo vencido sin devolver, por lo que no puede solicitar un nuevo préstamo.";
            echo json_encode(["error" => $mensaje]);
            exit;
        }

        if ($row["prestamosActivos"] == $_SESSION["peticiones_permitidas"]) {
            echo json_encode(["error" => "El usuario ha excedido el límite general (" . $_SESSION["peticiones_permitidas"] . ") de préstamos."]);
            exit;
        }

        if ($row["prestamosHoy"] >= $_SESSION["peticiones_diarias"]) {
            echo json_encode(["error" => "El usuario ha excedido el límite diario (" . $_SESSION["peticiones_diarias"] . ") de préstamos."]);
            exit;
        }

        

        $response["puedePedirPrestamo"] = true;
    } else{
        $response["puedePedirPrestamo"] = true;
    }
    
    // Si el usuario puede pedir renovación, obtener las reglas de circulación
    if ($response["puedePedirPrestamo"]) {
        $sql = "SELECT unidades, periodo_prestamo FROM regla_de_circulacion LIMIT 1";
        $resultado = mysqli_query($conexion, $sql);
    
        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);
            $response["unidades"] = $fila["unidades"];
            $response["periodo_prestamo"] = (int) $fila["periodo_prestamo"];
        }
    }
    
    // Responder con JSON
    
    echo json_encode($response);
}


?>
