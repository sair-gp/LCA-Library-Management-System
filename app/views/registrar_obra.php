<?php
include "app/config/database.php";
$conexion = conexion();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Obras - ISBD</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Reset y estilos base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        
        /* Barra de progreso HORIZONTAL CORREGIDA */
        .progress-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        
        .progress-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
            transform: translateY(-50%);
        }
        
        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            font-weight: bold;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin: 0 10px;
        }
        
        .progress-step.active {
            background: #3498db;
            color: white;
        }
        
        .progress-step.completed {
            background: #2ecc71;
            color: white;
        }

        /* Contenedor para asegurar que los pasos estén en línea */
        .progress-steps-wrapper {
            display: flex;
            width: 100%;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }

        /* Pasos del formulario */
        .step {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .step.active {
            display: block;
        }
        
        .step-title {
            color: #2c3e50;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
            font-weight: 500;
        }
        
        /* Grupos de formulario */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-help {
            display: block;
            color: #7f8c8d;
            font-size: 0.85em;
            margin-bottom: 8px;
            font-style: italic;
        }
        
        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        /* Select2 personalizado */
        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            height: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        
        /* Campos condicionales */
        .conditional-fields {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }
        
        /* Previsualización de imágenes */
        .image-preview {
            margin-top: 15px;
        }
        
        .preview-img {
            max-width: 150px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
        }
        
        /* Botones */
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 16px;
        }
        
        .btn-prev {
            background: #95a5a6;
            color: white;
        }
        
        .btn-prev:hover {
            background: #7f8c8d;
        }
        
        .btn-next {
            background: #3498db;
            color: white;
        }
        
        .btn-next:hover {
            background: #2980b9;
        }
        
        .btn-submit {
            background: #2ecc71;
            color: white;
        }
        
        .btn-submit:hover {
            background: #27ae60;
        }
        
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Diseño responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .progress-step {
                width: 30px;
                height: 30px;
                font-size: 14px;
                margin: 0 5px;
            }
            
            .btn {
                padding: 10px 15px;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Mensajes de error */
        .error-message {
            color: #e74c3c;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }
        
        .is-invalid {
            border-color: #e74c3c !important;
        }
        
        /* Campos de duración */
        .duration-fields {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .duration-fields select {
            flex: 1;
        }
        
        /* Grupo de extensión */
        .extension-group {
            display: flex;
            gap: 10px;
        }
        
        .extension-group input {
            flex: 2;
        }
        
        .extension-group select {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro de Obras - ISBD</h1>
        
        <!-- Barra de progreso horizontal corregida -->
        <div class="progress-container">
            <div class="progress-steps-wrapper">
                <?php for($i=1; $i<=8; $i++): ?>
                    <div class="progress-step <?= $i==1 ? 'active' : '' ?>" data-step="<?= $i ?>"><?= $i ?></div>
                <?php endfor; ?>
            </div>
        </div>
        
        <form id="registrationForm" action="app/controller/libros/c_registrar_libro.php" method="POST" enctype="multipart/form-data">
            
            <!-- [Resto del código del formulario permanece igual] -->
            <!-- Paso 1: Área 0 -->
            <div class="step active" data-step="1">
                <h2 class="step-title">Área 0 - Forma del contenido y tipo de medio</h2>
                
                <div class="form-group">
                    <label for="forma_contenido">Forma del contenido:</label>
                    <span class="form-help">Ej: texto, imágenes, audio</span>
                    <input type="text" id="forma_contenido" name="forma_contenido" required>
                </div>
                
                <div class="form-group">
                    <label for="calificacion_contenido">Calificación del contenido:</label>
                    <span class="form-help">Ej: informativo, educativo</span>
                    <input type="text" id="calificacion_contenido" name="calificacion_contenido" required>
                </div>
                
                <div class="form-group">
                    <label for="categoriaSelect">Categoría:</label>
                    <select id="categoriaSelect" name="categoria[]" multiple required></select>
                </div>
                
                <div class="form-group">
                    <label for="tipo_de_medio">Tipo de medio:</label>
                    <select id="tipo_de_medio" name="tipo_de_medio" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <?php
                        $query = $conexion->query("SELECT id, nombre FROM tipo_medio");
                        while($row = $query->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-next" onclick="nextStep(1)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 2: Área 1 -->
            <div class="step" data-step="2">
                <h2 class="step-title">Área 1 - Título y mención de responsabilidad</h2>
                
                <div class="form-group">
                    <label for="titulo">Título:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="titulo_paralelo">Título paralelo:</label>
                    <input type="text" id="titulo_paralelo" name="titulo_paralelo">
                </div>
                
                <div class="form-group">
                    <label for="autorSelect">Autores:</label>
                    <select id="autorSelect" name="autor[]" multiple required></select>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(2)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 3: Área 2 -->
            <div class="step" data-step="3">
                <h2 class="step-title">Área 2 - Edición</h2>
                
                <div class="form-group">
                    <label for="mencion_edicion">Mención de edición:</label>
                    <input type="text" id="mencion_edicion" name="mencion_edicion" required>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(3)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(3)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 4: Área 3 -->
            <div class="step" data-step="4">
                <h2 class="step-title">Área 3 - Publicación, producción, distribución</h2>
                
                <div class="form-group">
                    <label for="editorialSelect">Editorial:</label>
                    <select id="editorialSelect" name="editorial" required></select>
                </div>
                
                <div class="form-group">
                    <label for="lugar_publicacion">Lugar de publicación:</label>
                    <input type="text" id="lugar_publicacion" name="lugar_publicacion">
                    <input type="hidden" id="checkLugarPublicacion" name="checkLugarPublicacion">
                </div>
                
                <div class="form-group">
                    <label for="fecha_publicacion">Año de publicación:</label>
                    <input type="text" id="fecha_publicacion" name="fecha_publicacion" maxlength="4" required>
                    <div class="error-message" id="yearError"></div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(4)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(4)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 5: Área 4 -->
            <div class="step" data-step="5">
                <h2 class="step-title">Área 4 - Descripción física</h2>
                
                <div class="form-group">
                    <label>Extensión:</label>
                    <div class="extension-group">
                        <input type="text" id="extensionInput" name="extension" required>
                        <select id="extensionType" onchange="toggleDurationFields()">
                            <option value="pages">Páginas</option>
                            <option value="duration">Duración</option>
                        </select>
                    </div>
                    <div class="conditional-fields" id="durationFields" style="display: none;">
                        <div class="duration-fields">
                            <select id="durationHours">
                                <?php for($i=0; $i<=6; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> hora<?= $i!=1?'s':'' ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="durationMinutes">
                                <option value="0">0 minutos</option>
                                <option value="15">15 minutos</option>
                                <option value="30">30 minutos</option>
                                <option value="45">45 minutos</option>
                            </select>
                        </div>
                        <input type="hidden" id="extensionDuration" name="extension_duration">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="detalles_fisicos">Detalles físicos:</label>
                    <input type="text" id="detalles_fisicos" name="detalles_fisicos">
                </div>
                
                <div class="form-group">
                    <label for="dimensiones">Dimensiones:</label>
                    <input type="text" id="dimensiones" name="dimensiones">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(5)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(5)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 6: Área 5 -->
            <div class="step" data-step="6">
                <h2 class="step-title">Área 5 - Serie</h2>
                <span class="form-help">Opcional: Complete solo si pertenece a una serie</span>
                
                <div class="form-group">
                    <label for="tituloSerie">Título de la serie:</label>
                    <input type="text" id="tituloSerie" name="titulo_serie" oninput="toggleSerieFields()">
                </div>
                
                <div class="conditional-fields" id="serieFields" style="display: none;">
                    <div class="form-group">
                        <label for="numeracion_serie">Numeración:</label>
                        <input type="text" id="numeracion_serie" name="numeracion_serie">
                    </div>
                    
                    <div class="form-group">
                        <label for="isbnSerie">ISBN del volumen:</label>
                        <input type="text" id="isbnSerie" name="isbn_serie">
                        <div class="error-message" id="isbnSerieError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="portada_volumen">Portada del volumen:</label>
                        <input type="file" id="portada_volumen" name="portada_volumen" onchange="previewImage(event, 'previewVolumen')">
                        <div class="image-preview">
                            <img id="previewVolumen" src="public/img/libros/default.jpg" class="preview-img">
                        </div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(6)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(6)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 7: Área 6 -->
            <div class="step" data-step="7">
                <h2 class="step-title">Área 6 - Notas</h2>
                
                <div class="form-group">
                    <label for="notas">Notas:</label>
                    <textarea id="notas" name="notas" rows="4"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(7)">Anterior</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(7)">Siguiente</button>
                </div>
            </div>
            
            <!-- Paso 8: Área 7 -->
            <div class="step" data-step="8">
                <h2 class="step-title">Área 7 - Identificación del recurso</h2>
                
                <div class="form-group">
                    <label for="isbnInput">ISBN/ISSN:</label>
                    <input type="text" id="isbnInput" name="isbn">
                    <div class="error-message" id="isbnError"></div>
                </div>
                
                <div class="form-group">
                    <label for="cota">Clasificación Decimal Dewey:</label>
                    <input type="text" id="cota" name="cota">
                </div>
                
                <div class="form-group">
                    <label for="portada">Portada principal:</label>
                    <input type="file" id="portada" name="portada" onchange="previewImage(event, 'previewMain')">
                    <div class="image-preview">
                        <img id="previewMain" src="public/img/libros/default.jpg" class="preview-img">
                    </div>
                </div>
                
                <fieldset>
                    <legend>Gestionar ejemplares</legend>
                    
                    <div class="form-group">
                        <label for="estanteriaSelect">Estantería:</label>
                        <select id="estanteriaSelect" required></select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filaSelect">Fila:</label>
                        <select id="filaSelect" disabled required></select>
                        <div id="capacityInfo" class="form-help" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cantidadEjemplares">Cantidad de ejemplares:</label>
                        <input type="text" id="cantidadEjemplares" disabled required>
                        <div class="error-message" id="cantidadError"></div>
                    </div>
                </fieldset>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(8)">Anterior</button>
                    <button type="submit" class="btn btn-submit" id="submitBtn" disabled>Registrar Libro</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // [El código JavaScript permanece igual que en la versión anterior]
        // Variables globales
        let currentStep = 1;
        const totalSteps = 8;
        
        // Inicialización
        $(document).ready(function() {
            // Configurar Select2
            $('#autorSelect, #categoriaSelect, #editorialSelect, #estanteriaSelect, #filaSelect').select2({
                tags: true,
                ajax: {
                    url: 'public/js/ajax/cargarDatosSelect2.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term,
                            type: $(this).attr('id').replace('Select', '')
                        };
                    },
                    processResults: function(data) {
                        return { results: data };
                    }
                }
            });
            
            // Configurar eventos
            setupEventListeners();
            
            // Configurar editorial para actualizar lugar de publicación
            $('#editorialSelect').on('select2:select', function(e) {
                const lugarPublicacion = $('#lugarPublicacion');
                if(e.params.data.origen) {
                    lugarPublicacion.val(e.params.data.origen).prop('readonly', true);
                    $('#checkLugarPublicacion').val(1);
                } else {
                    lugarPublicacion.val('').prop('readonly', false);
                    $('#checkLugarPublicacion').val(0);
                }
            });
        });
        
        function setupEventListeners() {
            // Validación en tiempo real
            $('input, select, textarea').on('input change', validateForm);
            
            // Validación de ISBN
            $('#isbnInput, #isbnSerie').on('input', function() {
                validateISBN($(this));
                validateForm();
            });
            
            // Gestión de ejemplares
            $('#estanteriaSelect').on('change', function() {
                loadShelves($(this).val());
                validateForm();
            });
            
            $('#filaSelect').on('change', function() {
                updateCapacityInfo();
                validateForm();
            });
            
            $('#cantidadEjemplares').on('input', function() {
                validateQuantity();
                validateForm();
            });
            
            // Validación de año
            $('#fecha_publicacion').on('input', function() {
                validateYear($(this));
                validateForm();
            });
        }
        
        // Navegación por pasos
        function nextStep(step) {
            if(validateStep(step)) {
                $('.step[data-step="'+step+'"]').removeClass('active');
                $('.progress-step[data-step="'+step+'"]').removeClass('active').addClass('completed');
                
                const nextStep = step + 1;
                $('.step[data-step="'+nextStep+'"]').addClass('active');
                $('.progress-step[data-step="'+nextStep+'"]').addClass('active');
                
                currentStep = nextStep;
            }
        }
        
        function prevStep(step) {
            $('.step[data-step="'+step+'"]').removeClass('active');
            $('.progress-step[data-step="'+step+'"]').removeClass('active');
            
            const prevStep = step - 1;
            $('.step[data-step="'+prevStep+'"]').addClass('active');
            $('.progress-step[data-step="'+prevStep+'"]').addClass('active').removeClass('completed');
            
            currentStep = prevStep;
        }
        
        // Validación de pasos
        function validateStep(step) {
            let isValid = true;
            $(`.step[data-step="${step}"] [required]`).each(function() {
                if(!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if(!isValid) {
                alert('Por favor complete todos los campos obligatorios');
            }
            
            return isValid;
        }
        
        // Validación general del formulario
        function validateForm() {
            let isValid = true;
            
            // Validar campos requeridos
            $('[required]').each(function() {
                if(!$(this).val() && $(this).is(':visible')) {
                    isValid = false;
                    return false; // Salir del bucle temprano
                }
            });
            
            // Validar ISBNs
            if(!validateISBN($('#isbnInput'))) isValid = false;
            if($('#tituloSerie').val() && !validateISBN($('#isbnSerie'))) isValid = false;
            
            // Validar cantidad de ejemplares
            if(!validateQuantity()) isValid = false;
            
            // Validar año
            if(!validateYear($('#fecha_publicacion'))) isValid = false;
            
            // Habilitar/deshabilitar botón de envío
            $('#submitBtn').prop('disabled', !isValid);
            
            return isValid;
        }
        
        // Validación de ISBN (según tu implementación original)
        function validateISBN(input) {
            const isbn = input.val().replace(/[^0-9X]/gi, '');
            const isbnError = input.attr('id') === 'isbnInput' ? $('#isbnError') : $('#isbnSerieError');
            
            if(!isbn) {
                if(input.attr('required')) {
                    isbnError.text('Este campo es obligatorio').show();
                    return false;
                }
                isbnError.hide();
                return true;
            }
            
            if(isbn.length === 10) {
                if(validateISBN10(isbn)) {
                    checkISBNExists(isbn, input);
                    return true;
                }
            } else if(isbn.length === 13) {
                if(validateISBN13(isbn)) {
                    checkISBNExists(isbn, input);
                    return true;
                }
            }
            
            isbnError.text('ISBN inválido').show();
            return false;
        }
        
        function validateISBN10(isbn) {
            let sum = 0;
            for(let i = 0; i < 9; i++) {
                sum += parseInt(isbn[i]) * (10 - i);
            }
            
            const checkDigit = isbn[9].toUpperCase();
            const calculatedDigit = (11 - (sum % 11)) % 11;
            const calculatedCheck = calculatedDigit === 10 ? 'X' : calculatedDigit.toString();
            
            return calculatedCheck === checkDigit;
        }
        
        function validateISBN13(isbn) {
            let sum = 0;
            for(let i = 0; i < 12; i++) {
                sum += parseInt(isbn[i]) * (i % 2 === 0 ? 1 : 3);
            }
            
            const checkDigit = parseInt(isbn[12]);
            const calculatedDigit = (10 - (sum % 10)) % 10;
            
            return calculatedDigit === checkDigit;
        }
        
        async function checkISBNExists(isbn, input) {
            try {
                const response = await fetch('public/js/ajax/validarInputsRegistroLibro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ isbn: isbn })
                });
                
                const data = await response.json();
                const isbnError = input.attr('id') === 'isbnInput' ? $('#isbnError') : $('#isbnSerieError');
                
                if(data.result) {
                    isbnError.text('Este ISBN ya existe').show();
                    return false;
                } else {
                    isbnError.text('ISBN válido').css('color', 'green').show();
                    return true;
                }
            } catch(error) {
                console.error('Error:', error);
                return false;
            }
        }
        
        // Gestión de ejemplares
        function loadShelves(estanteriaId) {
            if(!estanteriaId) {
                $('#filaSelect').empty().prop('disabled', true);
                return;
            }
            
            $.ajax({
                url: 'public/js/ajax/obtenerEstanteriaYFila.php',
                data: { estanteria: estanteriaId },
                success: function(data) {
                    $('#filaSelect').empty().append('<option value="">Seleccione fila</option>');
                    
                    data.forEach(item => {
                        const disabled = item.disponible <= 0 ? 'disabled' : '';
                        $('#filaSelect').append(
                            `<option value="${item.id}" ${disabled} data-capacity="${item.disponible}">
                                ${item.text}
                            </option>`
                        );
                    });
                    
                    $('#filaSelect').prop('disabled', false);
                }
            });
        }
        
        function updateCapacityInfo() {
            const selectedOption = $('#filaSelect option:selected');
            const capacity = selectedOption.data('capacity') || 0;
            
            if(capacity > 0) {
                $('#capacityInfo').text(`Espacios disponibles: ${capacity}`).show();
                $('#cantidadEjemplares').prop('disabled', false);
            } else {
                $('#capacityInfo').hide();
                $('#cantidadEjemplares').prop('disabled', true).val('');
            }
        }
        
        function validateQuantity() {
            const cantidad = parseInt($('#cantidadEjemplares').val()) || 0;
            const capacity = parseInt($('#filaSelect option:selected').data('capacity')) || 0;
            
            if(cantidad <= 0) {
                $('#cantidadError').text('Ingrese una cantidad válida').show();
                return false;
            }
            
            if(cantidad > capacity) {
                $('#cantidadError').text(`Excede la capacidad (${capacity} disponibles)`).show();
                return false;
            }
            
            $('#cantidadError').hide();
            return true;
        }
        
        // Validación de año
        function validateYear(input) {
            const year = input.val();
            const currentYear = new Date().getFullYear();
            
            if(!year) {
                $('#yearError').text('Este campo es obligatorio').show();
                return false;
            }
            
            if(isNaN(year) || year.length !== 4 || year > currentYear) {
                $('#yearError').text('Año inválido').show();
                return false;
            }
            
            $('#yearError').hide();
            return true;
        }
        
        // Campos condicionales
        function toggleSerieFields() {
            if($('#tituloSerie').val()) {
                $('#serieFields').show();
                $('#isbnSerie').attr('required', true);
            } else {
                $('#serieFields').hide();
                $('#isbnSerie').removeAttr('required');
            }
            validateForm();
        }
        
        function toggleDurationFields() {
            if($('#extensionType').val() === 'duration') {
                $('#durationFields').show();
                $('#extensionInput').attr('maxlength', 2).attr('pattern', '[0-5]?[0-9]');
            } else {
                $('#durationFields').hide();
                $('#extensionInput').removeAttr('maxlength').removeAttr('pattern');
            }
        }
        
        // Previsualización de imágenes
        function previewImage(event, targetId) {
            const reader = new FileReader();
            reader.onload = function() {
                $('#' + targetId).attr('src', reader.result);
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>