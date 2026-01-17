<div class="modal fade custom-modal-ejemplar" id="nuevoEjemplar" tabindex="-1" aria-labelledby="modalEjemplarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h1 class="modal-title fs-5 text-dark" id="modalEjemplarLabel">Registrar Nuevo Ejemplar</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-white">
                <form id="agregarEjemplarForm">
                    <!-- Campos ocultos -->
                    <div class="mb-3">
                        <input type="hidden" name="isbn" id="isbnObraCompleta" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="id" id="idEjemplarAgregar" class="form-control" readonly>
                    </div>
                    
                    <!-- Cota -->
                    <div class="mb-3">
                        <label for="cota" class="form-label text-dark">Cota</label>
                        <input class="form-control" type="text" id="cotaEjemplar" name="cota" placeholder="Cota del libro" readonly>
                    </div>

                    <!-- Selector de Estantería -->
                    <div class="mb-3 form-group">
                        <label for="estanteriaModal" class="form-label text-dark d-block w-100">Estantería</label>
                        <select name="estanteria" id="estanteriaModal" class="form-select select2-estanteria w-100" required>
                            <option value="">Seleccione una estantería</option>
                        </select>
                    </div>

                    <!-- Selector de Fila -->
                    <div class="mb-3 form-group">
                        <label for="filaModal" class="form-label text-dark d-block w-100">Fila</label>
                        <select name="fila" id="filaModal" class="form-select select2-fila w-100" disabled required>
                            <option value="" id="mensajeFila">Seleccione primero una estantería</option>
                        </select>
                        <div id="filaInfoContainerModal" class="mt-2" style="display: none;">
                            <small id="espaciosDisponiblesModal" class="text-muted"></small>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="mb-3">
                        <label for="cantidadEjemplaresModal" class="form-label text-dark">Cantidad de Ejemplares</label>
                        <input class="form-control" type="text" name="cantidad" id="cantidadEjemplaresModal" placeholder="Cantidad" min="1" disabled required>
                        <div id="errorCantidadModal" class="invalid-feedback"></div>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-success" id="submitBtnModal" disabled>Agregar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos generales del modal */
/* Estilos mejorados para Select2 con texto visible */
.custom-modal-ejemplar .select2-container {
    width: 100% !important;
    margin-top: 0.25rem;
}

.custom-modal-ejemplar .select2-selection {
    height: auto !important;
    min-height: 38px;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    padding: 0.375rem 2.5rem 0.375rem 0.75rem !important;
    width: 100% !important;
    display: flex !important;
    align-items: center;
}

.custom-modal-ejemplar .select2-selection__rendered {
    width: 100% !important;
    white-space: normal !important;
    text-overflow: initial !important;
    overflow: visible !important;
    padding-right: 20px !important;
    line-height: 1.5 !important;
    color: #495057 !important;
    display: block !important;
}

.custom-modal-ejemplar .select2-selection__arrow {
    height: 100% !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    right: 8px !important;
    width: 20px !important;
}

/* Ajustes para el dropdown */
.custom-modal-ejemplar .select2-dropdown {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    z-index: 1061 !important;
    width: 100% !important;
}

/* Asegurar que las opciones sean visibles */
.custom-modal-ejemplar .select2-results__option {
    padding: 8px 12px !important;
    white-space: normal !important;
}

/* Espacio adicional para el texto largo */
.custom-modal-ejemplar .select2-container--default .select2-selection--single {
    height: auto !important;
    min-height: 38px;
}

/* Ajuste para cuando está abierto */
.custom-modal-ejemplar .select2-container--open .select2-selection {
    min-height: 38px;
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}
</style>

<script>
$(document).ready(function() {
    // Variables para el modal
    let espaciosDisponiblesModal = 0;
    const $estanteriaModal = $('#estanteriaModal');
    const $filaModal = $('#filaModal');
    const $mensajeFila = $('#mensajeFila');
    const $cantidadModal = $('#cantidadEjemplaresModal');
    const $errorCantidadModal = $('#errorCantidadModal');
    const $espaciosDisponiblesModal = $('#espaciosDisponiblesModal');
    const $filaInfoContainerModal = $('#filaInfoContainerModal');
    const $submitBtnModal = $('#submitBtnModal');

    // Configuración global de Select2 para mantener consistencia
    $.fn.select2.defaults.set('width', '100%');
    $.fn.select2.defaults.set('dropdownParent', $('#nuevoEjemplar'));
    $.fn.select2.defaults.set('minimumResultsForSearch', 10);

    // Inicializar Select2 para estantería
    $estanteriaModal.select2({
        ajax: {
            url: 'public/js/ajax/obtenerEstanteriaYFila.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { 
                    term: params.term,
                    action: 'estanterias'
                };
            },
            processResults: function(data) {
                return { results: data };
            },
            cache: true
        },
        placeholder: 'Seleccione la estantería',
        allowClear: true,
        dropdownAutoWidth: true,
        width: '100%'
    }).on('change', function() {
        const estanteriaId = $(this).val();
        
        // Resetear fila y cantidad
        $filaModal.val(null).trigger('change').prop('disabled', !estanteriaId);
        $mensajeFila.text(estanteriaId ? 'Seleccione una fila' : 'Seleccione primero una estantería');
        $cantidadModal.val('').prop('disabled', true);
        $filaInfoContainerModal.hide();
        espaciosDisponiblesModal = 0;
        $errorCantidadModal.hide();
        validarFormulario();
        
        if (estanteriaId) {
            cargarFilasModal(estanteriaId);
        }
    });

    // Inicializar Select2 para fila (inicialmente deshabilitado)
    $filaModal.select2({
        placeholder: 'Seleccione una fila',
        disabled: true,
        allowClear: true,
        dropdownAutoWidth: true,
        width: '100%'
    }).on('change', function() {
        const selectedOption = $(this).find('option:selected');
        espaciosDisponiblesModal = parseInt(selectedOption.data('disponible')) || 0;
        
        if ($(this).val() && !selectedOption.prop('disabled')) {
            const match = selectedOption.text().match(/\d+\/(\d+)/);
            const capacidad = match ? match[1] : '?';
            
            $espaciosDisponiblesModal.text(`${espaciosDisponiblesModal}/${capacidad} espacios disponibles`);
            $filaInfoContainerModal.show();
            $cantidadModal.prop('disabled', false);
            
            if ($cantidadModal.val()) {
                validarCantidadModal();
            }
        } else {
            $filaInfoContainerModal.hide();
            $cantidadModal.prop('disabled', true).val('');
            $errorCantidadModal.hide();
        }
        validarFormulario();
    });

    // Función para cargar filas en el modal
    function cargarFilasModal(estanteriaId) {
        $.ajax({
            url: 'public/js/ajax/obtenerEstanteriaYFila.php',
            dataType: 'json',
            data: { 
                estanteria: estanteriaId,
                action: 'filas'
            },
            success: function(data) {
                $filaModal.empty().append('<option value="">Seleccione una fila...</option>');
                
                data.forEach(function(item) {
                    const disponible = item.disponible || 0;
                    const disabled = disponible <= 0 ? 'disabled' : '';
                    
                    $filaModal.append(
                        `<option value="${item.id}" ${disabled} 
                          data-disponible="${disponible}">
                            ${item.text}
                        </option>`
                    );
                });
                
                $filaModal.prop('disabled', false).trigger('change.select2');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar filas:', error);
                $filaModal.empty().append('<option value="">Error al cargar filas</option>');
            }
        });
    }

    // Validar cantidad en el modal
    function validarCantidadModal() {
        const cantidad = parseInt($cantidadModal.val()) || 0;
        
        if (isNaN(cantidad)) {
            $errorCantidadModal.text('Ingrese un número válido').show();
            return false;
        }
        
        if (cantidad <= 0) {
            $errorCantidadModal.text('La cantidad debe ser mayor a cero').show();
            return false;
        }
        
        if (cantidad > espaciosDisponiblesModal) {
            $errorCantidadModal.text(`Solo hay ${espaciosDisponiblesModal} espacios disponibles`).show();
            return false;
        }
        
        $errorCantidadModal.hide();
        return true;
    }

    // Validar cantidad al cambiar valor
    $cantidadModal.on('input', function() {
        // Solo permitir números enteros
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Validar solo si hay valor
        if (this.value) {
            validarCantidadModal();
        } else {
            $errorCantidadModal.hide();
        }
        validarFormulario();
    });

    // Validar todo el formulario
    function validarFormulario() {
        const estanteriaValida = $estanteriaModal.val() && $estanteriaModal.val() !== '';
        const filaValida = $filaModal.val() && $filaModal.val() !== '' && !$filaModal.find('option:selected').prop('disabled');
        const cantidadValida = $cantidadModal.val() && validarCantidadModal();
        
        $submitBtnModal.prop('disabled', !(estanteriaValida && filaValida && cantidadValida));
        return estanteriaValida && filaValida && cantidadValida;
    }

});
</script>