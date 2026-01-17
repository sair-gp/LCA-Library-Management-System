
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const numFilasInput = document.getElementById('num-filas');
    const numFilasValue = document.getElementById('num-filas-value');
    const mismaClasificacion = document.getElementById('misma-clasificacion');
    const diferenteClasificacion = document.getElementById('diferente-clasificacion');
    const mismaContainer = document.getElementById('clasificacion-misma-container');
    const diferenteContainer = document.getElementById('clasificacion-diferente-container');
    const filasGrid = document.getElementById('filas-grid');
    const btnExplicacion = document.getElementById('btn-explicacion-codigo');
    const explicacionCodigo = document.getElementById('explicacion-codigo');
    const previewNombre = document.getElementById('preview-nombre');
    const previewCodigo = document.getElementById('preview-codigo');
    const previewFilas = document.getElementById('preview-filas');
    const previewTotalLibros = document.getElementById('preview-total-libros');
    const nombreEstanteria = document.getElementById('nombre-estanteria');
    const codigoEstanteria = document.getElementById('codigo-estanteria');
    const capacidadFila = document.getElementById('capacidad-fila');
    const clasificacionGeneral = document.getElementById('clasificacion-general');
    const deweyPreviewText = document.getElementById('dewey-preview-text');
    const btnGuardar = document.getElementById('btn-guardar');
    
    // Variables de estado
    let formIsValid = false;
    
    // Actualizar número de filas visualmente
    numFilasInput.addEventListener('input', function() {
        numFilasValue.textContent = this.value;
        if (diferenteClasificacion.checked) {
            generarFilasConfig();
        }
        actualizarPreview();
        validarFormulario();
    });
    
    // Alternar entre tipos de clasificación
    mismaClasificacion.addEventListener('change', function() {
        if (this.checked) {
            mismaContainer.style.display = 'block';
            diferenteContainer.style.display = 'none';
            clasificacionGeneral.setAttribute('required', ''); // ← Vuelve a agregar required
            validarFormulario();
            actualizarPreview();
        }
    });
    
    diferenteClasificacion.addEventListener('change', function() {
        if (this.checked) {
            mismaContainer.style.display = 'none';
            diferenteContainer.style.display = 'block';
            clasificacionGeneral.removeAttribute('required'); //remueve el required del select
            generarFilasConfig();
            validarFormulario();
            actualizarPreview();
        }
    });
    
    // Mostrar/ocultar explicación del código
    btnExplicacion.addEventListener('click', function() {
        explicacionCodigo.style.display = explicacionCodigo.style.display === 'none' ? 'block' : 'none';
    });
    
    // Actualizar vista previa de Dewey
    clasificacionGeneral.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        deweyPreviewText.textContent = selectedOption.value ? selectedOption.text : 'Ninguna categoría seleccionada';
        validarFormulario();
        actualizarPreview();
    });
    
    // Generar configuración de filas
    function generarFilasConfig() {
        const numFilas = parseInt(numFilasInput.value);
        filasGrid.innerHTML = '';
        
        for (let i = 0; i < numFilas; i++) {
            const filaHTML = `
                <div class="fila-config">
                    <h4>Fila ${i + 1}</h4>
                    <select class="fila-dewey" name="fila-dewey[]" data-fila="${i}" required>
                        <option value="">-- Seleccione categoría --</option>
                        <option value='1'>000 - Generalidades, Ciencia de la computación e información</option>
                        <option value='2'>100 - Filosofía y psicología</option>
                        <option value='3'>200 - Religión</option>
                        <option value='4'>300 - Ciencias sociales</option>
                        <option value='5'>400 - Lengua</optgroup>
                        <option value='6'>500 - Ciencia</option>
                        <option value='7'>600 - Tecnología</option>
                        <option value='8'>700 - Artes y recreación</option>
                        <option value='9'>800 - Literatura y retórica</option>
                        <option value='10'>900 - Historia y geografía</option>
                    </select>
                </div>`;
            filasGrid.insertAdjacentHTML('beforeend', filaHTML);
        }
        
        // Agregar eventos a los nuevos selects
        document.querySelectorAll('.fila-dewey').forEach(select => {
            select.addEventListener('change', function() {
                validarFormulario();
                actualizarPreview();
            });
        });
    }
    
    // Validar campos individuales
    function validarCampo(campo, regex = null) {
        const grupo = campo.closest('.form-group');
        const errorId = campo.id + '-error';
        const errorElement = document.getElementById(errorId);
        
        if (!campo.value.trim()) {
            grupo.classList.add('invalid');
            return false;
        }
        
        if (regex && !regex.test(campo.value)) {
            grupo.classList.add('invalid');
            return false;
        }
        
        if (campo.type === 'number') {
            const min = parseInt(campo.min);
            const max = parseInt(campo.max);
            const value = parseInt(campo.value);
            
            if (isNaN(value) || value < min || value > max) {
                grupo.classList.add('invalid');
                return false;
            }
        }
        
        if (campo.hasAttribute('required') && !campo.value) {
            grupo.classList.add('invalid');
            return false;
        }
        
        grupo.classList.remove('invalid');
        return true;
    }
    
    // Validar clasificación Dewey por filas
    function validarClasificacionFilas() {
        if (!diferenteClasificacion.checked) return true;
        
        const selects = document.querySelectorAll('.fila-dewey');
        let todasValidas = true;
        const filasError = document.getElementById('filas-error');
        
        selects.forEach(select => {
            if (!select.value) {
                select.closest('.fila-config').classList.add('invalid');
                todasValidas = false;
            } else {
                select.closest('.fila-config').classList.remove('invalid');
            }
        });
        
        filasError.style.display = todasValidas ? 'none' : 'block';
        return todasValidas;
    }
    
    // Validar todo el formulario
    function validarFormulario() {
        const nombreValido = validarCampo(nombreEstanteria);
        const codigoValido = validarCampo(codigoEstanteria, new RegExp(codigoEstanteria.pattern));
        const capacidadValida = validarCampo(capacidadFila);
        
        let clasificacionValida = false;
        
        if (mismaClasificacion.checked) {
            clasificacionValida = validarCampo(clasificacionGeneral);
        } else {
            clasificacionValida = validarClasificacionFilas();
        }
        
        formIsValid = nombreValido && codigoValido && capacidadValida && clasificacionValida;
        btnGuardar.disabled = !formIsValid;
        
        return formIsValid;
    }
    
    // Actualizar vista previa
    function actualizarPreview() {
        // Actualizar información básica
        previewNombre.textContent = nombreEstanteria.value || 'Nueva Estantería';
        previewCodigo.textContent = codigoEstanteria.value || 'SIN-CODIGO';
        
        // Calcular total de libros
        const numFilas = parseInt(numFilasInput.value);
        const capacidad = parseInt(capacidadFila.value) || 0;
        previewTotalLibros.textContent = numFilas * capacidad;
        
        // Generar filas de preview
        previewFilas.innerHTML = '';
        
        if (mismaClasificacion.checked) {
            // Todas las filas iguales
            const clasificacion = clasificacionGeneral.value;
            const selectedOption = clasificacionGeneral.options[clasificacionGeneral.selectedIndex];
            const clasificacionText = selectedOption ? selectedOption.text.split(' - ')[1] : 'Sin clasificación';
            
            for (let i = 0; i < numFilas; i++) {
                const filaHTML = `
                    <div class="preview-fila">
                        <span class="preview-fila-numero">${i + 1}</span>
                        <span class="preview-fila-dewey">${clasificacion ? clasificacion + ' - ' + clasificacionText : 'Sin clasificación'}</span>
                        <span class="preview-fila-capacidad">${capacidad} libros</span>
                    </div>`;
                previewFilas.insertAdjacentHTML('beforeend', filaHTML);
            }
        } else {
            // Filas con diferente clasificación
            const selects = document.querySelectorAll('.fila-dewey');
            
            selects.forEach((select, i) => {
                const selectedOption = select.options[select.selectedIndex];
                const clasificacionText = selectedOption ? selectedOption.text.split(' - ')[1] : 'Sin clasificación';
                const filaHTML = `
                    <div class="preview-fila">
                        <span class="preview-fila-numero">${i + 1}</span>
                        <span class="preview-fila-dewey">${select.value ? select.value + ' - ' + clasificacionText : 'Sin clasificación'}</span>
                        <span class="preview-fila-capacidad">${capacidad} libros</span>
                    </div>`;
                previewFilas.insertAdjacentHTML('beforeend', filaHTML);
            });
        }
    }
    
    // Eventos para validación en tiempo real
    nombreEstanteria.addEventListener('input', function() {
        validarCampo(this);
        validarFormulario();
        actualizarPreview();
    });
    
    codigoEstanteria.addEventListener('input', function() {
        validarCampo(this, new RegExp(this.pattern));
        validarFormulario();
        actualizarPreview();
    });
    
    capacidadFila.addEventListener('input', function() {
        validarCampo(this);
        validarFormulario();
        actualizarPreview();
    });
    
    // Inicializar
    actualizarPreview();
    validarFormulario();
    
    // Manejar envío del formulario
   /* btnGuardar.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            alert('Por favor complete todos los campos correctamente');
            return;
        }
        
        // Construir objeto con los datos
        const estanteriaData = {
            nombre: nombreEstanteria.value,
            codigo: codigoEstanteria.value,
            capacidadPorFila: parseInt(capacidadFila.value),
            numFilas: parseInt(numFilasInput.value),
            tipoClasificacion: mismaClasificacion.checked ? 'misma' : 'diferente',
            clasificacion: mismaClasificacion.checked ? clasificacionGeneral.value : null,
            filas: []
        };
        
        if (diferenteClasificacion.checked) {
            document.querySelectorAll('.fila-dewey').forEach(select => {
                estanteriaData.filas.push({
                    numero: parseInt(select.dataset.fila) + 1,
                    clasificacion: select.value
                });
            });
        }
        
        // Aquí iría la lógica para enviar los datos al servidor
        console.log('Datos a guardar:', estanteriaData);
        alert('Estantería registrada exitosamente!');
        
        // Limpiar formulario
        document.querySelector('.estanteria-registro-container').reset();
        if (diferenteClasificacion.checked) {
            generarFilasConfig();
        }
        actualizarPreview();
        validarFormulario();
    });*/
    
    // Manejar cancelar
    document.getElementById('btn-cancelar').addEventListener('click', function() {
        if (confirm('¿Está seguro que desea cancelar? Los datos no guardados se perderán.')) {
            document.querySelector('.estanteria-registro-container').reset();
            if (diferenteClasificacion.checked) {
                generarFilasConfig();
            }
            actualizarPreview();
            validarFormulario();
        }
    });
});
