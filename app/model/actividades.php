<?php
class actividades
{

    private $verificarEstadosSQL = "SELECT id, fecha_inicio, fecha_fin, estado FROM registro_actividades";
    private $cambiarEstadosSQL = "UPDATE registro_actividades SET estado = ?  WHERE id = ?";

    public function registrarActividad($descripcion, $encargado, $fecha_ini, $fecha_fin, $estado, $conn, $duracion = null)
    {

        // Preparando la sentencia SQL (eliminando el marcador de posición para 'id')
        $stmt = $conn->prepare("INSERT INTO `registro_actividades` (`descripcion`, `encargado`, `fecha_inicio`, `fecha_fin`, `estado`, `duracion`) VALUES (?, ?, ?, ?, ?, ?)");

        // Enlazando los parámetros (eliminando '$id')
        $stmt->bind_param("ssssis", $descripcion, $encargado, $fecha_ini, $fecha_fin,  $estado, $duracion);

        // Ejecutando la sentencia
        $stmt->execute();

        // Verificando si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function suspenderActividad($connect, $id, $motivo_suspension)
    {
        $stmt = $connect->prepare("UPDATE `registro_actividades` SET `estado` = 3, `motivo_suspension` = ? WHERE id = ?");
        $stmt->bind_param("si", $motivo_suspension, $id);

        $result = $stmt->execute();

        if ($result) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return null; // O retorna un mensaje de error adecuado
        }
    }

    public function reprogramarActividad($conexion, $id, $motivo_suspension, $nuevaFechaInicio, $nuevaFechaFin, $nuevaDuracion = null)
    {
        $stmt = $conexion->prepare("UPDATE `registro_actividades` SET `estado` = 4, `motivo_suspension` = ?, `fecha_inicio` = ?, `fecha_fin` = ? WHERE id = ?");
        $stmt->bind_param("sssi", $motivo_suspension, $nuevaFechaInicio, $nuevaFechaFin, $id);

        $result = $stmt->execute();

        if ($result) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return null; // O retorna un mensaje de error adecuado
        }
    }


    public function multiplicarTiempo($tiempo, $multiplicador) {
        // Extraer horas y minutos
        preg_match('/(\d+)h\s*(\d+)m/', $tiempo, $matches);
        
        if (count($matches) !== 3) {
            return "Formato inválido. Use 'Xh Ym' (ej. 4h 30m)";
        }
        
        $horas = (int)$matches[1];
        $minutos = (int)$matches[2];
        
        // Convertir todo a minutos
        $totalMinutos = ($horas * 60 + $minutos) * $multiplicador;
        
        // Convertir de vuelta a horas y minutos
        $nuevasHoras = floor($totalMinutos / 60);
        $nuevosMinutos = $totalMinutos % 60;
        
        return "{$nuevasHoras}h {$nuevosMinutos}m";
    }

    public function obtenerEstados($conexion) {
        $stmt = $conexion->prepare($this->verificarEstadosSQL);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta");
        }
    
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta");
        }
    
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error al obtener resultados");
        }
    
        $datos = [];
        while ($fila = $result->fetch_assoc()) {
            $datos[] = $fila;
        }
    
        $stmt->close();
        $result->free();
    
        return $datos;
    }

    /*----NOTA: Los números de estado equivalen a
    1 = En curso
    2 = Finalizado
    3 = Cancelado
    4 = Reprogramado
    5 = No iniciado
    */
    public function cambiarEstadoAutomatico($conexion) {
        try {
            $datos = $this->obtenerEstados($conexion);
            $hoy = strtotime(date("Y-m-d 00:00:00")); // Más preciso
    
            if (empty($datos)) {
                return "No hay registros para procesar";
            }
    
            $conexion->begin_transaction(); // Iniciar transacción
            $stmt = $conexion->prepare($this->cambiarEstadosSQL);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
    
            $actualizados = 0;
            foreach ($datos as $act) {
                if (!isset($act['fecha_inicio']) || !isset($act['fecha_fin'])) {
                    error_log("Registro ID {$act['id']} sin fechas válidas");
                    continue;
                }
    
                $fechaInicio = strtotime($act["fecha_inicio"]);
                $fechaFin = strtotime($act["fecha_fin"]);
                $estadoActual = (int)$act["estado"];
                $nuevoEstado = $estadoActual;
    
                // Lógica de cambio de estado
                if (($estadoActual === 1 || $estadoActual === 4) && ($fechaFin < $hoy)) {
                    $nuevoEstado = 2; // Finalizado
                } elseif ($estadoActual === 5 && ($fechaInicio <= $hoy)) {
                    $nuevoEstado = 1; // En curso
                }
    
                if ($nuevoEstado !== $estadoActual) {
                    $stmt->bind_param("ii", $nuevoEstado, $act["id"]);
                    if ($stmt->execute()) {
                        $actualizados++;
                    } else {
                        error_log("Error al actualizar estado para ID: ".$act["id"]);
                    }
                }
            }
    
            $conexion->commit(); // Confirmar cambios
            $stmt->close();
            
            return "Proceso completado. Registros actualizados: $actualizados";
            
        } catch (Exception $e) {
            $conexion->rollback(); // Revertir en caso de error
            error_log($e->getMessage());
            return "Error en el proceso automático de estados";
        }
    }



}
