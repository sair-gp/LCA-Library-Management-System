<?php
include '../../model/libros.php';
include '../../model/usuarios.php';
include '../../config/database.php';
$libro = new Libros();
$user = new User();
$conexion = conexion();


if (($_POST['accion'] === 'eliminar')) {
    if (isset($_POST['isbnEliminar'])) {

        $isbn = $_POST['isbnEliminar'];
        $titulo = $_POST['tituloEliminar'];
        $responsable = $_POST['responsableEliminar'];




        $resultado = $libro->eliminar_libro($conexion, $isbn);
        if ($resultado) {
            $usuario_responsable = $responsable;
            $fecha_actual = date('Y-m-d');
            $accion = 'se elimino el libro: ' . $titulo;
            $antes = NULL;
            $despues = NULL;
            $user->registrar_accion($usuario_responsable, $accion, $fecha_actual, $antes, $despues, $conexion);
            echo "
            <script src='../../../node_modules/sweetalert2/dist/sweetalert2.all.min.js'></script>
           
            <script language='JavaScript'>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'El registro fue eliminado correctamente',
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
}