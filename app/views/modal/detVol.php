<style>
    .nav-tabs {
        --bs-nav-link-padding-x: 0.5rem;
        --bs-nav-link-padding-y: 0.5rem;
    }
</style>

<!-- Modal con pestañas -->
<div class="modal fade" id="detallesModalVolumenes" tabindex="-1" aria-labelledby="detallesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detallesModalLabel">Detalles del Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <input type="hidden" id="isbnDetalles">

                <!-- Pestañas -->
                <ul class="nav nav-tabs" id="detailsVolTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-vol-tab" data-bs-toggle="tab" data-bs-target="#detailsVol" type="button" role="tab" aria-controls="detailsVol" aria-selected="true">Detalles</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-vol-tab" data-bs-toggle="tab" data-bs-target="#add-vol" type="button" role="tab" aria-controls="add-vol" aria-selected="false">Agregar Ejemplar</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="desincorporate-vol-tab" data-bs-toggle="tab" data-bs-target="#desincorporate-vol" type="button" role="tab" aria-controls="desincorporate-vol" aria-selected="false">Desincorporar Ejemplar</button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content mt-3" id="detailsVolTabContent">
                    <div class="tab-pane fade show active" id="detailsVol" role="tabpanel" aria-labelledby="details-vol-tab">
                        <div class="mb-3">
                            <label for="vol-select" class="form-label">Seleccionar Volumen:</label>
                            <select class="form-select" id="vol-select" style="width: 100%">

                            </select>
                        </div>
                        <div id="vol-details"></div>
                    </div>

                    <!-- Pestaña Agregar Ejemplar -->
                    <div class="tab-pane fade" id="add-vol" role="tabpanel" aria-labelledby="add-vol-tab">
                        <form id="add-vol-form">
                            <div class="mb-3">
                                <label for="vol-cota" class="form-label">Cota</label>
                                <input type="text" class="form-control" id="vol-cota" placeholder="Ingrese la cota">
                            </div>
                            <div class="mb-3">
                                <label for="vol-location" class="form-label">Ubicación:</label>
                                <select class="form-select" id="vol-location">
                                    <option value="0">Seleccione la ubicación</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="vol-source" class="form-label">Suministrado mediante:</label>
                                <select class="form-select" id="vol-source">
                                    <option value="2">Donación</option>
                                    <option value="1">Red de bibliotecas</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Agregar Ejemplar</button>
                        </form>
                    </div>

                    <!-- Pestaña Desincorporar Ejemplar -->
                    <div class="tab-pane fade" id="desincorporate-vol" role="tabpanel" aria-labelledby="desincorporate-vol-tab">
                        <form id="desincorporate-vol-form">
                            <div class="mb-3">
                                <label for="vol-cota-des" class="form-label">Cota</label>
                                <select class="form-select" id="vol-cota-des"></select>
                            </div>
                            <div class="mb-3">
                                <label for="vol-detail" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="vol-detail" placeholder="Descripción del estado">
                            </div>
                            <button type="submit" class="btn btn-danger">Desincorporar Ejemplar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    /*$(document).ready(function() {
        $('#vol-select').select2({
            ajax: {
                url: 'public/js/ajax/cargarDatosSelect2.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        buscarVolumen: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            },
            placeholder: 'Seleccione un volumen',
            minimumInputLength: 1
        });
    });*/
</script>