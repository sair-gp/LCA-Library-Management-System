<?php

class prestamos
{
    public function registrarPrestamo($cota, $fecha_fin, $lector, $conn)
    {

        // Iniciar la transacción
        $conn->begin_transaction();

        try {
            // Preparar y ejecutar el UPDATE en la tabla 'ejemplares'
            $stmt1 = $conn->prepare("UPDATE `ejemplares` 
        SET `estado` = 2 
        WHERE id = ?;");
            $stmt1->bind_param("i", $cota);  // 's' para string, asumiendo que `cota` es una cadena
            $stmt1->execute();
            //echo $cota;
            // Preparar y ejecutar el INSERT en la tabla 'prestamos'
            $stmt2 = $conn->prepare("INSERT INTO `prestamos`(`cota`, `fecha_inicio`, `fecha_fin`, `estado`, `lector`) VALUES (?, NOW(), ?, 1, ?)");
            $stmt2->bind_param("sss", $cota, $fecha_fin, $lector);
            $stmt2->execute();

            // Confirmar la transacción
            $conn->commit();

            // Verificar si la inserción fue exitosa
            if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
                return true;
            } else {
                echo "falso";
                //return false;
            }
        } catch (Exception $e) {
            // Si ocurre algún error, revertir la transacción
            $conn->rollback();
            echo "Error en la transacción: " . $e->getMessage();
            return false;
        }
    }

    public function devolverPrestamo($connect, $id, $estado, $hoy)
{
    // Validaciones básicas
    if (!is_numeric($id) || $id <= 0) {
        return ['success' => false, 'message' => 'ID de préstamo no válido'];
    }

    if (!in_array($estado, ['vencido', 'devuelto'])) {
        return ['success' => false, 'message' => 'Estado de devolución no válido'];
    }

    $connect->begin_transaction();

    try {
        // 1. Obtener información completa del préstamo con bloqueo
        $query = "SELECT p.cota, p.estado as estado_prestamo, e.estado as estado_ejemplar 
                 FROM prestamos p
                 JOIN ejemplares e ON p.cota = e.id
                 WHERE p.id = ? FOR UPDATE";
        
        $stmt = $connect->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar consulta: " . $connect->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $datos = $result->fetch_assoc();
        $stmt->close();

        if (!$datos) {
            return ['success' => false, 'message' => 'Préstamo no encontrado'];
        }

        // 2. Validar estado del préstamo (1, 3 o 4 son válidos)
        if (!in_array($datos['estado_prestamo'], [1, 3, 4])) {
            return ['success' => false, 'message' => 'El préstamo no está en estado válido para devolución'];
        }

        // 3. Determinar el nuevo estado del préstamo
        $nuevoEstadoPrestamo = ($estado === "vencido") ? 5 : 2; // 5: vencido, 2: devuelto

        // 4. Actualizar el préstamo (sin importar estado del ejemplar)
        $queryUpdatePrestamo = "UPDATE prestamos 
                               SET fecha_devolucion = ?, estado = ? 
                               WHERE id = ?";
        
        $stmtUpdatePrestamo = $connect->prepare($queryUpdatePrestamo);
        if (!$stmtUpdatePrestamo) {
            throw new Exception("Error al preparar actualización de préstamo: " . $connect->error);
        }
        
        $stmtUpdatePrestamo->bind_param("sii", $hoy, $nuevoEstadoPrestamo, $id);
        $stmtUpdatePrestamo->execute();
        
        if ($stmtUpdatePrestamo->affected_rows === 0) {
            throw new Exception("No se pudo actualizar el préstamo");
        }
        $stmtUpdatePrestamo->close();

        // 5. Actualizar el ejemplar a disponible (1) si no lo estaba
        if ($datos['estado_ejemplar'] != 1) {
            $queryUpdateEjemplar = "UPDATE ejemplares SET estado = 1 WHERE id = ?";
            $stmtUpdateEjemplar = $connect->prepare($queryUpdateEjemplar);
            
            if (!$stmtUpdateEjemplar) {
                throw new Exception("Error al preparar actualización de ejemplar: " . $connect->error);
            }
            
            $stmtUpdateEjemplar->bind_param("i", $datos['cota']);
            $stmtUpdateEjemplar->execute();
            
            if ($stmtUpdateEjemplar->affected_rows === 0) {
                throw new Exception("No se pudo actualizar el ejemplar");
            }
            $stmtUpdateEjemplar->close();
        }

        $connect->commit();

        return [
            'success' => true,
            'message' => 'Préstamo marcado como devuelto correctamente'
        ];

    } catch (Exception $e) {
        $connect->rollback();
        return [
            'success' => false,
            'message' => 'Error al procesar la devolución: ' . $e->getMessage()
        ];
    }
}

    public function renovarPrestamo($conexion, $prestamo_id, $nueva_fecha_fin)
    {

        try {
            $estado = 4; //Esto equivale a extendido/renovado
            $stmt = $conexion->prepare("UPDATE prestamos SET estado = ?, fecha_fin = ? WHERE id = ?;");
            $stmt->bind_param("isi", $estado, $nueva_fecha_fin, $prestamo_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function actualizarEstadoPrestamo($datos, $conexion){
        try {
        
            $stmt = $conexion->prepare("UPDATE `prestamos` SET `estado` = ? WHERE id = ?");
            $stmt->bind_param("ii", $datos['estado'], $datos['id_prestamo']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            } else {
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }







    }
}
