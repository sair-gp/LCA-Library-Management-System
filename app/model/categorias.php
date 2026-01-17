<?php
class categorias
{

    public function agregarCategoria($conn, $nombre)
    {
        $stmt = $conn->prepare("INSERT INTO `categorias`(`nombre`) VALUES (?)");
        $stmt->bind_param("s", $nombre);
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



