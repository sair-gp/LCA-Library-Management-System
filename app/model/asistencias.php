<?php
class asistencias
{

    public function registrarAsistencia($cedula, $origen, $descripcion, $conn)
    {

        // Preparando la sentencia SQL (eliminando el marcador de posición para 'id')
        $stmt = $conn->prepare("INSERT INTO `asistencias` (`cedula_visitante`, `origen`, `descripcion`, `fecha`) VALUES (?, ?, ?, NOW())");

        // Enlazando los parámetros (eliminando '$id')
        $stmt->bind_param("sss", $cedula, $origen, $descripcion);

        // Ejecutando la sentencia
        $stmt->execute();

        // Verificando si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

}

?>