<!DOCTYPE html>
<html lang="en">
<?php
include 'app/includes/head.php';
?>

<body style="margin: 0px; padding: 0px;">
    <?php
    
    date_default_timezone_set('America/Caracas');

    //require "./app/config/database.php";
    // $db = new Database();
    session_start();
    //Inicializa la variable get vista, para mostrar las vistas de forma dinamica

    if (!isset($_GET['vista']) || $_GET['vista'] == "") {
        $_GET['vista'] = "login";
    }

    //Verifica que la vista actual exista en la carpeta de vistas, y luego verifica que
    //la vista no sea ni login ni el error 404

    if (is_file("app/views/" . $_GET['vista'] . ".php") && $_GET['vista'] != "login" && $_GET['vista'] != "404") {

        // Cerrar sesion
        if ((!isset($_SESSION['cedula']) || $_SESSION['cedula'] == "")) {
            session_destroy();
            header("Location: index.php?vista=login");
            exit();
        }

        // Incluye la vista actual
        include "app/includes/sidebar.php";

        // agregar un include section para todas las vistas

    ?>


    <?php


    } else {
        if ($_GET['vista'] == "login") {
            include "./app/views/login.php";
            //include "../login/login_perro.php";
        } else {
            include "./app/views/errors/404.php";
        }
        //session_destroy();
    }
    ?>


    <div id="cajaToast" class="ds-toast-container">

    </div>

</body>

<!--script defer src="public/bootstrap/js/bootstrap.min.js"></script-->
<!--script defer src="public/bootstrap/js/bootstrap.bundle.min.js"></script-->
<!--script src="public/js/jquery-3.7.1.min.js"></script-->



<script>
    let btn = document.querySelector('#btn');
    let sidebar = document.querySelector('.sidebar');
    btn.onclick = function() {
        sidebar.classList.toggle('active');
    }
</script>
<script src="public/js/busquedaUniversal.js"></script>
<script src="public/js/alertas.js"></script>
<script src="public/js/alertasToast.js"></script>
<!--script src="public/js/abrirModalTablaReutilizable.js"></script-->

<script src="public/js/elementosDinamicos.js"></script>
<script src="public/js/tableSort.js"></script>
<script src="public/js/validaciones.js"></script>
<script src='node_modules/sweetalert2/dist/sweetalert2.min.js'></script>
<script defer src="public/js/alertas.js"></script>
<script src="node_modules/select2/dist/js/select2.min.js"></script>


</html>