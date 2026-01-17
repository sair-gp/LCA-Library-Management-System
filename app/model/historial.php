<?php
//include "app/config/database.php";

class Historial
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function MostrarHistorial()
    {
        $stmt = $this->conexion->prepare("SELECT h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ' ,u.apellido) as nombre FROM historial h JOIN acciones a ON h.accion_id = a.id JOIN usuarios as u ON u.cedula = h.cedula_responsable;");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $historialData = [];
            while ($row = $result->fetch_assoc()) {
                $historialData[] = $row; // Agregamos cada registro al arreglo
            }
            return $historialData;
        } else {
            return null; // O retorna un mensaje de error adecuado
        }
    }

    public function registrar_accion($usuario_responsable, $detalles, $fecha, $accion)
    {

        // Preparando la sentencia SQL con marcadores de posición
        $stmt = $this->conexion->prepare("INSERT INTO `historial`(`cedula_responsable`, `accion_id`, `fecha`, `detalles`) VALUES(?,?,?,?)");

        // Enlazando los parámetros con los marcadores de posición
        $stmt->bind_param("siss", $usuario_responsable, $accion, $fecha,  $detalles);

        // Ejecutando la sentencia
        $stmt->execute();

        // Verificando si la inserción fue exitosa
        return $stmt->affected_rows > 0;
    }

    public function filtrarPorFechas($fechaInicio, $fechaFin)
    {
        $query = "SELECT h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ' ,u.apellido) as nombre FROM historial h JOIN acciones a ON h.accion_id = a.id JOIN usuarios as u ON u.cedula = h.cedula_responsable WHERE h.fecha BETWEEN ? AND ?";

        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param('ss', $fechaInicio, $fechaFin);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        $stmt->close();
        return $datos;
    }

    public function filtrarPorAccion($accionId, $fechaInicio = null, $fechaFin = null)
    {
        $query = "
        SELECT h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ' ,u.apellido) as nombre FROM historial h JOIN acciones a ON h.accion_id = a.id JOIN usuarios as u ON u.cedula = h.cedula_responsable WHERE h.accion_id = ?";

        if ($fechaInicio && $fechaFin) {
            $query .= " AND h.fecha BETWEEN ? AND ?";
        }

        $stmt = $this->conexion->prepare($query);

        if ($fechaInicio && $fechaFin) {
            $stmt->bind_param('iss', $accionId, $fechaInicio, $fechaFin);
        } else {
            $stmt->bind_param('i', $accionId);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        $stmt->close();
        return $datos;
    }

    public function filtrarPorResponsable($cedulaResponsable, $fechaInicio = null, $fechaFin = null)
    {
        $query = "SELECT h.fecha, a.descripcion AS accion, h.detalles, CONCAT(u.nombre, ' ' ,u.apellido) as nombre FROM historial h JOIN acciones a ON h.accion_id = a.id JOIN usuarios as u ON u.cedula = h.cedula_responsable WHERE h.cedula_responsable = ?";

        if ($fechaInicio && $fechaFin) {
            $query .= " AND h.fecha BETWEEN ? AND ?";
        }

        $stmt = $this->conexion->prepare($query);

        if ($fechaInicio && $fechaFin) {
            $stmt->bind_param('iss', $cedulaResponsable, $fechaInicio, $fechaFin);
        } else {
            $stmt->bind_param('i', $cedulaResponsable);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }

        $stmt->close();
        return $datos;
    }
}
