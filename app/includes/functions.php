<!-- Agrega la biblioteca de SweetAlert -->


<?php

if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        //casos de registros
        case 'editar':
            //echo "la accion es editar";
            $isbn = $_POST['isbn'];
            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $anio = $_POST['anio'];
            $editorial = $_POST['editorial'];
            $edicion = $_POST['edicion'];
            $categoria = $_POST['categoria'];
            //echo "$isbn . $titulo . $autor . $anio . $editorial . $edicion . $categoria";
            editar();
            break;
    }
}

function editar()
{

    extract($_POST);
    require_once("../config/database.php");
    $conexion = conexion();

    $consulta = "UPDATE libros SET titulo = '$titulo', autor = '$autor', anio = '$anio', editorial = '$editorial', edicion = '$edicion', categoria = '$categoria' 
    WHERE isbn = '$isbn' ";

    $resultado = mysqli_query($conexion, $consulta);

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
}
