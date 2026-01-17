<?php

class check
{

    public function generarCheckboxesPermisos($conn) {
        echo '<div class="checks-grid">'; // Contenedor de la cuadrícula
    
        $consulta = "SELECT * FROM permisos WHERE id NOT IN (15, 13, 10, 9)";
        $resultado = mysqli_query($conn, $consulta);
    
        while ($row = mysqli_fetch_assoc($resultado)) {
            $idPermiso = $row['id'];
            $nombrePermiso = $row['permiso'];
    
            echo <<<HTML
            <div class="form-check form-switch">
            <label class="form-check-label" for="$nombrePermiso">$nombrePermiso</label>
                <input class="form-check-input" type="checkbox" role="switch" id="$nombrePermiso" name="permisos[]" value="$idPermiso" onchange="this.checked = !this.checked;">
                
            </div>
    HTML;
        }
    
        echo '</div>'; // Cierre del contenedor de la cuadrícula
    }
    

    public function generarCheckboxesPermisosUsuario($conn, $cedula)
    {

        // Consulta para obtener todos los permisos
        $consulta = "SELECT * FROM permisos";
        $resultado_todos_permisos = mysqli_query($conn, $consulta);

        // Consulta para obtener los permisos del usuario
        $consulta_usuario = "SELECT p.id FROM permisos p
                        INNER JOIN usuario_permisos up ON p.id = up.id_permiso
                        INNER JOIN usuarios u ON up.id_usuario = u.cedula
                        WHERE u.cedula = '$cedula'";
        $resultado_usuario = mysqli_query($conn, $consulta_usuario);


        // Array para almacenar los permisos del usuario
        $permisos_usuario = array();
        while ($row = mysqli_fetch_assoc($resultado_usuario)) {
            $permisos_usuario[] = $row['id'];
        }

        // Generar los checkboxes

        $checkboxes = '';
        while ($row = mysqli_fetch_assoc($resultado_todos_permisos)) {
            $checked = in_array($row['id'], $permisos_usuario) ? 'checked' : '';
            $checkboxes .= '<div class="form-check form-switch">';
            $checkboxes .= '  <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault_' . $row['id'] . '" name="permisos[]" value="' . $row['id'] . '" ' . $checked . '>';
            $checkboxes .= '  <label class="form-check-label" for="flexSwitchCheckDefault_' . $row['id'] . '">' . $row['permiso'] . '</label>';
            $checkboxes .= '</div>';
        }
        // Imprimir el input de texto con la cédula (oculto) y los checkboxes
        $input = '<input type="hidden" name="cedula" value="' . $cedula . '">';
        $input .= $checkboxes;

        echo $input;

        // Imprimir los checkboxes en formato HTML
        // echo $checkboxes;

        mysqli_close($conn);
    }

    public function actualizarPermisosUsuario($conn, $cedula, $nuevosPermisos)
    {
        try {
            // Eliminar todos los permisos existentes para el usuario
            $sqlEliminarPermisos = "DELETE FROM usuario_permisos WHERE id_usuario = ?";
            $stmtEliminar = mysqli_prepare($conn, $sqlEliminarPermisos);
            mysqli_stmt_bind_param($stmtEliminar, "i", $cedula);
            mysqli_stmt_execute($stmtEliminar);

            // Insertar los nuevos permisos
            $sqlInsertarPermiso = "INSERT INTO usuario_permisos (id_usuario, id_permiso) VALUES (?, ?)";
            $stmtInsertar = mysqli_prepare($conn, $sqlInsertarPermiso);

            foreach ($nuevosPermisos as $permiso) {
                mysqli_stmt_bind_param($stmtInsertar, "ii", $cedula, $permiso);
                mysqli_stmt_execute($stmtInsertar);
            }

            // Cerrar las sentencias preparadas
            mysqli_stmt_close($stmtEliminar);
            mysqli_stmt_close($stmtInsertar);

            return true; // Operación exitosa

        } catch (Exception $e) {
            echo "Error al actualizar permisos: " . $e->getMessage();
            return false;
        }
    }

    //funcion para obtener permisos por defectos dependiendo del cargo

    public function cBoxPermisoPorDefecto($conexion, $cargo)
    {
        $query = match ($cargo) {
            "Admin" => "",
            "Bibliotecario" => "",
            default => "",
        };
    }
}
