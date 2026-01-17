<?php

include '../../../app/config/database.php';
$conn = conexion();

session_start();

// Verificar que se haya enviado un archivo
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $userId = $_SESSION['cedula']; // ID del usuario desde la sesión
    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileSize = $_FILES['photo']['size'];

    /* Validar tamaño del archivo (10 MB máximo)
    $maxSize = 10 * 1024 * 1024; // 10 MB
    if ($fileSize > $maxSize) {
        echo json_encode([
            "success" => false,
            "error" => "La imagen no debe pesar más de 10 MB."
        ]);
        exit;
    }
    */
    // Leer contenido del archivo y convertirlo a BLOB
    $fileData = file_get_contents($fileTmpPath);

    // Actualizar la base de datos con el contenido del BLOB
    $query = "UPDATE usuarios SET foto_perfil = ? WHERE cedula = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $fileData, $userId); // "b" para datos binarios
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        // Actualizar la variable de sesión con la nueva imagen
        $_SESSION["fotoPerfil"] = "data:image/jpeg;base64," . base64_encode($fileData);

        echo json_encode([
            "success" => true,
            "fotoPerfil" => $_SESSION["fotoPerfil"]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Error al actualizar la base de datos: " . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        "success" => false,
        "error" => "No se recibió ningún archivo o hubo un error al subirlo."
    ]);
}




$conn->close();
