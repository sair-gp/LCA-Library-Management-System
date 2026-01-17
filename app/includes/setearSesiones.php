<?php
// Datos de la biblioteca
if (!isset($_SESSION["datos_biblioteca"])) {
    $query = "SELECT * FROM datos_biblioteca";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado) { // Check if the query was successful
        if (mysqli_num_rows($resultado) > 0) {
            $fila = $resultado->fetch_assoc();

            $_SESSION["datos_biblioteca"] = [
                "nombre" => $fila["nombre"] ?? "",
                "abreviacion" => $fila["abreviacion"] ?? "",
                "logo" => $fila["logo"] ?? "",
                "direccion" => $fila["direccion"] ?? "",
                "telefono" => $fila["telefono"] ?? "",
                "correo" => $fila["correo"] ?? ""
            ];
        } else {
            //Enviar a la configuracion para que ingrese los datos:
        //echo '<script type="text/javascript">';
        //echo 'window.location.href = "app/views/configuracion.php";';
        //echo '</script>';
        }
    } else {
        

        // Handle the query error
        error_log("Error en la consulta a la base de datos.: " . mysqli_error($conexion));
        echo "Ocurrió un error al intentar obtener la información.";
    }
} 



//Inicializar las variables de sesion de la regla de circulacion:

$query = "SELECT * FROM regla_de_circulacion";
$resultadoRDC = mysqli_query($conexion, $query);

if (mysqli_num_rows($resultadoRDC) > 0) {
    $fila = mysqli_fetch_assoc($resultadoRDC);

    $periodoPrestamo = $fila["periodo_prestamo"];
    $unidades = $fila["unidades"];
    $periodoRenovaciones = $fila["periodo_renovaciones"];
    $renovacionesPermitidas = $fila["renovaciones_permitidas"];
    $peticionesPermitidas = $fila["peticiones_permitidas"];
    $peticionesDiarias = $fila["peticiones_diarias"];
    $peticionesPorRegistro = $fila["peticiones_por_registro"];
    $dolarBCV = $fila["dolarBCV"];
    $montoRetraso = $fila["monto_por_dia_retraso"];
    $montoDano = $fila["monto_por_danio"];
    $montoPerdida = $fila["monto_por_perdida_material"];

    if (!isset($_SESSION["periodo_prestamo"], $_SESSION["unidades"], $_SESSION["periodo_renovaciones"], $_SESSION["renovaciones_permitidas"], $_SESSION["peticiones_permitidas"], $_SESSION["peticiones_diarias"], $_SESSION["peticiones_por_registro"], $_SESSION["dolarBCV"], $_SESSION["monto_por_perdida_material"], $_SESSION["monto_por_danio"], $_SESSION["monto_por_dia_retraso"])){
        $_SESSION["periodo_prestamo"] = $periodoPrestamo;
        $_SESSION["unidades"] = $unidades; 
        $_SESSION["periodo_renovaciones"] = $periodoRenovaciones;
        $_SESSION["renovaciones_permitidas"] = $renovacionesPermitidas; 
        $_SESSION["peticiones_permitidas"] = $peticionesPermitidas; 
        $_SESSION["peticiones_diarias"] = $peticionesDiarias; 
        $_SESSION["peticiones_por_registro"] = $peticionesPorRegistro;
        $_SESSION["dolarBCV"] = $dolarBCV;
        $_SESSION["monto_por_perdida_material"] = $montoPerdida;
        $_SESSION["monto_por_danio"] = $montoDano;
        $_SESSION["monto_por_dia_retraso"] = $montoRetraso;
    }
}

//Volver a verificar permisos del usuario

