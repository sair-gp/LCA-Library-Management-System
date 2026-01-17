<?php

date_default_timezone_set("America/Caracas");

if (session_status() == PHP_SESSION_NONE) {
    // La sesión no está activa, podemos iniciarla
    session_start();
    //echo "Sesión iniciada correctamente.";
} else if (session_status() == PHP_SESSION_ACTIVE) {
    // La sesión ya está activa, no es necesario iniciarla
   // echo "La sesión ya está activa.";
} else if (session_status() == PHP_SESSION_DISABLED){
    //echo "Las sesiones estan deshabilitadas";
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir la conexión a la base de datos
    require_once '../../config/database.php'; // Ajusta la ruta según tu estructura
    $conexion = conexion(); // Establecer la conexión

    // Incluir la clase Multas
    require_once '../../model/multas.php'; // Ajusta la ruta según tu estructura

    // Obtener los datos del formulario
    $multa_id = isset($_POST['idMulta']) ? intval($_POST['idMulta']) : 0;
    $monto_pagado = isset($_POST['monto']) ? floatval($_POST['monto']) : 0.0;
    $tipo_pago = $_POST['tipoPago'] ?? '';
    $tasa_del_dia = isset($_SESSION['dolarBCV']) ? floatval($_SESSION['dolarBCV']) : 0.0;

    // Validar los datos recibidos
    if ($multa_id <= 0 || $monto_pagado <= 0 || empty($tipo_pago) || $tasa_del_dia <= 0) {
        echo json_encode(['success' => false, 'message' => "Datos inválidos. Id: $multa_id. Tipo de pago: $tipo_pago. Monto pagado: bs.$monto_pagado. Tasa del dia: $tasa_del_dia."]);
        exit;
    }

    // Crear una instancia de la clase Multas
    $multasHandler = new Multas($conexion);

    // Llamar al método pagarMulta
    $resultado = $multasHandler->pagarMulta($multa_id, $monto_pagado, $tipo_pago, $tasa_del_dia);


    //  Registrar multa en el historial de acciones

    require_once "../../model/historial.php";
    $historial = new Historial($conexion);
    $hoy = Date("Y-m-d");
   
    $responsable = $_SESSION["cedula"];
    $accion = 20;//equivale a pago de multa

    //obtener nombre del visitante que realizo el pago para los detalles del historial
    $datos = $multasHandler->obtenerVisitante($resultado["pagoId"], "WHERE pa.id = ?;");//para obtener de la tabla de pagos
    $detalles = "Titulo: ". $datos["titulo"] ." Visitante: ". $datos["nombre"] .". Tipo de multa: ". $datos["motivo"] .". Monto: bs". $datos["monto_pagado"] .".";
    $historial->registrar_accion($responsable, $detalles, $hoy, $accion);


    // Enviar la respuesta como JSON
    echo json_encode($resultado);

    // Cerrar la conexión (si es necesario)
    $conexion->close();
} else {
    // Si no es una solicitud POST, devolver un error
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}



