<?php
include '../../config/database.php';
include '../../model/checks.php';

$conexion = conexion();
$check = new check;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'];


    $check->generarCheckboxesPermisosUsuario($conexion, $cedula);
    
} else {
    echo "Solicitud inválida.";
}