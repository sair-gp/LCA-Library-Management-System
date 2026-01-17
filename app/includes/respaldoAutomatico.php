<?php
// Incluir la clase Database
require_once "app/config/database.php";

// Crear una instancia de la clase Database
$database = new Database();

// Verificar si es necesario realizar un respaldo (cada 7 dias)
if ($database->necesitaRespaldoSemanal()) {
    // Ejecutar el respaldo
    $database->respaldoAutomatico('backups'); // Guardar respaldo en la carpeta "backups"

    // Registrar el respaldo en la base de datos
    $database->registrarRespaldo();

    $database->eliminarRespaldoAntiguos("backup", 7);

    echo "Respaldo generado correctamente.";
} else {
    //echo "No es necesario realizar un respaldo en este momento.";
}
?>