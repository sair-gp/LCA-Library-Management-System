<?php
// URL de la API
$url = "https://ve.dolarapi.com/v1/dolares";

// Valor predeterminado en caso de fallo
$valorDolar = $dolarBCV; // Puedes usar un valor predeterminado o recuperarlo de la base de datos

try {
    // Crear un contexto para la solicitud HTTP
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3\r\n"
        ]
    ];

    $context = stream_context_create($options);

    // Hacer la solicitud HTTP
    $response = @file_get_contents($url, false, $context); // Usamos @ para suprimir advertencias

    if ($response === FALSE) {
        throw new Exception("No se pudo conectar a la API. Usando valor predeterminado.");
    }

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        throw new Exception("Error al decodificar la respuesta JSON. Usando valor predeterminado.");
    }

    // Obtener el valor del dólar
    foreach ($data as $dolar) {
        $valorDolar = $dolar['promedio']; // Usamos el valor promedio
        break; // Solo tomamos el primer valor
    }

} catch (Exception $e) {
    // Manejar el error (por ejemplo, registrar en un log o mostrar un mensaje)
    error_log($e->getMessage()); // Registrar el error en el log del servidor
    echo $e->getMessage(); // Mostrar un mensaje al usuario (opcional)
}

// Redondear a 2 decimales
$valorFormateado = round($valorDolar, 2);

// Preparar la consulta SQL
$sql = 'UPDATE regla_de_circulacion SET dolarBCV = ? WHERE 1';

// Preparar la sentencia
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

// Vincular el parámetro (usar "d" para double/float)
$stmt->bind_param("d", $valorFormateado);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Valor actualizado correctamente: " . $valorFormateado;
} else {
    echo "Error al ejecutar la consulta: " . $stmt->error;
}

// Cerrar la sentencia
$stmt->close();
?>