<?php
include "../../config/database.php";
include '../../model/ejemplares.php';
include '../../model/usuarios.php';
$user = new User();
$conexion = conexion();
$ejemplar = new Ejemplares();


if (($_POST['accion'] === 'agregar')) {
    if (isset($_POST['isbnEjemplar']) && isset($_POST['cota'])) {

        $isbn = $_POST['isbnEjemplar'];
        $cota = $_POST['cota'];
        $titulo = $_POST['titulo'];
        $responsable = $_POST['responsable'];
        $estado = 1;
        $delete_at = 0;

        $resultado = $ejemplar->agregar_ejemplares($conexion, $isbn, $cota, $estado, $delete_at);
        if ($resultado) {

            $usuario_responsable = $responsable;
            $fecha_actual = date('Y-m-d');
            $accion = 'se agrego el ejemplar: ' . ' ' . $cota . '  ' . 'del libro ' . $titulo;
            $antes = NULL;
            $despues = NULL;
            $user->registrar_accion($usuario_responsable, $accion, $fecha_actual, $antes, $despues, $conexion);
            echo "
            <script src='../../../node_modules/sweetalert2/dist/sweetalert2.all.min.js'></script>
            <script language='JavaScript'>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'El registro fue actualizado correctamente',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    timer: 1500
                  }).then(() => {
                    location.assign('../../../index.php?vista=pagineichon&pagina=1');
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
                    location.assign('../../../index.php?vista=pagineichon&pagina=1');
                  });
        });
            </script>";
        }

        //echo $isbn . $cota;
    }




} /*elseif (($_POST['accion'] === 'eliminar')) {
if (isset($_POST['isbnEjemplar']) && isset($_POST['cota'])) {

    $isbn = $_POST['isbnEjemplar'];
    $resultado = $ejemplar->eliminar_ejemplares($conexion, $isbn, $cota, $estado, $delete_at);
    if ($resultado) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script language='JavaScript'>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'El registro fue actualizado correctamente',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                timer: 1500
              }).then(() => {
                location.assign('../../index.php?vista=pagineichon&pagina=1');
              });
    });
        </script>";
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
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
                location.assign('../../index.php?vista=pagineichon&pagina=1');
              });
    });
        </script>";
    }

    //echo $isbn . $cota;
}

}*/


