<?php
class visitantes
{

    public function agregarVisitante($cedula, $nombre, $telefono, $direccion, $sexo, $conn, $fechaIngreso, $fotoNombre = null, $correo)
    {
        // Preparando la sentencia SQL con el nuevo campo para la foto
        $stmt = $conn->prepare("INSERT INTO `visitantes` (`cedula`, `nombre`, `telefono`, `direccion`, `sexo`, `fecha_registro`, `foto`, `correo`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
        // Enlazando los parámetros (incluyendo el nombre de la foto)
        $stmt->bind_param("ssssisss", $cedula, $nombre, $telefono, $direccion, $sexo, $fechaIngreso, $fotoNombre, $correo);
    
        // Ejecutando la sentencia
        $stmt->execute();
    
        // Verificando si la inserción fue exitosa
        if ($stmt->affected_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    //habilitacion puede ser 1 o 0
    public function habilitacionDeVisitante(mysqli $conexion, string $cedula, $habilitacion): bool
{
    $stmt = $conexion->prepare("UPDATE visitantes SET activo = ? WHERE cedula = ?");
    if (!$stmt) {
        error_log("Error al preparar la consulta: " . $conexion->error);
        return false;
    }
    
    $stmt->bind_param("is", $habilitacion, $cedula);
    
    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta: " . $stmt->error);
        return false;
    }
    
    return $stmt->affected_rows > 0;
}

}

?>