<?php

class Ejemplares
{

   public function mostrar_ejemplares($connect, $isbn)
    {
        $stmt = $connect->prepare("SELECT id, cota, isbn_copia, titulo FROM ejemplares WHERE isbn_copia = '?';");
        $stmt->bind_param("s", $isbn);

        $result = $stmt->execute();

        if ($result->num_rows > 0) {
            $EjemplaresData = [];
            while ($row = $result->fetch_assoc()) {
                $EjemplaresData[] = $row; // Agregamos cada registro al arreglo
            }
            return $EjemplaresData;
        } else {
            return null; // O retorna un mensaje de error adecuado
        }

    }

    public function agregar_ejemplares($connect, $isbn, $cota, $estado, $delete_at)
    {
        $stmt = $connect->prepare("INSERT INTO `ejemplares`(`isbn_copia`, `cota`, `estado`, `delete_at`) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $isbn, $cota, $estado, $delete_at);


        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return null; // O retorna un mensaje de error adecuado
        }

    }

    /*function eliminar_ejemplares($connect, $isbn)
    {
        $stmt = $connect->prepare("UPDATE `ejemplares` SET `delete_at`= 1 WHERE isbn_copia = ?");
        $stmt->bind_param("s", $isbn);

        $result = $stmt->execute();

        if ($result) {
            return true;
        } else {
            return null; // O retorna un mensaje de error adecuado
        }

    }*/
}
