<!-- Modal -->
<div class="modal fade" id="registrarActividad" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar Actividad</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/reg_actividades/c_actividades.php" method="POST">

                    <div class="col-12">
                        <label for="cota">Descripcion</label><br>
                        <input type="text" name="descripcion" id="id_descripcion" class="form-control">
                        <label for="cota">Encargado</label>
                        <input type="text" name="encargado" id="id_encargado" class="form-control">
                        <label for="cota">Fecha de inicio</label>
                        <input type="date" name="fecha_ini" id="id_fechaInicio" class="form-control" value="<?php echo $hoy ?>">
                        <label for="cota">Fecha de finalizacion</label>
                        <input type="date" name="fecha_fin" id="id_fechaFin" value="<?php echo $hoy ?>" class="form-control">
                        <!--input type="hidden" name="responsable" id="responsable" class="form-control"-->

                        <label>Duración (por día)</label>
        <div class="input-group mb-3">
            <input type="number" id="duracion-horas" name="duracion_horas" 
                   class="form-control" placeholder="Horas" min="0" max="99" value="0" required>
            <span class="input-group-text">:</span>
            <input type="number" id="duracion-minutos" name="duracion_minutos" 
                   class="form-control" placeholder="Minutos" min="0" max="59" value="0" required>
        </div>
        <div id="error-duracion" class="text-danger small d-none"></div>
                    </div>




                    <div class="modal-footer">
                        <button type="submit" id="boton_submit" class="btn btn-success">Agregar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Configuración
    const HORAS_MAXIMAS_POR_DIA = 4;
    const DIAS_LABORALES_POR_SEMANA = 5;
    const MAX_DIAS_CALENDARIO = 7;

    // Elementos del DOM de tu modal exacto
    const form = document.querySelector('#registrarActividad form');
    const inputDescripcion = document.getElementById('id_descripcion');
    const inputEncargado = document.getElementById('id_encargado');
    const inputFechaInicio = document.getElementById('id_fechaInicio');
    const inputFechaFin = document.getElementById('id_fechaFin');
    const botonSubmit = document.getElementById('boton_submit');
    const inputHoras = document.getElementById('duracion-horas');
    const inputMinutos = document.getElementById('duracion-minutos');
    const errorDiv = document.getElementById('error-duracion');

    // ===== FUNCIÓN DE VALIDACIÓN DE DURACIÓN MODIFICADA ===== //
    function validarDuracion() {
        const horas = parseInt(inputHoras.value) || 0;
        const minutos = parseInt(inputMinutos.value) || 0;
        let valido = true;
        errorDiv.textContent = '';
        
        // Validación básica
        if (horas < 0 || minutos < 0) {
            errorDiv.textContent = 'Los valores no pueden ser negativos';
            valido = false;
        }
        
        if (minutos > 59) {
            errorDiv.textContent = 'Los minutos no pueden ser mayores a 59';
            valido = false;
        }
        
        if (horas === 0 && minutos === 0) {
            errorDiv.textContent = 'La duración no puede ser cero';
            valido = false;
        }

        // Validación por día (máximo 4 horas por día)
        const horasTotales = horas + (minutos / 60);
        if (horasTotales > HORAS_MAXIMAS_POR_DIA) {
            errorDiv.textContent = `Duración máxima permitida por día: ${HORAS_MAXIMAS_POR_DIA}h`;
            valido = false;
        }

        // Mostrar/ocultar error
        errorDiv.classList.toggle('d-none', valido);
        inputHoras.classList.toggle('is-invalid', !valido);
        inputMinutos.classList.toggle('is-invalid', !valido);
        
        return valido;
    }

    // ===== FUNCIONES AUXILIARES ===== //
    function calcularDiasLaborales(inicio, fin) {
        let count = 0;
        const curDate = new Date(inicio);
        const endDate = new Date(fin);
        
        while (curDate <= endDate) {
            const dayOfWeek = curDate.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) count++;
            curDate.setDate(curDate.getDate() + 1);
        }
        return count;
    }

    function validarDescripcion() {
        const valido = inputDescripcion.value.trim() !== '';
        inputDescripcion.classList.toggle('is-invalid', !valido);
        return valido;
    }

    function validarEncargado() {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        const valido = inputEncargado.value.trim() !== '' && regex.test(inputEncargado.value);
        inputEncargado.classList.toggle('is-invalid', !valido);
        return valido;
    }

    function validarFechas() {
    let valido = true;
    
    if (!inputFechaInicio.value || !inputFechaFin.value) {
        inputFechaInicio.classList.add('is-invalid');
        inputFechaFin.classList.add('is-invalid');
        return false;
    }

    const fechaInicio = new Date(inputFechaInicio.value);
    fechaInicio.setDate(fechaInicio.getDate() + 1);
    const fechaFin = new Date(inputFechaFin.value);
    fechaFin.setDate(fechaFin.getDate() + 1);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    console.log("Inicio" + fechaInicio + "y la actual: " + hoy + "Y la de fin: " + fechaFin)

    if (fechaInicio < hoy || fechaFin < hoy) {
        errorDiv.textContent = 'No se permiten fechas anteriores al día actual';
        valido = false;
    }

    if (fechaInicio > fechaFin) {
        errorDiv.textContent = 'La fecha de inicio no puede ser mayor a la final';
        valido = false;
    }

    const diasNaturales = Math.floor((fechaFin - fechaInicio) / (86400000)) + 1;
    if (diasNaturales > MAX_DIAS_CALENDARIO) {
        errorDiv.textContent = `Máximo ${MAX_DIAS_CALENDARIO} días naturales permitidos`;
        valido = false;
    }

    // Validación de que no empiece en sábado (día 6)
    if (fechaInicio.getDay() === 0) {
        errorDiv.textContent = 'La fecha de inicio no puede ser un domingo';
        valido = false;
    }

    // Validación de que no termine en sábado (día 6)
    if (fechaFin.getDay() === 0) {
        errorDiv.textContent = 'La fecha de fin no puede ser un domingo';
        valido = false;
    }

    inputFechaInicio.classList.toggle('is-invalid', !valido);
    inputFechaFin.classList.toggle('is-invalid', !valido);
    return valido;
}

    function validarFormulario() {
        const descValido = validarDescripcion();
        const encValido = validarEncargado();
        const fecValido = validarFechas();
        const durValido = validarDuracion();

        botonSubmit.disabled = !(descValido && encValido && fecValido && durValido);
        return !botonSubmit.disabled;
    }

    // ===== EVENT LISTENERS ===== //
    function setupEventListeners() {
        // Validación en tiempo real
        [inputDescripcion, inputEncargado].forEach(input => {
            input.addEventListener('input', validarFormulario);
        });

        // Validación de fechas
        [inputFechaInicio, inputFechaFin].forEach(input => {
            input.addEventListener('change', function() {
                validarFechas();
                validarDuracion();
                validarFormulario();
            });
        });

        // Validación de duración
        [inputHoras, inputMinutos].forEach(input => {
            input.addEventListener('input', function() {
                // Auto-corrección de valores
                if (input === inputMinutos) {
                    let value = parseInt(this.value) || 0;
                    if (value < 0) value = 0;
                    if (value > 59) value = 59;
                    this.value = value;
                } else if (input === inputHoras) {
                    let value = parseInt(this.value) || 0;
                    if (value < 0) value = 0;
                    if (value > HORAS_MAXIMAS_POR_DIA) {
                        value = 0;
                        errorDiv.textContent = `Máximo ${HORAS_MAXIMAS_POR_DIA} horas permitidas por día`;
                        errorDiv.classList.remove('d-none');
                    }
                    this.value = value;
                }
                validarDuracion();
                validarFormulario();
            });
        });

        // Envío del formulario
        form.addEventListener('submit', function(e) {
            if (!validarFormulario()) {
                e.preventDefault();
                errorDiv.classList.remove('d-none');
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    setupEventListeners();
});
</script>