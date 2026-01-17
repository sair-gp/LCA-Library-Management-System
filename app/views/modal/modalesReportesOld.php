<!-- Modal de Préstamos -->
<div id="prestamosModal" class="modal fade papaDropdown" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📚 Reporte de Préstamos</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="app/reportes/reportesPrestamos.php" method="POST" target="_blank">

           
                <!-- Contenido de las pestañas -->
                <div id="general">
                    <label for="fechaInicioGeneral">Desde:</label>
                    <input type="date" id="fechaInicioGeneral" name="fechaInicioGeneral">
                    <label for="fechaFinGeneral">Hasta:</label>
                    <input type="date" id="fechaFinGeneral" name="fechaFinGeneral">
       
                    <label for="lectorPrestamos">Lector:</label>
                    <select id="lectorPrestamos" style="width: 100%;" name="lectorPrestamos">
                        <option value="todos">Todos</option>
                    </select>
                    <input type="hidden" id="parametroEspecialPorLector" name="parametroEspecial" value="AND v.cedula">
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Devoluciones -->
<div id="devolucionesModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🔄 Reporte de Devoluciones</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="app/reportes/reportesDevoluciones.php" method="POST" target="_blank">
                <label for="fechaInicioDevoluciones">Fecha de Inicio:</label>
                <input type="date" id="fechaInicioDevoluciones" name="fechaInicioDevoluciones">
                <label for="fechaFinDevoluciones">Fecha de Fin:</label>
                <input type="date" id="fechaFinDevoluciones" name="fechaFinDevoluciones">

                <label for="visitanteDevoluciones">Lector:</label>
                <select id="visitanteDevoluciones" style="width: 100%;" name="visitanteDevoluciones">
                    <option value="todos">Todos</option>
                </select>
                
                <label for="estadoDevoluciones">Estado:</label>
                <select id="estadoDevoluciones" name="estadoDevoluciones">
                    <option value="todos">Todos</option>
                    <option value="2">A tiempo</option>
                    <option value="5">Con retraso</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Actividades -->
<div id="actividadesModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📅 Reporte de Actividades</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="app/reportes/reportesActividades.php" method="POST" target="_blank">
                <label for="fechaInicioActividades">Fecha de Inicio:</label>
                <input type="date" id="fechaInicioActividades" name="fechaInicioActividades">
                <label for="fechaFinActividades">Fecha de Fin:</label>
                <input type="date" id="fechaFinActividades" name="fechaFinActividades">
                <label for="estadoActividades">Estado:</label>
                <select id="estadoActividades" name="estadoActividades">
                    <option value="todos">Todos</option>
                    <option value="activas">Activas</option>
                    <option value="reprogramadas">Reprogramadas</option>
                    <option value="suspendidas">Suspendidas</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Libros más solicitados -->
<div id="librosSolicitadosModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🔥 Libros más solicitados</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="app/reportes/reportesLibrosMasSolicitados.php" method="POST" target="_blank">
                <label for="fechaInicioLibros">Fecha de Inicio:</label>
                <input type="date" id="fechaInicioLibros" name="fechaInicioLibros">
                <label for="fechaFinLibros">Fecha de Fin:</label>
                <input type="date" id="fechaFinLibros" name="fechaFinLibros">
                <label for="categoriaLibros">Categoría:</label>
                <select id="categoriaLibros" style="width: 100%;" name="categoriaLibros">
                    <option value="todos">Todos</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Sanciones -->
<div id="sancionesModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>⚠️ Reporte de Sanciones</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="fechaInicioSanciones">Fecha de Inicio:</label>
                <input type="date" id="fechaInicioSanciones" name="fechaInicioSanciones">
                <label for="fechaFinSanciones">Fecha de Fin:</label>
                <input type="date" id="fechaFinSanciones" name="fechaFinSanciones">
                <label for="tipoSancion">Tipo de Sanción:</label>
                <select id="tipoSancion" name="tipoSancion">
                    <option value="todos">Todos</option>
                    <option value="retraso">Retraso</option>
                    <option value="perdida">Pérdida</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="generarReporte('sanciones')">Generar Reporte</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visitas por horario -->
<div id="visitasHorarioModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🕒 Reporte de Visitas por horario</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            <!-- Pestanas -->
            <ul class="nav nav-tabs" id="visitasTabs" role="tab-list">
              <li class="nav-item">
              <button class="nav-link active" id="rango-tab" data-bs-toggle="tab" data-bs-target="#visitasPorRangoDeFecha" type="button" role="tab" aria-controls="visitasPorRangoDeFecha" aria-selected="true">Por Rango de Fecha</button>
                <!--a class="nav-link active" data-toggle="tab" href="#visitasPorRangoDeFecha" role="tab">Por rango de fecha</a-->
              </li>
              <li class="nav-item">
              <button class="nav-link" id="periodo-tab" data-bs-toggle="tab" data-bs-target="#visitasPorPeriodo" type="button" role="tab" aria-controls="visitasPorPeriodo" aria-selected="false">Por Periodo</button>
                <!--a class="nav-link" data-toggle="tab" href="#visitasPorPeriodo" role="tab">Por periodo</a-->
              </li>
            </ul>  

            <div class="tab-content">

              <div class="tab-pane active" id="visitasPorRangoDeFecha" role="tabpanel">

              <form action="app/reportes/reportesVisitas.php" method="POST" target="_blank">
                <label for="fechaInicioVisitas">Fecha de Inicio:</label>
                <input type="date" id="fechaInicioVisitas" name="fechaInicioVisitas">
                <label for="fechaFinVisitas">Fecha de Fin:</label>
                <input type="date" id="fechaFinVisitas" name="fechaFinVisitas">

                <label for="horarioSexoRF">Sexo:</label>
                <select id="horarioSexoRF" name="horarioSexo">
                    <option value="todos">Todos</option>
                    <option value="1">Masculino</option>
                    <option value="2">Femenino</option>
                </select>

                <label for="horarioVisitasRF">Horario:</label>
                <select id="horarioVisitasRF" name="horarioVisitas">
                    <option value="todos">Todos</option>
                    <option value="manana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="noche">Noche</option>
                </select>
          
            <div class="modal-footer">
                <button type="type" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>

              </div>




              <div class="tab-pane" id="visitasPorPeriodo" role="tabpanel">

              <form action="app/reportes/reportesVisitasPorPeriodo.php" method="POST" target="_blank">
                <label for="">Periodo:</label>
                <select name="horarioPeriodo" id="horarioPeriodo">
                  <option value="anio">Año</option>
                  <option value="mes" selected>Mes</option>
                  <option value="semana">Semana</option>
                  <option value="dia">Día</option>
                </select>

                <label for="horarioSexo">Sexo:</label>
                <select id="horarioSexo" name="horarioSexo">
                    <option value="todos">Todos</option>
                    <option value="1">Masculino</option>
                    <option value="2">Femenino</option>
                </select>

                <label for="horarioVisitas">Horario:</label>
                <select id="horarioVisitas" name="horarioVisitas">
                    <option value="todos">Todos</option>
                    <option value="manana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="noche">Noche</option>
                </select>
            <div class="modal-footer">
                <button type="type" class="btn btn-primary">Generar Reporte</button>
            </div>
            </form>


              </div>

            </div>

        </div>
    </div>
</div>

<script>

$(document).ready(function () {
  $("#lectorPrestamos").select2({
    dropdownParent: $(".papaDropdown"),
    placeholder: "Cedula del lector",
    minimumInputLength: 2, // Start searching after the user types 1 character
    language: {
      inputTooShort: function () {
        return "Por favor, ingrese al menos 2 caracteres"; // Custom message for too few characters
      },
      noResults: function () {
        checkerLector = false;
        return "No se encontraron resultados"; // Custom "No results found" message
      },
      searching: function () {
        return "Buscando..."; // Message while searching
      },
    },
    ajax: {
      url: "public/js/ajax/validarInputsRegistroPrestamo.php", // The URL of your PHP script
      dataType: "json",
      delay: 250, // Delay before making the request to prevent too many calls
      data: function (params) {
        return {
          cedulaLector: params.term, // Send the search term as 'cedulaLector'
        };
      },
      processResults: function (data) {
        // Agregar la opción "Todos" a los resultados de la búsqueda
        data.results.unshift({
          id: "todos",
          text: "Todos",
        });
        // Format the returned data for Select2
        return {
          results: data.results.map(function (user) {
            return {
              id: user.id, // User's ID (cedula)
              text: user.text, // User's name
            };
          }),
        };
      },
      cache: true,
    },
    templateResult: function (data) {
      // Personalizar cómo se muestra la opción "Todos"
      if (data.id === "todos") {
        return $('<span style="font-weight: bold; color: #00f260;">' + data.text + '</span>');
      }
      return data.text; // Mostrar el texto normal para otras opciones
    },
    escapeMarkup: function (markup) {
      return markup;
    }, // Prevent Select2 from escaping markup
  });


  // Inicializar select2 para el modal de Devoluciones
  $("#visitanteDevoluciones").select2({
    dropdownParent: $("#devolucionesModal"), // Referencia al modal de Devoluciones
    placeholder: "Cedula del lector",
    minimumInputLength: 2,
    language: {
      inputTooShort: function () {
        return "Por favor, ingrese al menos 2 caracteres";
      },
      noResults: function () {
        return "No se encontraron resultados";
      },
      searching: function () {
        return "Buscando...";
      },
    },
    ajax: {
      url: "public/js/ajax/validarInputsRegistroPrestamo.php",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          cedulaLector: params.term,
        };
      },
      processResults: function (data) {
        data.results.unshift({
          id: "todos",
          text: "Todos",
        });
        return {
          results: data.results,
        };
      },
      cache: true,
    },
    templateResult: function (data) {
      if (data.id === "todos") {
        return $('<span style="font-weight: bold; color: #00f260;">' + data.text + '</span>');
      }
      return data.text;
    },
    escapeMarkup: function (markup) {
      return markup;
    },
  });

  $("#categoriaLibros").select2({
    dropdownParent: $("#librosSolicitadosModal"),
    placeholder: "Nombre de la categoría.",
    minimumInputLength: 2,
    language: {
        inputTooShort: function () {
            return "Por favor, ingrese al menos 2 caracteres";
        },
        noResults: function () {
            return "No se encontraron resultados";
        },
        searching: function () {
            return "Buscando...";
        },
    },
    ajax: {
        url: 'public/js/ajax/cargarDatosSelect2.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                nombreCategoria: params.term
            };
        },
        processResults: function (data) {
            if (data.results && Array.isArray(data.results)) {
                data.results.unshift({
                    id: "todos",
                    text: "Todos",
                });
            }
            return {
                results: data.results || []
            };
        },
        cache: false, // Deshabilita la caché si es necesario
    },
    templateResult: function (data) {
        if (!data) return null;
        if (data.id === "todos") {
            return $('<span style="font-weight: bold; color: #00f260;">' + data.text + '</span>');
        }
        return data.text;
    },
    escapeMarkup: function (markup) {
        return markup;
    },
});

});

</script>