<?php
include '../../model/editoriales.php';
include '../../config/database.php';
$conn = conexion();
$editorial = new editorial();

if (isset($_POST['editorialNombre']) && isset($_POST['editorialOrigen'])) {
    $nombre = $_POST['editorialNombre'];
    $origen = $_POST['editorialOrigen'];



    if ($editorial->agregarEditorial($conn, $nombre, $origen)) {
        // $userSession->setCurrentUser($CedulaForm);
        // $user->setUser($CedulaForm);
        echo "
        <script src='../../../node_modules/sweetalert2/dist/sweetalert2.all.min.js'></script>
       
        <script language='JavaScript'>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Editorial registrado correctamente',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                timer: 1500
              }).then(() => {
                location.assign('../../../index.php?vista=editoriales&pagina=1');
              });
    });
        </script>";
        //header("Location: ../../../index.php?vista=autores&pagina=1");
    }
}

?>