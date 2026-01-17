<?php $registrosVariable = 10;

if ($_SESSION["rol"] == "Admin") {
    $consultaCount = "SELECT COUNT(*) AS total FROM usuarios";

    $consultaPaginacion = "SELECT u.foto_perfil, u.cedula, u.nombre, u.apellido, u.fecha_nacimiento, u.direccion, u.telefono, u.correo, CASE WHEN u.sexo = 1 THEN 'Masculino' WHEN u.sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion, r.nombre AS rol_nombre, YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5)) AS edad FROM usuarios AS u JOIN rol AS r ON u.rol = r.id_rol WHERE u.estado = 1 LIMIT ?, ?;";
} else {
    $consultaCount = "SELECT COUNT(*) AS total FROM usuarios where estado = 1 and rol != 1;";

    $consultaPaginacion = "SELECT u.foto_perfil, u.cedula, u.nombre, u.apellido, u.fecha_nacimiento, u.direccion, u.telefono, u.correo, CASE WHEN u.sexo = 1 THEN 'Masculino' WHEN u.sexo = 2 THEN 'Femenino' ELSE 'Otro' END AS sexo_descripcion, r.nombre AS rol_nombre, YEAR(CURRENT_DATE) - YEAR(u.fecha_nacimiento) - (RIGHT(CURRENT_DATE,5) < RIGHT(u.fecha_nacimiento,5)) AS edad FROM usuarios AS u JOIN rol AS r ON u.rol = r.id_rol WHERE u.estado = 1 AND r.id_rol != 1 LIMIT ?, ?;";
}



include "app/controller/c_paginacion.php";
include "modal/editarPermisos.php";
//include "modal/registrarUsuario.php";


?>

<style>
    .user-info-container {
        display: flex;
        align-items: center;
    }

    .user-img {
        width: 50px;
        /* Ajusta el tamaño de la imagen según sea necesario */
        height: auto;
        margin-right: 10px;
        /* Espacio entre la imagen y el texto */
    }

    .user-name {
        font-weight: bold;
        /* Texto del nombre en negrita */
    }

    .user-email {
        color: #888;
        /* Color de fuente gris para el correo */
    }
</style>

<div class="vista-tabla">
    <div class="tabla-header">
        <div class="header-titulo">
            <h2>Usuarios del sistema</h2>
            <p></p>
        </div>

        <div class="header-herramientas">
            <div class="busqueda">
                <input type="text" id="inputBuscarUsuarios" class="input-busqueda" placeholder="Buscar por nombre, cédula o correo...">
                <i class="bi bi-search"></i>
            </div>
            <button class="btn-registrar" onclick="window.location.href = 'index.php?vista=registrar_usuario';">
                + Registrar Usuario
            </button>
            <!--button type="button" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i></button-->
        </div>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-general table-sortable">
            <thead>
                <tr>

                    <th class="flecha-arriba" style="text-align: center;">NOMBRES</th>
                    <th style="text-align: center;" id="idTh" class="th-sort-asc">CEDULA</th>
                    <th style="text-align: center;">ROL</th>
                    <th style="text-align: center;">DIRECCIÓN</th>
                    <th style="text-align: center;">TELEFONO</th>
                    <th style="text-align: center;">SEXO</th>
                    <th style="text-align: center;">EDAD</th>
                    <th style="text-align: center;">PERFIL</th>
                    <?= $_SESSION["rol"] == "Admin" ? '<!--th style="text-align: center;">PERMISOS</th-->' : "" ?>


            </thead>



            <tbody id="tablaUsuarios">
                <?php
                // Verificamos si la sesión existe y si contiene los datos
                if (isset($resultado)) {
                    $result = $resultado;
                ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>


                            <td id="nombreTd" class="editable">
                                <div class="user-info-container">

                                    <?php $imagen = $row["foto_perfil"] != NULL ? "data:image/jpeg;base64," . base64_encode($row['foto_perfil']) : "public/img/userDefault.jpg"; ?>

                                    <img src="<?php echo $imagen ?>" alt="me" class="user-img fotoPerfilTablaUser">
                                    <div>
                                        <div class="user-name">
                                            <?php echo $row['nombre'] . " " . $row['apellido']; ?>
                                        </div>
                                        <div class="user-email">
                                            <?php echo $row['correo']; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td style="text-align: center;" id="idUsuario" class="notEditable"><?php echo $row['cedula']; ?>
                            </td>

                            <td style="text-align: center;" id="rolTd" class="notEditable"><?php echo $row['rol_nombre']; ?>
                            </td>

                            <td style="text-align: center;" id="direccionTd" class="notEditable">
                                <?php echo $row['direccion']; ?>
                            </td>

                            <td style="text-align: center;" id="telefonoTd" class="notEditable">
                                <?php echo $row['telefono']; ?>
                            </td>

                            <td style="text-align: center;" id="sexoTd" class="notEditable">
                                <?php echo $row['sexo_descripcion']; ?>
                            </td>

                            <td style="text-align: center;" id="edadTd" class="notEditable"><?php echo $row['edad']; ?>
                            </td>

                            <td><button type="button" class="btn btn" style="background-color: green; color: white;" onclick="window.location.href='index.php?vista=perfilUsuario&cedula=<?= $row['cedula'] ?>'">
                            <i class="bi bi-person-lines-fill"></i>
                            </button></td>

                            <?php if ($_SESSION["rol"] == "Admin" && $row["rol_nombre"] !== 'Admin') { ?>
                                <!--td style="display: flex; justify-content:center;">
                                    <button type="button" class="btn btn-warning modalbtnPermisos" data-bs-toggle="modal"
                                        data-bs-target="#editarPermisos" data-cedula="<?php echo $row['cedula']; ?>"> <i class="bi bi-key"></i></button>
                                </td-->
                            <?php    } else {
                                //$ced = $row["cedula"];
                                echo <<<HTML
                                    <!--td style="display: flex; justify-content:center;">
                                    <button type="button" class="btn btn-warning modalbtnPermisos" data-bs-toggle="modal"
                                        data-bs-target="" data-cedula="" title="Los permisos del administrador no pueden ser editados." disabled> <i class="bi bi-key"></i></button>
                                    </td-->
                                    HTML;
                            }
                            ?>







                        </tr>
                    <?php endwhile; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9">No se encontraron registros.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="paginacion-contenedor">
        <div class="col-sm-5">
            <?php
            echo "<p> Total de registros: ($total_registros)</p>";
            // echo $inicio . " " . $registros_por_pagina . " " . $total_paginas;
            ?>
        </div>

        <?php echo $paginacion->generarPaginacion($pagina_actual, $total_paginas) ?>
    </div>

</div>

<script>
    $(document).on('click', '.modalbtnPermisos', function() {
        var cedula = $(this).data('cedula');
        $('#cedulaUser').val(cedula);

        // Llamar a la función para generar los checkboxes 
        // con el valor de la cédula obtenido 
        $.ajax({
            url: 'app/controller/usuarios/permisos_usuario.php',
            type: 'POST',
            data: {
                cedula: cedula
            },
            success: function(response) {
                $('#formPermisos').html(response);
            }
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('inputBuscarUsuarios');
    const tablaUsuarios = document.getElementById('tablaUsuarios');
    const filasOriginales = Array.from(tablaUsuarios.querySelectorAll('tr')).filter(tr => 
        !(tr.cells.length === 1 && tr.cells[0].colSpan > 1)
    );
    
    // Función para filtrar los usuarios
    function filtrarUsuarios() {
        const termino = inputBusqueda.value.trim().toLowerCase();
        let resultadosVisibles = 0;
        
        filasOriginales.forEach(fila => {
            const nombreCompleto = fila.cells[0].textContent.toLowerCase(); // Columna Nombres
            const cedula = fila.cells[1].textContent.toLowerCase(); // Columna Cédula
            const correo = fila.querySelector('.user-email')?.textContent.toLowerCase() || ''; // Email dentro de la columna Nombres
            
            const coincide = nombreCompleto.includes(termino) || 
                           cedula.includes(termino) || 
                           correo.includes(termino);
            
            fila.style.display = coincide ? '' : 'none';
            if (coincide) resultadosVisibles++;
        });
        
        // Manejar mensaje de no resultados
        const mensajeNoResultados = tablaUsuarios.querySelector('tr td[colspan]');
        
        if (resultadosVisibles === 0 && termino !== '') {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = <?= $_SESSION["rol"] == "Admin" ? '9' : '8' ?>; // Ajusta según columnas visibles
                td.textContent = 'No se encontraron usuarios que coincidan con: "' + termino + '"';
                tr.appendChild(td);
                tablaUsuarios.appendChild(tr);
            }
        } else {
            // Eliminar mensaje de no resultados si existe (excepto el original)
            const mensajes = tablaUsuarios.querySelectorAll('tr td[colspan]');
            mensajes.forEach(msg => {
                if (msg.textContent.includes('No se encontraron usuarios')) {
                    msg.parentElement.remove();
                }
            });
            
            // Mostrar mensaje original si no hay término de búsqueda
            if (termino === '' && mensajeNoResultados && 
                mensajeNoResultados.textContent === 'No se encontraron registros') {
                mensajeNoResultados.parentElement.style.display = '';
            }
        }
    }
    
    // Evento de búsqueda con cada tecla presionada
    inputBusqueda.addEventListener('input', filtrarUsuarios);
    
    // Limpiar búsqueda si el input es de tipo search
    inputBusqueda.addEventListener('search', function() {
        if (this.value === '') {
            filtrarUsuarios();
        }
    });
});
</script>

<style>
.input-busqueda {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    width: 300px;
    transition: all 0.3s;
    margin-right: 10px;
}

.input-busqueda:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
}

.busqueda {
    position: relative;
    display: inline-block;
}

.busqueda i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    pointer-events: none;
}

#tablaUsuarios tr td[colspan] {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

/* Mantiene el estilo de la información de usuario */
.user-info-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.user-name {
    font-weight: bold;
}

.user-email {
    font-size: 0.8em;
    color: #666;
}
</style>