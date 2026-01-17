<?php
include '../../../app/config/database.php';

try {
    $conexion = conexion();

    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        $isbn = $conexion->real_escape_string($data['isbn']) ?? null;

        // Consulta que elimina guiones de ambos lados
        $sql = "SELECT isbn FROM libros WHERE REPLACE(isbn, '-', '') = REPLACE(?, '-', '')";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]); // ISBN no encontrado
        }

        $stmt->close();
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();
