<?php
require_once "../../config/database.php";
$conexion = conexion();


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =============================================
    // VALIDACIÓN DE DATOS (antes de todo)
    // =============================================
    $errores = [];

    // 1. Validar campos básicos
    $camposRequeridos = [
        'nombre-estanteria' => 'Nombre de estantería',
        'codigo-estanteria' => 'Código',
        'num-filas' => 'Número de filas',
        'capacidad-fila' => 'Capacidad por fila',
        'tipo-clasificacion' => 'Tipo de clasificación'
    ];

    foreach ($camposRequeridos as $campo => $nombre) {
        if (empty($_POST[$campo])) {
            $errores[] = "El campo <b>$nombre</b> es obligatorio";
        }
    }

    // 2. Si no hay errores básicos, procesar valores
    if (empty($errores)) {
        // Procesar con sanitización básica
        $descripcionEstanteria = trim($_POST['nombre-estanteria']);
        $codigo = trim($_POST['codigo-estanteria']);
        $numFilas = (int)$_POST['num-filas'];
        $capacidad = (int)$_POST['capacidad-fila'];
        $tipoClasificacion = $_POST['tipo-clasificacion'];

        // Validaciones específicas
        if (strlen($descripcionEstanteria) > 255) {
            $errores[] = "El nombre no puede exceder 255 caracteres";
        }

        /*if (!preg_match('/^[A-Z0-9\-]{1,20}$/i', $codigo)) {
            $errores[] = "El código solo permite letras, números y guiones (máx. 20 caracteres)";
        }*/

        if ($numFilas <= 0 || $numFilas > 10) {
            $errores[] = "El número de filas debe ser entre 1 y 10";
        }

        if ($capacidad <= 10 || $capacidad > 200) {
            $errores[] = "La capacidad por fila debe ser entre 10 y 200";
        }

        // Validar clasificación
        if ($tipoClasificacion === "misma") {
            if (empty($_POST['clasificacion-general'])) {
                $errores[] = "Debe especificar una clasificación general";
            } else {
                $clasificacionGeneral = trim($_POST['clasificacion-general']);
            }
        } else {
            if (empty($_POST['fila-dewey']) || count($_POST['fila-dewey']) !== $numFilas) {
                $errores[] = "Debe especificar una clasificación Dewey para cada fila";
            } else {
                $filasDewey = $_POST['fila-dewey'];
                foreach ($filasDewey as $dewey) {
                    if (empty(trim($dewey))) {
                        $errores[] = "Las clasificaciones Dewey no pueden estar vacías";
                        break;
                    }
                }
            }
        }
    }

    // =============================================
    // MANEJO DE ERRORES O CONTINUAR PROCESO
    // =============================================
    if (!empty($errores)) {
        // Redirección con error (usando tu ruta)
        header("Location: ../../../index.php?vista=registrar_estanteria&alerta=incompleto");
        exit();
    }

    // =============================================
    // SI TODO ESTÁ BIEN: PROCESAR INSERCIONES
    // =============================================
    $capacidadTotal = $capacidad * $numFilas;


$conexion->begin_transaction();
try {
    //insertar estanteria
    $sql = "INSERT INTO `estanterias`( `codigo`, `descripcion`, `cantidad_filas`, `capacidad_total`) VALUES (?, ? , ? , ?)";
    $stmt1 = $conexion->prepare($sql);
    $stmt1->bind_param('ssii', $codigo, $descripcionEstanteria, $numFilas, $capacidadTotal);
    $stmt1->execute();
    $idEstanteria = $conexion->insert_id;

    

    //insertar filas de la estanteria
    $sql = "INSERT INTO `fila`(`EstanteriaID`, `NumeroFila`, `Capacidad`, `DeweyID`, `LibrosActuales`) VALUES (?, ?, ?, ?, ?)";
    $stmt2 = $conexion->prepare($sql);
    $librosActuales = 0;
    for ($i = 1; $i <= $numFilas; $i++){
        $clasificacion = $tipoClasificacion === "misma" ? $clasificacionGeneral : $filasDewey[$i - 1]; // -1 porque los arrays POST empiezan desde 0
        $stmt2->bind_param("iiiii", $idEstanteria, $i, $capacidad, $clasificacion, $librosActuales);


        $stmt2->execute();

    }

    if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
        $stmt1->close();
        $stmt2->close();

        $conexion->commit();

        header("Location: ../../../index.php?vista=estanterias&alerta=exito");

    } 

    


} catch (Exception $e) {
    $stmt1->close();
    $stmt2->close();
    $conexion->rollback();
    //echo "Error: ". $e->getMessage() .".";

    header("Location: ../../../index.php?vista=registrar_estanteria&alerta=error");

}



}

