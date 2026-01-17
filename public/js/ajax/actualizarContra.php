<?php

include '../../../app/config/database.php';
$conn = conexion();

session_start();

// Verificar que se recibió la contraseña actual y las nuevas contraseñas
if (isset($_POST["contrasenaActual"], $_POST["nuevaContrasena"], $_POST["confirmarNuevaContrasena"])) {
    // Recibir las contraseñas del formulario
    $contrasenaActual = $_POST["contrasenaActual"];
    $nuevaContrasena = $_POST["nuevaContrasena"];
    $confirmarNuevaContrasena = $_POST["confirmarNuevaContrasena"];

    // Verificar si las nuevas contraseñas coinciden
    if ($nuevaContrasena !== $confirmarNuevaContrasena) {
        echo json_encode([
            "respuesta" => false,
            "mensaje" => "Las nuevas contraseñas no coinciden."
        ]);
        exit; // Terminar la ejecución aquí
    }

    // Preparar consulta para obtener la contraseña actual de la base de datos
    $sql = "SELECT clave FROM usuarios WHERE cedula = ?";
    $stmt = $conn->prepare($sql); // Preparar la consulta SQL
    $stmt->bind_param("s", $_SESSION['cedula']); // Asignar la cédula del usuario logueado
    $stmt->execute(); // Ejecutar la consulta
    $resultado = $stmt->get_result(); // Obtener el resultado

    if ($resultado->num_rows > 0) {
        // Obtener la clave almacenada
        $fila = $resultado->fetch_assoc();
        $claveAlmacenada = $fila["clave"];

        // Validar si la contraseña actual es correcta
        if (password_verify($contrasenaActual, $claveAlmacenada)) {
            // La contraseña actual es válida, proceder a actualizar la nueva contraseña
            $nuevaClaveHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT); // Hash de la nueva contraseña

            // Preparar la consulta para actualizar la contraseña
            $sqlUpdate = "UPDATE usuarios SET clave = ? WHERE cedula = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate); // Preparar la consulta de actualización
            $stmtUpdate->bind_param("ss", $nuevaClaveHash, $_SESSION['cedula']); // Asignar valores
            if ($stmtUpdate->execute()) {
                // Actualización exitosa
                echo json_encode([
                    "respuesta" => true,
                    "mensaje" => "La contraseña ha sido actualizada correctamente."
                ]);
            } else {
                // Error en la actualización
                echo json_encode([
                    "respuesta" => false,
                    "mensaje" => "Error al actualizar la contraseña. Intente nuevamente."
                ]);
            }
            $stmtUpdate->close(); // Cerrar la consulta de actualización
        } else {
            // La contraseña actual es incorrecta
            echo json_encode([
                "respuesta" => false,
                "mensaje" => "La contraseña actual no es correcta."
            ]);
        }
    } else {
        // No se encontró al usuario
        echo json_encode([
            "respuesta" => false,
            "mensaje" => "Usuario no encontrado."
        ]);
    }
    $stmt->close(); // Cerrar la consulta
} else {
    // Datos faltantes en la solicitud
    echo json_encode([
        "respuesta" => false,
        "mensaje" => "Faltan datos en la solicitud."
    ]);
}

$conn->close();
