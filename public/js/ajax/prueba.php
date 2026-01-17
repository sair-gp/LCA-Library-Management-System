<?php
include '../../../app/config/database.php';

try {
    $conexion = conexion();

    $data = true;//json_decode(file_get_contents('php://input'), true);

    if ($data) {
        $isbn = $_GET["isbn"];//$conexion->real_escape_string($data['isbn']) ?? null;
        $isbn_sin_guiones = str_replace('-', '', $isbn);

        $sql = "SELECT REPLACE(isbn, '-', '') as isbn_sin_guiones FROM libros WHERE isbn = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $isbnDB_sin_guiones = $fila['isbn_sin_guiones'];

            echo ("ISBN del js sin guiones: $isbn_sin_guiones");
            echo ("ISBN del servidor sin guiones: $isbnDB_sin_guiones");

            if ($isbn_sin_guiones === $isbnDB_sin_guiones) {
                echo json_encode(['result' => true]);
            } else {
                echo json_encode(['result' => false]);
            }
        } else {
            echo json_encode(['result' => false]);

        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();