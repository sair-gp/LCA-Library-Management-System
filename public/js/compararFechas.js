/**
 * Valida dos fechas (inicio y fin) mostrando errores debajo de los inputs
 * @param {string} idFechaInicio - ID del input de fecha de inicio
 * @param {string} idFechaFin - ID del input de fecha de fin
 * @returns {boolean} - Devuelve true si la validación es correcta
 */
function validarFechas(idFechaInicio, idFechaFin) {
    // Obtener elementos del DOM
    const inputInicio = document.getElementById(idFechaInicio);
    const inputFin = document.getElementById(idFechaFin);
    
    // Crear elementos de error si no existen
    let errorInicio = document.getElementById(`${idFechaInicio}-error`);
    let errorFin = document.getElementById(`${idFechaFin}-error`);
    
    if (!errorInicio) {
        errorInicio = document.createElement('small');
        errorInicio.id = `${idFechaInicio}-error`;
        errorInicio.className = 'error-message';
        inputInicio.parentNode.appendChild(errorInicio);
    }
    
    if (!errorFin) {
        errorFin = document.createElement('small');
        errorFin.id = `${idFechaFin}-error`;
        errorFin.className = 'error-message';
        inputFin.parentNode.appendChild(errorFin);
    }
    
    // Limpiar errores previos
    errorInicio.textContent = '';
    errorFin.textContent = '';
    inputInicio.classList.remove('error');
    inputFin.classList.remove('error');
    
    // Obtener valores de las fechas
    const fechaInicio = inputInicio.value ? new Date(inputInicio.value) : null;
    const fechaFin = inputFin.value ? new Date(inputFin.value) : null;
    
    // Validar que ambas fechas tengan valor
    if (!inputInicio.value || !inputFin.value) {
        if (!inputInicio.value) {
            errorInicio.textContent = 'La fecha de inicio es requerida';
            inputInicio.classList.add('error');
        }
        if (!inputFin.value) {
            errorFin.textContent = 'La fecha de fin es requerida';
            inputFin.classList.add('error');
        }
        return false;
    }
    
    // Validar que la fecha de inicio no sea mayor que la de fin
    if (fechaInicio > fechaFin) {
        errorFin.textContent = 'La fecha de fin no puede ser anterior a la de inicio';
        inputFin.classList.add('error');
        inputFin.focus();
        return false;
    }
    
    // Validar que la fecha de inicio no sea en el pasado
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Ignorar la hora para comparar solo fechas
    
    if (fechaInicio < hoy) {
        errorInicio.textContent = 'La fecha de inicio no puede ser en el pasado';
        inputInicio.classList.add('error');
        inputInicio.focus();
        return false;
    }
    
    // Si pasa todas las validaciones
    return true;
}