<?php $registrosVariable = 10;
$consultaCount = "SELECT COUNT(*) AS total FROM visitantes;";
$consultaPaginacion = "SELECT * FROM visitantes LIMIT ?, ?";
include "app/controller/c_paginacion.php";
//include "modal/agregar_visitante.php";
include "modal/asistencias.php";
include "modal/registrar_asistencia.php";


?>


<div class="vista-tabla">
    <div class="tabla-header">
        <div class="header-titulo">
            <h2>Visitantes</h2>
            <p></p>
        </div>

        <div class="header-herramientas">
    <!-- Buscador principal existente -->
    <div class="busqueda">
        <input type="text" id="inputTermino" class="input-busqueda" placeholder="Buscar visitantes...">
        <i class="bi bi-search"></i>
    </div>
    
    <!-- Botones existentes -->
    <button class="btn-registrar" onclick="window.location.href='index.php?vista=registrarVisitante'">
        + Registrar Visitante
    </button>
    <button class="btn-registrar" data-bs-toggle="modal" data-bs-target="#registrarAsistencia">
        + Registrar Asistencia
    </button>
</div>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla-general table-sortable">
            <thead>
                <tr>
                    <th style="text-align: center;">INFORMACIÓN</th>
                    <th style="text-align: center;" id="idTh" class="th-sort-asc">CEDULA</th>
                    <th style="text-align: center;">TELEFONO</th>
                    <th style="text-align: center;">DIRECCION</th>
                    <th style="text-align: center;">SEXO</th>
                    <th style="text-align: center;">ASISTENCIAS</th>
                    <th style="text-align: center;">PERFIL</th>
                </tr>
            </thead>

            <tbody id="tablaVisitantes" class="tablaTbody">
                <?php if (isset($resultado)): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td style="display: none;"><?= $row["cedula"] ?></td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="<?php echo !empty($row['foto']) ? $row['foto'] : 'public/img/default-profile.png'; ?>" 
                                         alt="Foto de perfil" 
                                         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                                    <div style="text-align: left;">
                                        <div style="font-weight: bold;"><?php echo $row['nombre']; ?></div>
                                        <div style="font-size: 0.8em; color: #666;"><?php echo $row['correo'] ?? 'Sin correo'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;"><?php echo $row['cedula']; ?></td>
                            <td style="text-align: center;"><?php echo $row['telefono']; ?></td>
                            <td style="text-align: center;"><?php echo $row['direccion']; ?></td>
                            <td style="text-align: center;">
                                <div class="spanSex<?= $row['sexo']?>">
                                    <span>
                                    <?php echo $row['sexo'] == 1 ? "Masculino" : "Femenino"; ?>
                                    </span>
                                </div>
                            </td>

                            <td style="text-align: center;">
                                <button type="button" class="modalBtnAsistencias btn btn-success" data-bs-toggle='modal' data-bs-target='#asistenciasModal'>
                                <i class="bi bi-list-nested"></i>
                                    
                                </button>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-success" onclick="window.location.href='index.php?vista=fichaLector&cedulaVisitante=<?php echo $row['cedula'] ?>'">
                                <i class="bi bi-person-lines-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No se encontraron registros.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="paginacion-contenedor">
        <div class="col-sm-5">
            <?php
            echo "<p> Total de registros: ($total_registros)</p>";
            ?>
        </div>
        <?php echo $paginacion->generarPaginacion($pagina_actual, $total_paginas); ?>
    </div>
</div>



<script>
    $(document).on('click', '.modalBtnAsistencias', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        var cedula = datos[0];
        console.log(cedula)

        fetch("public/js/ajax/generarAsistencias.php", {
        method: "POST",
        headers: {"Content-type" : "application/json"},
        body: JSON.stringify({cedulaAsistencia : cedula})
        }).then(response => {
            if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
        }
         return response.json(); // Procesar la respuesta JSON
        }).then(data => {
            if (data.error){
                console.log(data.error);
               // return;
            }
            
                document.getElementById("asistenciasTBody").innerHTML = data.tbody;
                document.getElementById("asistenciasModalLabelSpan").textContent = data.visitante;
            
        }).catch(error => {
        console.error("Error al obtener datos de asistencia:", error); // Manejo de errores
        // Puedes mostrar un mensaje de error al usuario aquí
        })


    });

    $(document).on('click', '.btn-registrar', function() {
        


    });



</script>

<!--script src="public/js/validarCedulaUnica.js">
   /* var cedulaVisitante = document.getElementById("validarCedulaBD"); 

    cedulaVisitante.addEventListener("input", () => {
        fetch("public/js/ajax/validarCamposUnicos.php", {
        method: "POST",
        headers: {"Content-type" : "application/json"},
        body: JSON.stringify({validarCedulaVisitante: cedulaVisitante.value})
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json(); // Procesar la respuesta JSON
    }).then(data => {
        console.log(data.message);
        crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, data.message, data.class, "");
        if (cedulaVisitante.value == ""){
        crearSpan(cedulaVisitante, cedulaVisitante.nextElementSibling, "", "", true);
    }
    }).catch(error => {
        console.error("Error al validar la cédula:", error); // Manejo de errores
        // Puedes mostrar un mensaje de error al usuario aquí
    });;

    });

    
*/



</script-->




<script>
    //buscador
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('inputTermino');
    const tablaVisitantes = document.getElementById('tablaVisitantes');
    
    // Cache de todas las filas de datos (excluyendo mensajes de no resultados)
    const filasDatos = Array.from(tablaVisitantes.querySelectorAll('tr')).filter(tr => 
        tr.cells.length > 1 && !tr.querySelector('td[colspan]')
    );
    
    // Fila de mensaje "No se encontraron registros" (si existe)
    const filaMensajeOriginal = tablaVisitantes.querySelector('tr td[colspan]')?.parentElement;
    
    inputBusqueda.addEventListener('input', function() {
        const termino = this.value.trim().toLowerCase();
        let resultadosVisibles = 0;
        
        // Mostrar/ocultar filas según coincidencia
        filasDatos.forEach(fila => {
            const textoFila = [
                fila.cells[0].textContent, // Nombre y foto
                fila.cells[1].textContent, // Cédula
                fila.cells[2].textContent  // Teléfono
            ].join(' ').toLowerCase();
            
            const coincide = textoFila.includes(termino);
            fila.style.display = coincide ? '' : 'none';
            if (coincide) resultadosVisibles++;
        });
        
        // Manejar mensaje de no resultados
        const filaMensajeActual = tablaVisitantes.querySelector('tr td[colspan]')?.parentElement;
        
        if (resultadosVisibles === 0 && termino !== '') {
            if (!filaMensajeActual) {
                const nuevaFila = document.createElement('tr');
                nuevaFila.innerHTML = `<td colspan="7">No hay coincidencias para "${termino}"</td>`;
                tablaVisitantes.appendChild(nuevaFila);
            } else if (filaMensajeActual !== filaMensajeOriginal) {
                filaMensajeActual.querySelector('td').textContent = `No hay coincidencias para "${termino}"`;
            }
        } else {
            // Eliminar mensaje de no resultados si no es el original
            if (filaMensajeActual && filaMensajeActual !== filaMensajeOriginal) {
                filaMensajeActual.remove();
            }
        }
    });
    
    // Limpiar búsqueda si el input es de tipo search y se hace clic en la X
    inputBusqueda.addEventListener('search', function() {
        if (this.value === '') {
            this.dispatchEvent(new Event('input'));
        }
    });
});
</script>

<style>
    .spanSex1 {
        background-color: skyblue;
        color: black;
        border-radius: 50px;
        width: 100%;
    }
    .spanSex2 {
        background-color: pink;
        color: black;
        border-radius: 50px;
        width: 100%;
    }
</style>