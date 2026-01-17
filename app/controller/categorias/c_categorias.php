<?php
include "../../config/database.php";
include '../../model/categorias.php';

$conexion = conexion();
$categoria = new categorias();


if (($_POST['accion'] === 'agregar')) {
    if (isset($_POST['nombreCategoria'])) {

        $nombreCategoria = $_POST['nombreCategoria'];
        

        $resultado = $categoria->agregarCategoria($conexion, $nombreCategoria);
        if ($resultado) {
            echo "
            <script src='../../../node_modules/sweetalert2/dist/sweetalert2.all.min.js'></script>
            <script language='JavaScript'>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Categoria registrada correctamente',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    timer: 1500
                  }).then(() => {
                    location.assign('../../../index.php?vista=categorias&pagina=1');
                  });
        });
            </script>";
        } else {
            echo "
            <script src='../../../node_modules/sweetalert2/dist/sweetalert2.all.min.js'></script>
            
            <script language='JavaScript'>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Algo salio mal. Intenta de nuevo',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    timer: 1500
                  }).then(() => {
                    location.assign('../../../index.php?vista=categorias&pagina=1');
                  });
        });
            </script>";
        }

        //echo $isbn . $cota;
    }




} 


