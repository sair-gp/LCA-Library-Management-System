<?php
include '../../model/usuarios.php';
include '../../model/historial.php';
include '../../model/sesion_usuario.php';
include '../../config/database.php';

$conn = conexion();

//$userSession = new UserSession();
session_start();
$user = new User();
$historial = new Historial($conn);
//$user->setUser($_POST["cedula"]);

//var_dump($usuario_responsable);

if (isset($_POST['cedula']) && isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['fecha_nac']) && isset($_POST['direccion']) && isset($_POST['dominio']) && isset($_POST['telefono']) && isset($_POST['correo']) && isset($_POST['sexo']) && isset($_POST['rol']) && isset($_POST['clave']) && isset($_POST['permisos']) && is_array($_POST["permisos"])) {

    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $fecha_nac = $_POST['fecha_nac'];
    $direccion = $_POST['direccion'];
    $dominio = $_POST['dominio'];
    $tlf = $_POST['telefono'];
    $telefono = $dominio . $tlf;
    $correo = $_POST['correo'];
    $sexo = $_POST['sexo'];
    $rol = $_POST['rol'];
    $clave = $_POST['clave'];
    $permisos = $_POST['permisos'];

    if ($user->addUser($cedula, $nombre, $apellido, $fecha_nac, $direccion, $telefono, $correo, $sexo, $rol, $clave, $permisos, $conn)) {

        $usuarioResponsable = $_SESSION['cedula'] ?? 'Usuario desconocido';

        $detalles = "Cédula: {$cedula}, Nombre: {$nombre} {$apellido}";

        $fechaActual = date('Y-m-d');


        $accion = 3; // Código de acción para registro de usuario
        $historial->registrar_accion($usuarioResponsable, $detalles, $fechaActual, $accion);

        header("Location: ../../../index.php?vista=usuarios&toast=success&mensaje=Usuario registrado correctamente.");
    }
}
