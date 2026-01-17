<?php

include '../../../app/config/database.php';
include '../../../app/model/checks.php';
$conexion = conexion();
$checks = new check;

if (isset($_POST["cargo"])) {
    $cargo = $_POST["cargo"];

    /*$permisos = match ($cargo) {
        "Coordinador" => ["home", "libros", "prestamos", "historial"],
        "Bibliotecario" => ["home", "libros", "prestamos", "actividades"],
        default => ""
    };*/

    $permisos = ["home", "libros", "prestamos", "historial"];



    if ($permisos) {
        $html = $checks->generarCheckboxesPermisos($conexion, $permisos);
        $data = json_encode([$html]);
        echo $data;
        exit;
    } else {
        echo json_encode(["error" => 'Error al generar los checkboxes']);
    }
}

if (isset($_GET["cargo"])) {
    $cargo = $_GET["cargo"];

    $permisos = match ($cargo) {
        "Coordinador" => ["home", "libros", "prestamos", "historial"],
        "bibliotecario" => ["home", "libros", "prestamos", "actividades"],
        default => ""
    };



    if ($permisos) {
        $html = $checks->generarCheckboxesPermisos($conexion, $permisos);
        $data = json_encode([$html]);
        echo $data;
        exit;
    } else {
        echo json_encode(["error" => 'Error al generar los checkboxes']);
    }
}
