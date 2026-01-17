<?php 

class editorial {


    public function agregarEditorial ($conn, $nombre, $origen){
        $stmt = $conn->prepare("INSERT INTO `editorial`(`nombre`, `origen`) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $origen);
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