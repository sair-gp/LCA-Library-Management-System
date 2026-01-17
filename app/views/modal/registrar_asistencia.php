<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="registrarAsistencia" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Asistencia</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/asistencias/c_asistencias.php" method="POST">

                    <div class="col-12">
                        <label for="id_cedulaVisitante">Visitante</label><br>
                        <select class="form-select" name="cedulaVisitante" id="id_cedulaVisitante"></select>

                        <label for="id_origen">Origen</label>
                        <input type="text" name="origen" id="id_origen" class="form-control">
                        <label for="id_descripcion">Descripcion</label>
                        <input type="text" name="descripcion" id="id_descripcion" class="form-control">
                        <!--label for="cota">Fecha</label>
                        <input type="date" name="fecha" id="id_autorNombre" class="form-control"-->
                        <!--input type="hidden" name="responsable" id="responsable" class="form-control"-->

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {


        $('#id_cedulaVisitante').select2({
            dropdownParent: $('#registrarAsistencia'), // Para asegurarte de que funcione dentro del modal
            placeholder: 'Seleccionar visitante/lector',
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return "Por favor, ingrese al menos 2 caracteres";
                },
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            ajax: {
                url: 'public/js/ajax/cargarDatosSelect2.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        cedulaVisitanteAsistencia: params.term, // Nombre de la cota que el usuario escribe

                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.id, // El ID único de la cota
                                text: item.text // El texto que se mostrará
                            };
                        })
                    };
                },
                cache: true,
                timeout: 5000, // 5 segundos de tiempo máximo para la solicitud
                error: function(jqXHR, textStatus) {
                    if (textStatus === 'timeout') {
                        alert('La búsqueda ha tardado demasiado. Intenta nuevamente.');
                    }
                }
            },






            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                return `<span>${data.text}</span>`;
            },
            templateSelection: function(data) {
                return data.text || 'Seleccionar Cota';
            }
        });

        // Evento cuando se selecciona una opción
        /* $('#cota').on('select2:select', function() {
           checkerCota = true; // Cambiar checkerCota a true
           checkChecker();

           console.log('Cota seleccionada:', checkerCota);
         });

         // Evento cuando se deselecciona una opción
         $('#cota').on('select2:unselect', function() {
           checkerCota = false; // Cambiar checkerCota a false
           checkChecker();

           console.log('Cota deseleccionada:', checkerCota);
         });

         // Para comprobar el estado inicial al cargar
         $('#cota').on('change', function() {
           checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
           checkChecker();

           console.log('Cambio detectado:', checkerCota);
         });*/

        // Establecer el ancho del contenedor de Select2 al 100%
        $('.select2-container').css('width', '100%');
    });
</script>