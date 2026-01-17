<?php

include '../../../app/config/database.php';
$conn = conexion();

// Obtener el correo enviado por AJAX
if (isset($_GET['userEmail'])) {
    $correo = $_GET['userEmail'];
    //echo "el correo es $correo";

    // Escapar la entrada para prevenir inyección SQL
    $correo = $conn->real_escape_string($correo);

    // Consultar la base de datos para verificar si el correo existe
    $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existe = ($row['total'] > 0);
    } else {
        $existe = false; // Si hay un error en la consulta, asumimos que no existe
    }

    // Enviar la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode(array('existe' => $existe));
}

$conn->close();
?>