<!-- Modal -->
<div class="modal fade" id="reporteModal" tabindex="-1" aria-labelledby="reporteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reporteModalLabel">Generar Reporte PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs -->
                <ul class="tab-list">
                    <li class="tab active" data-tab="tab-fechas">Por Rango de Fechas</li>
                    <li class="tab" data-tab="tab-acciones">Por Tipo de Acción</li>
                    <li class="tab" data-tab="tab-responsable">Por Responsable</li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Tab 1: Rango de Fechas -->
                    <div class="tab-panel active" id="tab-fechas">
                        <form id="formFechas" action="app/reportes/historial_de_movimientos.php" target="_blank" method="POST">
                            <input type="hidden" name="reporte" value="fechas">
                            <label for="fechaInicio">Fecha de Inicio:</label>
                            <input type="date" id="fechaInicio" name="fechaInicio" value="<?php echo $hoy ?>" onchange="validarFechas('fechaInicio', 'fechaFin')" required>
                            <small id="fechaInicio-error" class="error-message"></small>
                            <label for="fechaFin">Fecha de Fin:</label>
                            <input type="date" id="fechaFin" name="fechaFin" value="<?php echo $hoy ?>" onchange="validarFechas('fechaInicio', 'fechaFin')" required>
                            <small id="fechaFin-error" class="error-message"></small>
                            <button type="submit" class="btn-submit">Generar Reporte</button>
                        </form>
                    </div>

                    <!-- Tab 2: Tipo de Acción -->
                    <div class="tab-panel" id="tab-acciones">
                        <form id="formAcciones" action="app/reportes/historial_de_movimientos.php" target="_blank" method="POST">
                            <input type="hidden" name="reporte" value="acciones">
                            <label for="tipoAccion">Tipo de Acción:</label>
                            <select id="tipoAccion" name="tipoAccion" required>
                                <option value="all">Todos</option>


                                <option value="1">Registro de libro</option>
                                <option value="2">Registro de ejemplar</option>
                                <option value="3">Registro de usuario</option>
                                <option value="4">Usuario deshabilitado</option>
                                <option value="5">Ejemplares desincorporados</option>
                                <option value="6">Visitantes</option>
                                <option value="7">Asistencias</option>
                                <option value="8">Actividades canceladas</option>
                                <option value="9">Actividades reprogramadas
                                <option value="10">Préstamos realizados</option>
                                <option value="11">Préstamos renovados</option>
                                <option value="12">Préstamos devueltos</option>
                            </select>
                            <div id="fechaAccionOpcional" class="optional-dates">
                                <label for="accionFechaInicio">Fecha de Inicio:</label>
                                <input type="date" id="accionFechaInicio" name="accionFechaInicio">
                                <label for="accionFechaFin">Fecha de Fin:</label>
                                <input type="date" id="accionFechaFin" name="accionFechaFin">
                            </div>
                            <button type="submit" class="btn-submit">Generar Reporte</button>
                        </form>
                    </div>

                    <!-- Tab 3: Por Responsable -->
                    <div class="tab-panel" id="tab-responsable">
                        <form id="formResponsable" action="app/reportes/historial_de_movimientos.php" target="_blank" method="POST">
                            <input type="hidden" name="reporte" value="responsable">
                            <label for="responsable">Responsable:</label>
                            <select name="responsable" id="responsable">

                            </select>
                            <div id="fechaResponsableOpcional" class="optional-dates">
                                <label for="responsableFechaInicio">Fecha de Inicio:</label>
                                <input type="date" id="responsableFechaInicio" name="responsableFechaInicio">
                                <label for="responsableFechaFin">Fecha de Fin:</label>
                                <input type="date" id="responsableFechaFin" name="responsableFechaFin">
                            </div>
                            <button type="submit" class="btn-submit">Generar Reporte</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

.error-message {
    color: #dc3545;
    font-size: 0.875em;
    display: block;
    margin-top: 5px;
}

.error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
}
    /* General Styles */
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
        font-family: 'Arial', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        border: 1px solid #ddd;
    }

    .modal-header {
        /*background-color: #007bff;*/
        background-color: #4CAF50;
        color: white;
        padding: 1rem;
    }

    .modal-title {
        font-size: 1.5rem;
    }

    .tab-list {
        display: flex;
        justify-content: space-around;
        padding: 0;
        margin: 0;
        list-style: none;
        background-color: #4CAF50;
        border-bottom: 2px solid #ddd;
    }

    .tab {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        cursor: pointer;
        font-weight: bold;
        color: white;
        transition: background-color 0.3s ease;
    }

    .tab:hover,
    .tab.active {
        background-color: green;
    }

    .tab-content {
        padding: 1rem;
        border: 1px solid #ddd;
        background-color: white;
        border-radius: 0 0 10px 10px;
        margin-top: -2px;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    input[type="date"],
    select {
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    /*input[type="text"] {
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ddd;
        border-radius: 5px;
    }*/

    .btn-submit {
        background-color: #007bff;
        color: white;
        padding: 0.7rem;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #0056b3;
    }
</style>
<script src="node_modules/select2/js/select2.min.js"></script>

<script>
    // JavaScript para cambiar entre tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));

            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });


    //Javascript select2 responsables

    $(document).ready(function() {
        // Inicializa la variable checkerCota
        // let checkerCota = false;

        $('#responsable').select2({
            dropdownParent: $('#reporteModal'), // Para asegurarte de que funcione dentro del modal
            placeholder: 'Cédula o nombre del responsable',
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
                        responsable: params.term // Nombre de la cota que el usuario escribe
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
                return data.text || 'Seleccionar responsable';
            }
        });

        // Evento cuando se selecciona una opción
        /*
        $('#responsable').on('select2:select', function() {
            checkerCota = true; // Cambiar checkerCota a true
            checkChecker();

            console.log('Cota seleccionada:', checkerCota);
        });

        // Evento cuando se deselecciona una opción
        $('#responsable').on('select2:unselect', function() {
            checkerCota = false; // Cambiar checkerCota a false
            checkChecker();

            console.log('Cota deseleccionada:', checkerCota);
        });

        // Para comprobar el estado inicial al cargar
        $('#cota').on('change', function() {
            checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
            checkChecker();

            console.log('Cambio detectado:', checkerCota);
        });

        */
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Configuración de tabs
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Función para obtener la fecha actual en formato YYYY-MM-DD
    function getCurrentDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Función principal de validación corregida
    function validarParFechas(inicioId, finId, esRequerido = true) {
        const inputInicio = document.getElementById(inicioId);
        const inputFin = document.getElementById(finId);
        const errorInicio = document.getElementById(`${inicioId}-error`) || crearElementoError(inicioId);
        const errorFin = document.getElementById(`${finId}-error`) || crearElementoError(finId);
        const fechaHoy = getCurrentDate();
        let isValid = true;

        // Limpiar errores previos
        errorInicio.textContent = '';
        errorFin.textContent = '';
        inputInicio.classList.remove('error');
        inputFin.classList.remove('error');

        // Validación para campos requeridos
        if (esRequerido) {
            if (!inputInicio.value) {
                inputInicio.value = fechaHoy;
                errorInicio.textContent = 'Se estableció la fecha de hoy';
                isValid = false;
            }
            if (!inputFin.value) {
                inputFin.value = fechaHoy;
                errorFin.textContent = 'Se estableció la fecha de hoy';
                isValid = false;
            }
        }

        // Solo validar si ambos campos tienen valor
        if (inputInicio.value && inputFin.value) {
            const fechaInicio = new Date(inputInicio.value);
            const fechaFin = new Date(inputFin.value);
            const hoy = new Date(fechaHoy);

            // Validar que fechaInicio no sea en el futuro (pero sí permite cualquier pasado)
            if (fechaInicio > hoy) {
                inputInicio.value = fechaHoy;
                errorInicio.textContent = 'No puede ser fecha futura. Se estableció hoy';
                inputInicio.classList.add('error');
                isValid = false;
            }

            // Validar que fechaFin no sea anterior a fechaInicio
            if (fechaFin < fechaInicio) {
                inputFin.value = inputInicio.value;
                errorFin.textContent = 'No puede ser anterior al inicio. Se ajustó';
                inputFin.classList.add('error');
                isValid = false;
            }

            // Validar que fechaFin no sea en el futuro (pero sí permite cualquier pasado)
            if (fechaFin > hoy) {
                inputFin.value = fechaHoy;
                errorFin.textContent = 'No puede ser fecha futura. Se estableció hoy';
                inputFin.classList.add('error');
                isValid = false;
            }
        }

        return isValid;
    }

    // Función para crear elementos de error
    function crearElementoError(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return null;
        
        const errorElement = document.createElement('small');
        errorElement.id = `${inputId}-error`;
        errorElement.className = 'error-message';
        input.parentNode.insertBefore(errorElement, input.nextSibling);
        
        return errorElement;
    }

    // Eventos para el formulario de fechas (requerido)
    const formFechas = document.getElementById('formFechas');
    if (formFechas) {
        formFechas.addEventListener('submit', function(e) {
            if (!validarParFechas('fechaInicio', 'fechaFin', true)) {
                e.preventDefault();
            }
        });

        document.getElementById('fechaInicio').addEventListener('change', function() {
            validarParFechas('fechaInicio', 'fechaFin', true);
        });
        
        document.getElementById('fechaFin').addEventListener('change', function() {
            validarParFechas('fechaInicio', 'fechaFin', true);
        });
    }

    // Eventos para el formulario de acciones (opcional)
    const formAcciones = document.getElementById('formAcciones');
    if (formAcciones) {
        formAcciones.addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('accionFechaInicio').value;
            const fechaFin = document.getElementById('accionFechaFin').value;
            
            if (fechaInicio || fechaFin) {
                if (!validarParFechas('accionFechaInicio', 'accionFechaFin', false)) {
                    e.preventDefault();
                }
            }
        });

        document.getElementById('accionFechaInicio').addEventListener('change', function() {
            validarParFechas('accionFechaInicio', 'accionFechaFin', false);
        });
        
        document.getElementById('accionFechaFin').addEventListener('change', function() {
            validarParFechas('accionFechaInicio', 'accionFechaFin', false);
        });
    }

    // Eventos para el formulario de responsable (opcional)
    const formResponsable = document.getElementById('formResponsable');
    if (formResponsable) {
        formResponsable.addEventListener('submit', function(e) {
            const fechaInicio = document.getElementById('responsableFechaInicio').value;
            const fechaFin = document.getElementById('responsableFechaFin').value;
            
            if (fechaInicio || fechaFin) {
                if (!validarParFechas('responsableFechaInicio', 'responsableFechaFin', false)) {
                    e.preventDefault();
                }
            }
        });

        document.getElementById('responsableFechaInicio').addEventListener('change', function() {
            validarParFechas('responsableFechaInicio', 'responsableFechaFin', false);
        });
        
        document.getElementById('responsableFechaFin').addEventListener('change', function() {
            validarParFechas('responsableFechaInicio', 'responsableFechaFin', false);
        });
    }
});

// Función global para usar en atributos onchange
function validarFechas(inicioId, finId) {
    const inputInicio = document.getElementById(inicioId);
    const inputFin = document.getElementById(finId);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    
    if (inputInicio.value && inputFin.value) {
        const fechaInicio = new Date(inputInicio.value);
        const fechaFin = new Date(inputFin.value);
        
        // Validar que no sean fechas futuras
        if (fechaInicio > hoy) {
            inputInicio.value = hoy.toISOString().split('T')[0];
            const errorInicio = document.getElementById(`${inicioId}-error`);
            if (errorInicio) {
                errorInicio.textContent = 'No puede ser fecha futura. Se estableció hoy';
            }
            return false;
        }
        
        if (fechaFin > hoy) {
            inputFin.value = hoy.toISOString().split('T')[0];
            const errorFin = document.getElementById(`${finId}-error`);
            if (errorFin) {
                errorFin.textContent = 'No puede ser fecha futura. Se estableció hoy';
            }
            return false;
        }
        
        // Validar que fechaFin no sea anterior a fechaInicio
        if (fechaFin < fechaInicio) {
            inputFin.value = inputInicio.value;
            const errorFin = document.getElementById(`${finId}-error`);
            if (errorFin) {
                errorFin.textContent = 'No puede ser anterior al inicio. Se ajustó';
            }
            return false;
        }
    }
    return true;
}
</script>

<style>
    /* Estilos para los mensajes de error */
.error-message {
    color: #dc3545;
    font-size: 0.875em;
    display: block;
    margin-top: 0.25rem;
}

.error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Estilos para los tabs */
.tab-list {
    display: flex;
    list-style: none;
    padding: 0;
    margin-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.tab {
    padding: 0.5rem 1rem;
    cursor: pointer;
    margin-right: 0.5rem;
    border: 1px solid transparent;
    border-radius: 0.25rem 0.25rem 0 0;
}

.tab.active {
    background-color: #f8f9fa;
    border-color: #dee2e6 #dee2e6 #f8f9fa;
    color: #495057;
}

.tab-panel {
    display: none;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-top: none;
}

.tab-panel.active {
    display: block;
}

/* Estilos para los formularios */
.modal-body form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.modal-body label {
    font-weight: 500;
}

.modal-body input[type="date"],
.modal-body select {
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.btn-submit {
    background-color: #0d6efd;
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
    align-self: flex-start;
}

.btn-submit:hover {
    background-color: #0b5ed7;
}
</style>