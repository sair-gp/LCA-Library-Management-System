<?php

class selects
{

    public function select_dinamico($tabla, $connect, $columna, $c2 = "")
    {

        $stmt = $connect->prepare("SELECT * FROM $tabla ORDER BY $columna DESC");
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) {

            while ($row = $resultado->fetch_assoc()) {

                echo "<option value='" . $row["id"] . "'>" . $row["$columna"] . (isset($row["$c2"]) ? " " . $row["$c2"] : "") . "</option>";

            }

        }

    }
}
