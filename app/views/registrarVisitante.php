<?php
include_once "app/config/database.php";
$conexion = conexion();
?>
<?php
include_once "app/config/database.php";
$conexion = conexion();
?>


<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --light-gray: #f8f9fa;
    }
    
    body {
        background-color: #f5f7fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container-wide {
        max-width: 1400px;
        padding: 2rem;
    }
    
    .page-header {
        border-bottom: 2px solid var(--secondary-color);
        padding-bottom: 0.1rem;
        margin-bottom: 0.5rem;
    }
    
    .form-section {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2.5rem;
    }
    
    .tutorial-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-left: 5px solid var(--secondary-color);
        height: 100%;
        padding: 2rem;
    }
    
    .image-container {
        margin: 1rem 0;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-gray);
        border-radius: 8px;
        overflow: hidden;
    }
    
    #imagePreview {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        display: none;
    }
    
    .btn-upload {
        position: relative;
        overflow: hidden;
    }
    
    .btn-group-custom .btn {
        border-radius: 0;
    }
    
    .btn-group-custom .btn:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }
    
    .btn-group-custom .btn:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    @media (max-width: 768px) {
        .container-wide {
            padding: 1rem;
        }
        
        .form-section, .tutorial-card {
            padding: 1.5rem;
        }
    }
</style>

<div class="container-wide">
    <div class="row">
        <div class="col-12">
            <!-- Encabezado -->
            <div class="page-header text-center">
                <!--h1 class="display-4 fw-bold" style="color: var(--primary-color);">Registro de Visitantes</h1-->
                <h2 class="fw-bold" style="color: var(--primary-color);">Registro de Visitantes</h2>
                <p class="lead fs-4">Complete todos los campos para registrar un nuevo visitante</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tutorial -->
        <div class="col-lg-4">
            <div class="tutorial-card">
                <h3 class="mb-4 text-primary"><i class="bi bi-info-circle"></i> Instrucciones</h3>
                <div class="alert alert-primary">
                    <i class="bi bi-exclamation-triangle"></i> Los campos marcados con <span class="required-field"></span> son obligatorios.
                </div>
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item border-0 ps-0">Ingrese la cédula del visitante (solo números)</li>
                    <li class="list-group-item border-0 ps-0">Complete los datos personales requeridos</li>
                    <li class="list-group-item border-0 ps-0">Seleccione el prefijo y número de teléfono</li>
                    <li class="list-group-item border-0 ps-0">Suba una foto de perfil (opcional)</li>
                    <li class="list-group-item border-0 ps-0">Revise que todos los datos sean correctos</li>
                    <li class="list-group-item border-0 ps-0">Presione "Registrar" para guardar</li>
                </ol>
                
                <div class="mt-4">
                    <h5><i class="bi bi-lightbulb"></i> Recomendaciones</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle text-success"></i> Verifique la cédula antes de registrar</li>
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle text-success"></i> Use fotos claras y con buen contraste</li>
                        <li class="list-group-item border-0 ps-0"><i class="bi bi-check-circle text-success"></i> Confirme el número de teléfono</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="col-lg-8">
            <div class="form-section">
                <form id="registroVisitanteForm" action="app/controller/visitantes/c_visitantes.php" method="POST" enctype="multipart/form-data">
                    <!-- Foto de perfil -->
                    <div class="mb-4">
                        <label class="form-label">Foto de perfil (opcional)</label>
                        <div class="image-container">
                            <img id="imagePreview" alt="Previsualización de foto">
                            <span id="noImageText" class="text-muted">No se ha seleccionado imagen</span>
                        </div>
                        
                        <div class="btn-group btn-group-custom mt-2" role="group">
                            <label for="fotoVisitante" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Seleccionar imagen
                            </label>
                            <button type="button" id="removeImageBtn" class="btn btn-danger" style="display: none;">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                        
                        <input type="file" name="fotoVisitante" id="fotoVisitante" accept="image/*" class="d-none">
                    </div>

                    <!-- Datos personales -->
                    <div class="row g-3">
                        <!-- Cédula -->
                        <div class="col-md-6">
                            <label for="cedula-visitante" class="form-label required-field">Cédula</label>
                            <input type="text" name="cedulaVisitante" id="validarCedulaBD" class="form-control val-cedula val-requerido" 
                                   placeholder="Ej: 12345678" maxlength="9" pattern="[0-9]+" 
                                   title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, '')" required>
                        </div>

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label for="nombre-visitante" class="form-label required-field">Nombre completo</label>
                            <input type="text" name="nombreVisitante" id="nombre-visitante" class="form-control val-texto" 
                                   placeholder="Nombre y apellido" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label class="form-label required-field">Teléfono</label>
                            <div class="d-flex gap-2">
                                <select name="prefijoVisitante" id="prefijo-visitante" class="form-select" style="width: 120px;" required>
                                    <option value="" disabled selected>Prefijo</option>
                                    <option value="0412">0412 (Digitel)</option>
                                    <option value="0414">0414 (Movistar)</option>
                                    <option value="0416">0416 (Movilnet)</option>
                                    <option value="0424">0424 (Movistar)</option>
                                    <option value="0426">0426 (Movilnet)</option>
                                    <option value="0293">0293 (Fijo)</option>
                                </select>
                                <input type="text" name="numeroVisitante" id="numero-visitante" class="form-control val-telefono" 
                                       placeholder="1234567" maxlength="7" pattern="[0-9]+" 
                                       title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, '')" required>
                            </div>
                        </div>

                        <!-- Género -->
                        <div class="col-md-6">
                            <label for="sexoVisitante" class="form-label required-field">Género</label>
                            <select class="form-select" name="sexoVisitante" id="sexoVisitante" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <option value="0">Femenino</option>
                                <option value="1">Masculino</option>
                            </select>
                        </div>

                        <!-- Correo -->
                        <div class="col-12">
                            <label for="correo-visitante" class="form-label required-field">Correo</label>
                            <input type="text" name="correoVisitante" id="correo-visitante" class="form-control val-email" 
                                   placeholder="Correo eléctronico" required>
                        </div>

                        <!-- Dirección -->
                        <div class="col-12">
                            <label for="direccion-visitante" class="form-label required-field">Dirección</label>
                            <input type="text" name="dirVisitante" id="direccion-visitante" class="form-control val-text val-requerido" 
                                   placeholder="Dirección completa" required>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between mt-5">
                        <button type="reset" class="btn btn-outline-secondary px-4" id="resetFormBtn">
                            <i class="bi bi-eraser"></i> Limpiar formulario
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Registrar Visitante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    // Variables
    const fileInput = document.getElementById('fotoVisitante');
    const previewElement = document.getElementById('imagePreview');
    const noImageText = document.getElementById('noImageText');
    const removeBtn = document.getElementById('removeImageBtn');
    const resetBtn = document.getElementById('resetFormBtn');

    // Evento para mostrar previsualización
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                previewElement.src = event.target.result;
                previewElement.style.display = 'block';
                noImageText.style.display = 'none';
                removeBtn.style.display = 'block';
            }
            
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Eliminar imagen
    removeBtn.addEventListener('click', function() {
        resetImage();
    });

    // Limpiar formulario
    resetBtn.addEventListener('click', function() {
        resetImage();
    });

    // Función para resetear la imagen
    function resetImage() {
        fileInput.value = '';
        previewElement.src = '';
        previewElement.style.display = 'none';
        noImageText.style.display = 'block';
        removeBtn.style.display = 'none';
    }

    // Validación del formulario
    document.getElementById('registroVisitanteForm').addEventListener('submit', function(e) {
        // Validaciones adicionales pueden ir aquí
        console.log('Formulario enviado');
    });
</script>

<!--script src="public/js/validarCedulaUnica.js"></script-->

<!--script src="public/js/validarFormulariosFocus.js"></script-->

<script>


document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroVisitanteForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // 1. Selección de campos
    const fields = {
        cedula: form.querySelector('input[name="cedulaVisitante"]'),
        nombre: form.querySelector('input[name="nombreVisitante"]'),
        prefijo: form.querySelector('select[name="prefijoVisitante"]'),
        telefono: form.querySelector('input[name="numeroVisitante"]'),
        genero: form.querySelector('select[name="sexoVisitante"]'),
        email: form.querySelector('input[name="correoVisitante"]'),
        direccion: form.querySelector('input[name="dirVisitante"]')
    };

    // 2. Validadores
    const validators = {
        cedula: {
            regex: /^\d{8}$/,
            error: 'La cédula debe tener 8 dígitos',
            success: '✓ Válido'
        },
        nombre: {
            regex: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/,
            error: 'Solo letras y espacios',
            success: '✓ Válido'
        },
        telefono: {
            regex: /^\d{7}$/,
            error: 'El teléfono debe tener 7 dígitos',
            success: '✓ Formato válido'
        },
        email: {
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            error: 'Email no válido',
            success: '✓ Formato válido'
        },
        select: {
            error: 'Seleccione una opción',
            //success: '✓ Válido'
        }
    };

    // 3. Función crearSpan mejorada (solo para el campo activo)
    function showMessage(input, message, color = '') {
        // Eliminar mensajes anteriores solo en este campo
        const existingSpans = input.parentNode.querySelectorAll('.span-message');
        existingSpans.forEach(span => span.remove());
        
        if (message) {
            const span = document.createElement('span');
            span.className = 'span-message';
            span.textContent = message;
            span.style.color = color;
            span.style.display = 'block';
            span.style.marginTop = '5px';
            span.style.fontSize = '0.85rem';
            input.insertAdjacentElement('afterend', span);
        }
    }

    // 4. Validación por campo (solo muestra mensaje en el campo actual)
    async function validateField(field, key) {
        const value = field.value.trim();
        let isValid = true;
        
        // Validación básica de campo requerido
        if (!value) {
            showMessage(field, 'Este campo es obligatorio', 'red');
            return false;
        }

        // Validación para selects
        if (field.tagName === 'SELECT') {
            isValid = field.value !== '';
            showMessage(field, isValid ? validators.select.success : validators.select.error, isValid ? 'green' : 'red');
            return isValid;
        }

        // Validación específica por tipo de campo
        if (validators[key]) {
            isValid = validators[key].regex.test(value);
            showMessage(field, isValid ? validators[key].success : validators[key].error, isValid ? 'green' : 'red');
            
            // Validación AJAX adicional para cédula
            if (key === 'cedula' && isValid) {
                try {
                    const response = await fetch("public/js/ajax/validarCamposUnicos.php", {
                        method: "POST",
                        headers: {"Content-type": "application/json"},
                        body: JSON.stringify({validarCedulaVisitante: value})
                    });
                    const data = await response.json();
                    
                    if (data.message) {
                        showMessage(field, data.message, data.class || 'red');
                        return data.class !== "red";
                    }
                } catch (error) {
                    console.error("Error validando cédula:", error);
                    showMessage(field, "Error de conexión", "red");
                    return false;
                }
            }
        }

        return isValid;
    }

    // 5. Actualizar estado del formulario (sin mostrar mensajes en otros campos)
    async function updateFormState() {
        let allValid = true;
        const currentField = document.activeElement;
        
        // Solo validar el campo actualmente enfocado
        if (currentField && Object.values(fields).includes(currentField)) {
            const fieldKey = Object.keys(fields).find(key => fields[key] === currentField);
            allValid = await validateField(currentField, fieldKey);
        }
        
        // Verificar silenciosamente los otros campos (sin mostrar mensajes)
        for (const [key, field] of Object.entries(fields)) {
            if (field !== currentField) {
                const value = field.value.trim();
                if (!value) {
                    allValid = false;
                    continue;
                }
                
                if (field.tagName === 'SELECT' && field.value === '') {
                    allValid = false;
                    continue;
                }
                
                if (validators[key] && !validators[key].regex.test(value)) {
                    allValid = false;
                }
            }
        }
        
        submitBtn.disabled = !allValid;
    }

    // 6. Configurar eventos
    Object.values(fields).forEach(field => {
        field.addEventListener('input', updateFormState);
        field.addEventListener('change', updateFormState);
        field.addEventListener('blur', updateFormState);
        
        // Formateo automático para cédula y teléfono
        if (field === fields.cedula || field === fields.telefono) {
            field.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
        }
    });

    // 7. Validación inicial
    submitBtn.disabled = true;
});

</script>