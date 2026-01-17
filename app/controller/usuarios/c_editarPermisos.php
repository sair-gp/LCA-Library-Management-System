<?php 

include '../../config/database.php';
include '../../model/checks.php';
$conn = conexion();
$check = new check;

if (isset($_POST['cedula']) && isset($_POST['permisos']) && is_array($_POST["permisos"])) {
    //echo "se enviaron los datos";

    $cedula = $_POST['cedula'];
    $permisos = $_POST['permisos']; 
    if ($check->actualizarPermisosUsuario($conn, $cedula, $permisos)) {
    	//echo "se actualizaron los permisos";
        header ('Location: ../../../index.php?vista=usuarios&alerta=exito');
    }

    //echo $cedula . ": " . $permisos;
}
















?>