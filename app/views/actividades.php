<?php

require_once "app/controller/reg_actividades/c_setear_actividad.php";



//echo "<br><br><br>$hoy";
?>
<!--link rel="stylesheet" href="public/css/actividades.css"-->


<div class="vista-tabla">
    <div class="tabla-header">
        <div class="header-titulo">
            <h2>Actividades</h2>
            <p>Administra las actividades de manera rápida y efectiva</p>
        </div>

        <div class="header-herramientas">
    <div class="busqueda">
        <input type="text" id="inputBusqueda" class="input-busqueda" placeholder="Buscar actividad...">
        <i class="bi bi-search"></i>
    </div>
    <button class="btn-registrar" data-bs-toggle="modal" data-bs-target="#registrarActividad">
        + Registrar Actividad
    </button>
</div>


    </div>

    <div class="tabla-contenedor">
        <table class="tabla-general table-sortable">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>DESCRIPCIÓN</th>
                    <th>ENCARGADO</th>
                    <th>INICIO</th>
                    <th>FINALIZA</th>
                    <th>DURACIÓN (por día)</th>
                    <th>DURACIÓN (total)</th>
                    <th>OBSERVACIÓN</th>
                    <th>ACCIONES</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody id="tablaActividades">
                <?php if (isset($resultado)): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultado)): 
                        //calcular diferencia de dias

                       // $fechaInicio = DateTime::createFromFormat("Y-m-d", $row["fecha_inicio"]);
                        //$fechaFin = DateTime::createFromFormat("Y-m-d", $row["fecha_fin"]);

                        $duracionTotal = $row["duracion"];

                        if ($row["fecha_inicio"] != $row["fecha_fin"]) {
                       
                        $fechaInicio = strtotime($row["fecha_inicio"]);
                        $fechaFin = strtotime($row["fecha_fin"]);

                        //$diferencia = $fechaFin - $fechaInicio;

                        $diferenciaDias = ($fechaFin - $fechaInicio) / (60 * 60 * 24); // Diferencia total en días

                        // Calculamos cuántos domingos hay en el rango
                        $domingos = 0;
                        $primerDomingo = strtotime('next Sunday', $fechaInicio - 1);
                        while ($primerDomingo <= $fechaFin) {
                            $domingos++;
                            $primerDomingo = strtotime('+1 week', $primerDomingo);
                        }

                        $dias = $diferenciaDias - $domingos;

                        //$dias = round($diferencia / (60 * 60 * 24));

                        $dias += 1; // Para que tenga en cuenta el dia actual y no solo la diferencia. Es decir, si la diferencia entre 8 y 12 es 4, este solo tiene en cuenta 9, 10, 11 y 12, pero noel 8, que es cuando empieza la actividad

                        $duracionTotal = $actividad->multiplicarTiempo($row["duracion"], $dias);

                        }

                        
                        


                        ?>
                        <tr>
                            <td style="display: none;"><?php echo $row['id']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td><?php echo $row['encargado']; ?></td>
                            <td><?php echo $row['fecha_inicio']; ?></td>
                            <td><?php echo $row['fecha_fin']; ?></td>
                            <td><?php echo $row['duracion'] ?? "3 h 0m." ?></td>
                            <td><?php echo $duracionTotal ?? "Error" ?></td>
                            <td><?php echo $row['observacion'] ?? "No se ha indicado observación alguna." ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm modalbtnAccion" data-bs-toggle="modal" data-bs-target="#modalAccion" data-id="<?php echo $row['id']; ?>">
                                    Acciones
                                </button>
                            </td>
                            <td><?php echo $row['estado']; ?></td>
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










<!-- Modal con pestañas -->
<div class="modal fade" id="modalAccion" tabindex="-1" aria-labelledby="modalAccionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAccionLabel">Acciones de la Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="suspender-tab" data-bs-toggle="tab" data-bs-target="#suspender" type="button" role="tab" aria-controls="suspender" aria-selected="true">
                            Suspender
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reprogramar-tab" data-bs-toggle="tab" data-bs-target="#reprogramar" type="button" role="tab" aria-controls="reprogramar" aria-selected="false">
                            Reprogramar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="observacion-tab" data-bs-toggle="tab" data-bs-target="#observacion" type="button" role="tab" aria-controls="observacion" aria-selected="false">
                            Observación
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="suspender" role="tabpanel" aria-labelledby="suspender-tab">
                        <form action="app/controller/reg_actividades/c_suspender_actividad.php" method="POST">
                            <div class="mb-3">
                                <label for="motivoSuspension" class="form-label">Motivo de Suspensión</label>
                                <textarea class="form-control" name="motivo_suspension" id="motivoSuspension" rows="3"></textarea>
                                <input type="hidden" name="idSuspender" class="idActividad" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-danger" id="motivoSuspensionBTN" disabled>Suspender</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="reprogramar" role="tabpanel" aria-labelledby="reprogramar-tab">
                        <form action="app/controller/reg_actividades/c_reprogramarActividad.php" method="POST">
                            <input type="hidden" name="idActividad" class="idActividad" class="form-control">
                            <div class="mb-3">
                                <label for="nuevaFechaInicio" class="form-label">Inicio</label>
                                <input type="date" id="fechaInicioActividad" name="nuevaFechaInicioActividad" class="form-control nuevaFechaInicioActividad" value="<?php echo $hoy; ?>" onchange="validarFechas({ inputFechaInicio: this, inputFechaFinal: document.getElementById('fechaFinActividad'), boton: document.getElementById('btnReprogramar'), reemplazar: true })">
                            </div>
                            <div class="mb-3">
                                <label for="nuevaFechaFin" class="form-label">Finalización</label>
                                <input type="date" id="fechaFinActividad" name="nuevaFechaFinActividad" class="form-control nuevaFechaFinActividad" value="<?php echo $hoy; ?>" onchange="validarFechas({ inputFechaInicio: document.getElementById('fechaInicioActividad'), inputFechaFinal: this, boton: document.getElementById('btnReprogramar'), reemplazar: true })">
                            </div>
                            <div class="mb-3">
                                <label for="motivoSuspensionR" class="form-label">Motivo de Suspensión</label>
                                <textarea class="form-control" name="motivo_suspension" id="motivoSuspensionR" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btnReprogramar" id="motivoSuspensionRBTN" disabled>Reprogramar</button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="observacion" role="tabpanel" aria-labelledby="observacion-tab" style="padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
                    <form action="app/controller/reg_actividades/c_observacion_actividad.php" method="post">
                    <input type="hidden" name="idActividad" class="idActividad" class="form-control">
                    <label for="observacionText" style="display: block; margin-bottom: 10px; font-weight: bold; color: #495057;">Indique alguna observación respecto a esta actividad</label>
                    <textarea name="observacion" id="observacionText" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 3px; box-sizing: border-box; font-size: 16px; line-height: 1.5;"></textarea>
                    <button type="submit" class="btn btn-primary" id="btn-observacion" disabled>Registrar observación</button>
                    </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="public/js/actividades.js"></script>


<script>
// Variable para almacenar la tabla original
let originalTableContent = null;

document.getElementById('inputBusqueda').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    const tablaContainer = document.querySelector('.tabla-contenedor');
    const originalTable = document.querySelector('.tabla-general');
    const originalTbody = originalTable.querySelector('tbody');
    const paginationContainer = document.querySelector('.paginacion-contenedor');
    
    // Guardar contenido original si no está guardado
    if(!originalTableContent) {
        originalTableContent = originalTbody.innerHTML;
    }
    
    // Si el campo está vacío, restaurar la tabla original inmediatamente
    if(searchTerm === '') {
        originalTbody.innerHTML = originalTableContent;
        originalTable.style.display = '';
        if(paginationContainer) paginationContainer.style.display = '';
        
        // Restaurar contador original
        const counter = document.querySelector('.paginacion-contenedor div p');
        if(counter) {
            counter.textContent = `Total de registros: (${<?php echo $total_registros; ?>})`;
        }
        return;
    }
    
    // Mostrar mensaje de búsqueda rápida sin spinner
    originalTbody.innerHTML = '<tr><td colspan="9" class="text-muted">Buscando...</td></tr>';
    
    // Ocultar paginación
    if(paginationContainer) {
        paginationContainer.style.display = 'none';
    }
    
    // Hacer petición AJAX con retraso para evitar muchas solicitudes
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => {
        fetch(`public/js/ajax/cargar_actividades.php?q=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if(!response.ok) throw new Error('Error en la respuesta');
                return response.text();
            })
            .then(html => {
                // Insertar resultados directamente en el tbody original
                originalTbody.innerHTML = html;
                
                // Actualizar contador de resultados
                const counter = document.querySelector('.paginacion-contenedor div p');
                if(counter) {
                    const resultCount = originalTbody.querySelectorAll('tr').length;
                    if(resultCount > 0 && !originalTbody.querySelector('td[colspan="9"]')) {
                        counter.textContent = `Resultados encontrados: ${resultCount}`;
                    } else {
                        counter.textContent = `No se encontraron resultados para "${searchTerm}"`;
                    }
                }
                
                // Reasignar eventos a los botones de acción
                document.querySelectorAll('.modalbtnAccion').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const tr = this.closest('tr');
                        const datos = Array.from(tr.children).map(td => td.textContent);
                        document.querySelector('.idActividad').value = datos[0];
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                originalTbody.innerHTML = '<tr><td colspan="9">Error al cargar resultados</td></tr>';
            });
    }, 300); // Retraso de 300ms para evitar muchas solicitudes
});
</script>


<style>
/* Estilo para el mensaje de búsqueda */
.text-muted {
    color: #6c757d;
    text-align: center;
    padding: 20px;
    font-style: italic;
}
</style>