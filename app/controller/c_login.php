<?php
include '../model/usuarios.php';
include '../model/sesion_usuario.php';
include '../config/database.php';
$conn = conexion();

$userSession = new UserSession();
session_start();

if (isset($_POST['cedula']) && isset($_POST['contrasena'])) {

    $CedulaForm = $_POST['cedula'];
    $PassForm = $_POST['contrasena'];

    $user = new User();
    if ($user->userExists($CedulaForm, $PassForm, $conn)) {
        echo "Existe el usuario";
        $userSession->setCurrentUser($CedulaForm);
        $user->setUser($CedulaForm, $conn);
        header("Location: ../../index.php?vista=home");
    } else {
        header("Location: ../../index.php?vista=login&alerta=!login");
    }
}
