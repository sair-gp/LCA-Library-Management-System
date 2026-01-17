<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include '../../../app/config/database.php';
$conn = conexion();
session_start();

// Obtener datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["image"])) {
    echo json_encode(["success" => false, "error" => "No se recibió una imagen válida"]);
    exit;
}

$userId = $_SESSION['cedula']; // ID del usuario desde la sesión
$base64Image = $data["image"];

// Decodificar imagen base64 a binario
$imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

if ($imageData === false) {
    echo json_encode(["success" => false, "error" => "Error al decodificar la imagen"]);
    exit;
}

// Guardar en la base de datos
$query = "UPDATE usuarios SET foto_perfil = ? WHERE cedula = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $imageData, $userId);

if ($stmt->execute()) {
    // Actualizar la imagen en la sesión
    $_SESSION["fotoPerfil"] = "data:image/jpeg;base64," . base64_encode($imageData);

    echo json_encode([
        "success" => true,
        "filePath" => $_SESSION["fotoPerfil"]
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Error al actualizar la base de datos"]);
}

$stmt->close();
$conn->close();
