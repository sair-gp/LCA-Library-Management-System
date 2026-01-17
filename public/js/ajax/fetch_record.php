<?php
include '../../../app/config/database.php';
$conn = conexion();


// Verifica si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $result = $conn->query($query);

    if ($result) {
        $columns = $result->fetch_fields();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        if (count($rows) > 0) {
            echo "<table class='tabla-modal table-sortable'>";
            echo "<thead><tr>";
            foreach ($columns as $column) {
                echo "<th>{$column->name}</th>";
            }
            echo "</tr></thead><tbody>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>{$cell}</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No se encontraron resultados.</p>";
        }
    } else {
        echo "<p>Error en la consulta: " . $conn->error . "</p>";
    }
}

$conn->close();
