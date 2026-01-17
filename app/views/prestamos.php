<?php

include "app/controller/prestamos/c_setear_prestamos.php";

include_once "app/controller/c_paginacion.php";
include_once "modal/prestar.php";
include_once "modal/renovarPrestamo.php";
include_once "modal/devolver_prestamo.php";
?>

<div class="vista-tabla">
    <!-- Contenedor de Cabecera -->
    <div class="tabla-header">
        <div class="header-titulo">
            <h2>Gestión de Préstamos</h2>
            <p>Administra los préstamos de manera rápida y efectiva</p>
        </div>

        <div class="header-herramientas">
            <div class="busqueda">
            <input type="text" id="inputBuscarPrestamos" class="input-busqueda" placeholder="Buscar por título, lector, cota o estado...">
            <i class="bi bi-search"></i>
            </div>

                <!-- Nuevo buscador de préstamos -->
    <!--div class="busqueda" style="margin-left: 10px;">
        <input type="text" id="inputPrestamos" class="input-busqueda" placeholder="Buscar préstamos por cédula...">
        <i class="bi bi-book"></i>
    </div-->

            <button class="btn-registrar" data-bs-toggle="modal" data-bs-target="#registrarPrestamo">
                + Registrar Préstamo
            </button>
        </div>
    </div>

    <!-- Tabla Responsive -->
    <div class="tabla-contenedor">
        <table class="tabla-general table-sortable">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>Título</th>
                    <th>Cota</th>
                    <th>Lector</th>
                    <th>Fecha de Préstamo</th>
                    <th>Fecha de Devolución</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <th>Ticket</th>
                </tr>
            </thead>
            <tbody id="tablaPrestamos">
                <?php if (isset($resultado)): ?>
                    <?php while ($row = mysqli_fetch_assoc($resultado)):
                // Inicializar fechas
                $fechaActual = new DateTime('today'); // 'today' asegura que la hora es 00:00
                $fechaInicio = new DateTime($row["fecha_inicio"]);
                $fechaFin = new DateTime($row["fecha_fin"]);

                // Calcular días entre fechas
                $fechaInicio->setTime(0, 0, 0);
                $fechaFin->setTime(0, 0, 0);
                $diferencia = $fechaInicio->diff($fechaFin)->days + 1;

                // Obtener valores de sesión
                $unidad = $_SESSION["unidades"] ?? "dias"; // Valor por defecto "dias"
                $periodo = $_SESSION["periodo_renovaciones"] ?? 14; // Valor por defecto 14 días

                // Calcular la fecha máxima de renovación
                $fechaMaxRenovacion = clone $fechaFin;
                switch ($unidad) {
                case "semanas":
                    $fechaMaxRenovacion->modify("+{$periodo} weeks");
                    break;
                case "meses":
                    $fechaMaxRenovacion->modify("+{$periodo} months");
                    break;
                default: // "dias"          
                    $fechaMaxRenovacion->modify("+{$periodo} days");
                    break;
}

                // Determinar si el botón debe estar habilitado
                $puedeRenovar = $diferencia < intval($_SESSION["periodo_renovaciones"]) && 
                ($row["estado"] == "prestado" || $row["estado"] == "extendido") && 
                $fechaFin >= $fechaActual && 
                $fechaMaxRenovacion >= $fechaActual;

                /*echo "<br>
                puede renovar $puedeRenovar <br>
                diferencia $diferencia <br>
                periodo $periodo
                ";*/

            
// =============================================
// CONFIGURACIÓN DEL RESALTADO DE FILA
// =============================================
$resaltarFila = (isset($_GET['id']) && $row['id'] == $_GET['id']) 
    ? "style='background-color: #ffcc00;'" 
    : "";

// =============================================
// CONFIGURACIÓN DEL TÍTULO (TOOLTIP)
// =============================================
$title = ($row['estado'] === 'vencido' && $row['tieneMulta']) 
    ? "title='Este préstamo ha sido multado'" 
    : "title='El ejemplar ya ha sido devuelto.'";

// =============================================
// CONFIGURACIÓN DEL BOTÓN DEVOLVER
// =============================================
// Estado por defecto del botón Devolver
if ($row["estado"] != 'devuelto' && 
    $row["estado"] != 'Devolución tardía' && 
    $row["estado"] != 'vencido') {
    
    $botonDevolver = "<button type='button' class='modalDevolver btn-general' 
                      data-bs-toggle='modal' data-bs-target='#devolver'>
                      Devolver</button>";
} else {
    $botonDevolver = "<button type='button' class='btn-general' 
                      style='background-color: lightgrey;' $title disabled>
                      Devolver</button>";
}

// Caso especial: préstamo vencido sin multa
/*if ($row["estado"] === 'vencido' && !$row['tieneMulta']) {
    $botonDevolver = "<button type='button' class='modalDevolverConMulta btn-general' 
                      style='background-color: red;' data-bs-toggle='modal' 
                      data-bs-target='#modalRegistrarMulta'>
                      Multar</button>";
}

// Caso especial: préstamo vencido con multa
/*if ($row["estado"] === 'vencido' && $row['tieneMulta']) {
    $botonDevolver = "<button type='button' class='modalDevolverConMulta btn-general' 
                      style='background-color: lightgrey;' 
                      data-bs-toggle='modal' 
                      data-bs-target='#modalRegistrarMulta' 
                      title='Este préstamo ha sido multado' disabled>
                      Multar</button>";
}*/

// Caso especial: préstamo vencido sin multa (devolver con devolucion tardia)
if ($row["estado"] === 'vencido' && $row['tieneMulta']) {
    $botonDevolver = "<button type='button' class='modalDevolverConMulta modalDevolver btn-general' 
                      style='background-color: red;' data-bs-toggle='modal' 
                      data-bs-target='#devolver'>
                      Devolver</button>";
}

// =============================================
// CONFIGURACIÓN DEL BOTÓN RENOVAR
// =============================================
if ($row["estado"] != "devuelto" && $row["estado"] != 'vencido') {
    // Configuración para préstamos activos
    $disabled = $puedeRenovar ? "" : "disabled";
    $style = $puedeRenovar ? "" : "style='background-color: lightgrey;'";
    $title = $puedeRenovar ? "" : "title='No se puede renovar ya que se ha alcanzado el límite máximo de días para el préstamo.'";

    $botonRenovar = sprintf(
        "<button type='button' class='btn-general' 
         data-bs-toggle='modal' data-bs-target='#renovar' 
         onclick='setRenovarId(event, %d, %d)' %s %s %s>
         Renovar</button>",
        $row['id'], 
        $row['cedula'], 
        $disabled, 
        $style, 
        $title
    );
} else {
    // Configuración para préstamos finalizados o vencidos
    $botonRenovar = "<button type='button' class='btn-general' 
                     style='background-color: lightgrey;' disabled 
                     title='No se puede renovar un préstamo {$row["estado"]}.'>
                     Renovar</button>";
}

// definir el botón de impresión de ticket
$botonTicket = "<a href='app/reportes/ticket.php?idPrestamo=". $row["id"] ."' target='_blank' class='btn btn-success'><i class='bi bi-receipt'></i></a>";
?>

<tr <?= $resaltarFila; ?>>
    <td style="display: none;"><?= $row['id']; ?></td>
    <td><a style="text-decoration: none; color: inherit;" href="index.php?vista=fichaLibro&isbn=<?= $row["isbn"]; ?>"><?= $row['titulo']; ?></a></td>
    <td><?= $row['cota']; ?></td>
    <td><?= $row['nombre']; ?></td>
    <td><?= $row['fecha_inicio']; ?></td>
    <td><?= $row['fecha_fin']; ?></td>
    <td><?= $row['estado']; ?></td>
    <td style="display: none;"><?= $row['cedula']; ?></td>
    <td style="display: none;"><?= $row['diasDeRetraso']; ?></td>
    <td>
        <?= $botonDevolver; ?>
        <?= $puedeRenovar ? $botonRenovar : "" ; ?>
    </td>
    <td> <?= $botonTicket; ?></td>
</tr>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No se encontraron registros.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <footer>
        <div class="paginacion-contenedor">

            <div class="col-sm-5">
                <?php
                echo "<p> Total de registros: ($total_registros)</p>";
                // echo $inicio . " " . $registros_por_pagina . " " . $total_paginas;
                ?>
            </div>


            <?php echo $paginacion->generarPaginacion($pagina_actual, $total_paginas); ?>
        </div>
    </footer>

</div>


<!-- Modal Renovar -->
<!--div class="modal fade" id="renovar" tabindex="-1" aria-labelledby="renovarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renovarLabel">Renovar Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRenovar" method="POST" action="procesar_renovacion.php">
                    <div class="mb-3">

                        <label for="idPrestamoRenovar" class="form-label">ID del Préstamo</label>
                        <input type="text" class="form-control" id="idPrestamoRenovar" name="idPrestamo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nuevaFecha" class="form-label">Nueva Fecha de Devolución</label>
                        <input type="date" class="form-control" id="nuevaFecha" name="nuevaFecha" required>
                        <input type="hidden" id="spanAdvertenciaFecha" value="valor">

                    </div>
                    <button type="submit" class="btn btn-primary" id="btnRenovarPrestamo" disabled>Renovar</button>
                </form>
            </div>
        </div>
    </div>
</div-->


<script defer>
    $(document).on('click', '.modalDevolver', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        $('#idPrestamo').val(datos[0]);
        $('#estadoPrestamo').val(datos[6]);


    });


    $(document).on('click', '.modalDevolverConMulta', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        $('#prestamo_id').val(datos[0]);
       $('#lectorCedula').val(datos[7]);
       $('#diferencia_dias').val(datos[8]);
       $('#lectorNombre').val(datos[3]);
        

    });


</script>
<script defer src="public/js/renovarPrestamo.js">
    /*const fechaActual = new Date();
    console.log(fechaActual);
    const spanAdvertenciaFecha = document.getElementById("spanAdvertenciaFecha");
    const nuevaFechaInput = document.getElementById("nuevaFecha");

    function setRenovarId(id = "") {
        if (id) {
            document.getElementById("idPrestamoRenovar").value = id;
        }

        const button = event.target;
        const row = button.closest("tr");

        const fechaInicioStr = row.querySelector("td:nth-child(5)").innerText.trim();
        const fechaFinStr = row.querySelector("td:nth-child(6)").innerText.trim();

        // Convierte las fechas de las celdas al formato esperado
        const inicio = new Date(fechaInicioStr); // Asume formato válido para `new Date`
        let fin = new Date(fechaFinStr);
        fin.setDate(fin.getDate() + 1);

        console.log("fecha fin: " + fin);

        // Calcula la diferencia en días
        const diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));

        if (fin < fechaActual) {
            console.log("Prestamo vencido");
            console.log("diferencia: " + diferencia);
            //  return;
        }

        if (diferencia < 14) {
            const diasExtensionPrestamo = 14 - diferencia;
            crearSpan(
                spanAdvertenciaFecha,
                spanAdvertenciaFecha.nextElementSibling,
                `El usuario puede solicitar una renovación de un tiempo máximo de ${diasExtensionPrestamo} día(s).`,
                "green"
            );
            console.log("diferencia dentro del if: " + diferencia);
            console.log("fecha fin dentro if diferencia: " + fin);
            // Calcula la nueva fecha
            const nuevaFecha = new Date(fechaActual);
            nuevaFecha.setDate(fin.getDate() + diasExtensionPrestamo);

            // Formatea la fecha en `yyyy-MM-dd`
            const anio = nuevaFecha.getFullYear();
            const mes = String(nuevaFecha.getMonth() + 1).padStart(2, "0");
            const dia = String(nuevaFecha.getDate()).padStart(2, "0");
            const fechaFormateada = `${anio}-${mes}-${dia}`;

            // Actualiza el campo de entrada
            nuevaFechaInput.value = fechaFormateada;
        } else {
            console.log("El usuario no es apto para solicitar renovación.");
        }
    }

    function verificarFechasRenovacion(diferencia) {
        // Verificar que las fechas sean <= que la variable diferencia
        const devolucion = new Date(nuevaFecha.value);

        const actual = new Date();

        const diferenciaAD = Math.ceil((devolucion - actual) / (1000 * 60 * 60 * 24));
        //AD = actual (fecha actual) y devolución (fecha devolución)

        if (diferencia <= diferenciaAD) {
            console.log("Ta fino");
        } else {
            console.log("No ta fino");
        }
    }

    /*  nuevaFecha.addEventListener("change", () => {
              let diferencia = ""
              diferencia = setRenovarId()

              if (diferencia) {
                  verificarFechasRenovacion(diferencia);
              }



          })*/
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusqueda = document.getElementById('inputBuscarPrestamos');
    const tablaPrestamos = document.getElementById('tablaPrestamos');
    const filasOriginales = Array.from(tablaPrestamos.querySelectorAll('tr')).filter(tr => 
        !(tr.cells.length === 1 && tr.cells[0].colSpan > 1)
    );
    
    // Función para filtrar los préstamos
    function filtrarPrestamos() {
        const termino = inputBusqueda.value.trim().toLowerCase();
        let resultadosVisibles = 0;
        
        filasOriginales.forEach(fila => {
            const titulo = fila.cells[1].textContent.toLowerCase(); // Columna Título
            const lector = fila.cells[3].textContent.toLowerCase(); // Columna Lector
            const cota = fila.cells[2].textContent.toLowerCase(); // Columna Cota
            const estado = fila.cells[6].textContent.toLowerCase(); // Columna Estado
            
            const coincide = titulo.includes(termino) || 
                           lector.includes(termino) || 
                           cota.includes(termino) ||
                           estado.includes(termino);
            
            fila.style.display = coincide ? '' : 'none';
            if (coincide) resultadosVisibles++;
        });
        
        // Manejar mensaje de no resultados
        const mensajeNoResultados = tablaPrestamos.querySelector('tr td[colspan]');
        
        if (resultadosVisibles === 0 && termino !== '') {
            if (!mensajeNoResultados) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 9; // Ajusta según número de columnas
                td.textContent = 'No se encontraron préstamos que coincidan con: "' + termino + '"';
                tr.appendChild(td);
                tablaPrestamos.appendChild(tr);
            }
        } else {
            // Eliminar mensaje de no resultados si existe (excepto el original)
            const mensajes = tablaPrestamos.querySelectorAll('tr td[colspan]');
            mensajes.forEach(msg => {
                if (msg.textContent.includes('No se encontraron préstamos')) {
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
    inputBusqueda.addEventListener('input', filtrarPrestamos);
    
    // Limpiar búsqueda si el input es de tipo search
    inputBusqueda.addEventListener('search', function() {
        if (this.value === '') {
            filtrarPrestamos();
        }
    });
});
</script>

<!-- Estilo opcional para mejorar la apariencia -->
<style>
.input-busqueda {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    width: 320px;
    transition: all 0.3s;
    margin-right: 10px;
}

.input-busqueda:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
    width: 380px;
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

#tablaPrestamos tr td[colspan] {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

/* Estilo para resaltar filas */
tr[style*="background-color: #ffcc00"] {
    animation: highlight 2s ease-in-out;
}

@keyframes highlight {
    from { background-color: #ffcc00; }
    to { background-color: inherit; }
}
</style>