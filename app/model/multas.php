<?php
include_once("prestamos.php");
class Multas extends prestamos{
    private $conexion;
    private $mostrarMultas = "SELECT mm.motivo, mm.id AS idMotivo, m.id AS idMulta, m.fecha_multa, m.estado, v.nombre, p.fecha_fin, CASE WHEN p.fecha_devolucion IS NOT NULL THEN DATEDIFF(p.fecha_devolucion, p.fecha_fin) ELSE DATEDIFF(CURDATE(), p.fecha_fin) END AS diasDeRetraso, COALESCE((SELECT pa.monto_pagado FROM pagos pa WHERE pa.multa_id = m.id), 0) AS monto_pagado FROM multas m LEFT JOIN motivo_multa mm ON m.motivo = mm.id LEFT JOIN prestamos p ON p.id = m.prestamo_id LEFT JOIN visitantes v ON v.cedula = m.cedula_visitante;";
    private $obtenerDatosHistorialSQL = "SELECT v.nombre, mm.motivo, pa.monto_pagado, CASE WHEN l.es_obra_completa = 1 THEN l.titulo ELSE CASE WHEN REGEXP_REPLACE(vo.nombre, '[0-9]', '') = l.titulo THEN CONCAT(l.titulo, ' ', 'volumen ', vo.numero) ELSE CONCAT(l.titulo, ' \"', vo.nombre, '\". ') END END AS titulo FROM visitantes v LEFT JOIN multas m ON m.cedula_visitante = v.cedula LEFT JOIN pagos pa ON pa.multa_id = m.id LEFT JOIN prestamos p ON p.id = m.prestamo_id LEFT JOIN ejemplares ej ON ej.id = p.cota LEFT JOIN libros l ON l.isbn = ej.isbn_copia LEFT JOIN motivo_multa mm ON mm.id = m.motivo LEFT JOIN volumen vo ON vo.id = ej.isbn_vol ";
    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function registrarMultaAutomaticamente($datos){
        
    }

    public function imprimirMultas($dolar, $montoRetraso, $montoDano, $montoPerdida) {
        // Verifica si la conexión a la base de datos está establecida
        if (!$this->conexion) {
            throw new Exception("Error: No se ha establecido una conexión a la base de datos.");
        }
    
        // Prepara la consulta SQL
        $stmt = $this->conexion->prepare($this->mostrarMultas);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
        }
    
        // Ejecuta la consulta
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    
        // Obtiene el resultado de la consulta
        $resultado = $stmt->get_result();
        if (!$resultado) {
            throw new Exception("Error al obtener el resultado de la consulta: " . $stmt->error);
        }
    
        // Verifica si hay filas en el resultado
        if ($resultado->num_rows === 0) {
            return []; // Retorna un array vacío si no hay multas
        }
    
        // Procesa las filas y las almacena en un array
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            // Verifica que los campos necesarios estén presentes en la fila
            if (!isset($fila["motivo"], $fila["fecha_multa"], $fila["estado"], $fila["nombre"], $fila["fecha_fin"], $fila["diasDeRetraso"])) {
                throw new Exception("Error: Faltan campos en el resultado de la consulta.");
            }
            //multiplicar por retraso
            $mpr = $fila["diasDeRetraso"] > 0 ? $dolar * ($montoRetraso * intval($fila["diasDeRetraso"])) : 0;
            
            // Calcular montoFinal independientemente de monto_pagado
        if ($fila["idMotivo"] === 1) {
            $montoFinal = $mpr;
        } elseif ($fila["idMotivo"] === 2) {
            $montoFinal = $dolar * $montoPerdida + $mpr;
        } elseif ($fila["idMotivo"] === 3) {
            $montoFinal = $dolar * $montoDano + $mpr;
        } else {
            $montoFinal = 0; // Si idMotivo no es 1, 2 o 3
        }

        // Mostrar monto_pagado si es mayor a 0, de lo contrario mostrar montoFinal
        if ($fila["monto_pagado"] > 0) {
            $montoMostrar = $fila["monto_pagado"];
        } else {
            $montoMostrar = $montoFinal;
        }
            
            // Ahora puedes usar $resultado en lugar de la expresión ternaria compleja
            $datos[] = [
                "motivo" => $fila["motivo"],
                "fechaMulta" => $fila["fecha_multa"],
                "estado" => $fila["estado"],
                "nombre" => $fila["nombre"],
                "fechaFin" => $fila["fecha_fin"],
                "diasRetraso" => $fila["diasDeRetraso"],
                "monto" => $montoFinal,
                "montoPagado" => $fila["monto_pagado"],
                "idMulta" => $fila["idMulta"],
            ];
        }
    
        // Cierra el statement y libera los recursos
        $stmt->close();
    
        return $datos;
    }

    /**
 * Método para registrar el pago de una multa y actualizar su estado.
 *
 * @param int $multa_id ID de la multa.
 * @param float $monto_pagado Monto pagado.
 * @param string $tipo_pago Tipo de pago (efectivo, transferencia, etc.).
 * @param float $tasa_del_dia Tasa del día para conversiones.
 * @return array Respuesta en formato JSON.
 */
public function pagarMulta($multa_id, $monto_pagado, $tipo_pago, $tasa_del_dia) {
    // Validar los datos recibidos
    if ($multa_id <= 0 || $monto_pagado <= 0 || empty($tipo_pago) || $tasa_del_dia <= 0) {
        return ['success' => false, 'message' => "Datos inválidos. Id: $multa_id. Tipo de pago: $tipo_pago. Monto pagado: bs.$monto_pagado. Tasa del dia: $tasa_del_dia."];
    }

    // Iniciar una transacción (opcional, pero recomendado para operaciones múltiples)
    $this->conexion->begin_transaction();

    try {
        // 1. Insertar el pago en la tabla `pagos`
        $sqlPago = "INSERT INTO pagos (multa_id, tipo_pago, monto_pagado, tasa_del_dia, fecha_pago) 
                    VALUES (?, ?, ?, ?, NOW())";
        $stmtPago = $this->conexion->prepare($sqlPago);
        $stmtPago->bind_param("isdd", $multa_id, $tipo_pago, $monto_pagado, $tasa_del_dia);
        $stmtPago->execute();

        // Obtener el ID de la inserción
        $pago_id = $this->conexion->insert_id;
        
        $stmtPago->close();

        // 2. Actualizar el estado de la multa en la tabla `multas`
        $sqlMulta = "UPDATE multas SET estado = 'pagada' WHERE id = ?";
        $stmtMulta = $this->conexion->prepare($sqlMulta);
        $stmtMulta->bind_param("i", $multa_id);
        $stmtMulta->execute();
        $stmtMulta->close();

        // Confirmar la transacción
        $this->conexion->commit();

        // Respuesta exitosa
        return ['success' => true, 'message' => 'Pago registrado y multa actualizada correctamente', 'pagoId' => $pago_id ];
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $this->conexion->rollback();
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

    public function obtenerVisitante($id, $filtro){
        $stmt = $this->conexion->prepare("$this->obtenerDatosHistorialSQL  $filtro");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if (!$fila = $resultado->fetch_assoc()){
            $stmt->close();
            return false;
        } 

        $datos = [
            "titulo" => $fila["titulo"],
            "nombre" => $fila["nombre"],
            "motivo" => $fila["motivo"],
            "monto_pagado" => $fila["monto_pagado"] ?? ""
        ];
        $stmt->close();
        return $datos;


        
    }

}
