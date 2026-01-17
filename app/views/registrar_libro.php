<?php

include "app/config/database.php";
$conexion = conexion();

?>


<title>Registro de Obras - ISBD</title>
<link rel="stylesheet" href="public/css/registrarLibro.css">
<br>
<div class="container">
    <h1>Registro de Obras - (ISBD)</h1>
    <form id="formulario" class="formulario" action="app/controller/libros/c_registrar_libro.php" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Área 0 - Forma del contenido y tipo de medio</legend>
            <label>Forma del contenido:</label>
            <small>Describe la forma en que se presenta el contenido del material (ej. texto, imágenes, audio).</small>
            <select name="forma_contenido" class="form-select" required>
                <option value="" selected disabled>Seleccione una opción</option>
                <option value="texto">Texto</option>
                <option value="texto cartográfico">Texto cartográfico</option>
                <option value="texto notado">Texto notado (música)</option>
                <option value="texto sonoro">Texto sonoro (audiolibros)</option>
                <option value="texto táctil">Texto táctil (braille)</option>
                <option value="imagen fija">Imagen fija</option>
                <option value="imagen en movimiento">Imagen en movimiento</option>
                <option value="objeto tridimensional">Objeto tridimensional</option>
                <option value="datos computacionales">Datos computacionales</option>
                <option value="programa">Programa (software)</option>
                <option value="multimedia">Multimedia</option>
                <option value="sitio web">Sitio web</option>
                <option value="colección mixta">Colección mixta</option>
            </select>                 
            <!--input type="text" name="forma_contenido" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios (sin números)" oninput="this.value = this.value.replace(/[0-9]/g, '')" required-->

            <!--label>Calificación del contenido:</label>
            <small>Indica si el contenido es informativo, educativo, recreativo, etc.</small>
            <input type="text" name="calificacion_contenido" pattern="[A-Za-z\s]+" title="Solo se permiten letras y espacios (sin números)" oninput="this.value = this.value.replace(/[0-9]/g, '')" required-->

            <label>Categoría del contenido:</label>
            <small>Indica a qué categoría pertenece el contenido (ej. ciencias, ficción, literatura, etc).</small>
            <select name="categoria[]" class="form-select" id="categoriaRegistro" required>
                <!-- Categorías dinámicas -->
            </select>

            <label>Tipo de medio:</label>
            <small>Ejemplo: impreso, digital, en línea, audiovisual.</small>
            <select name="tipo_de_medio" class="form-select" id="tipoDeMedio">
                <option value="" selected disabled>Seleccione un tipo de medio.</option>
                <?php
                $query = $conexion->prepare("SELECT id, nombre FROM tipo_medio");
                $query->execute();
                $query->bind_result($id, $nombre);

                $opciones = "";

                while ($query->fetch()) {
                    $opciones .= "<option value='$id'>$nombre</option>";
                }

                $query->close();

                echo $opciones;
                ?>
            </select>
        </fieldset>

        <fieldset>
            <legend>Área 1 - Título y mención de responsabilidad</legend>
            <label>Título:</label>
            <small>Ingrese el título principal del libro tal como aparece en la portada.</small>
            <input type="text" name="titulo" pattern="[A-Za-z0-9\u00C0-\u024F\s]+" title="Solo se permiten letras, números y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F\s]/g, '')" required>

            <label>Título paralelo:</label>
            <small>Si el libro tiene un título en otro idioma, ingréselo aquí.</small>
            <input type="text" name="titulo_paralelo" pattern="[A-Za-z0-9\u00C0-\u024F\s]+" title="Solo se permiten letras, números y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F\s]/g, '')">

            <label>Mención de responsabilidad:</label>
            <small>Indique los autores o editores responsables de la obra.</small>
            <select name="autor[]" class="form-select" id="autorRegistro" required>
                <!-- Autores dinámicos -->
            </select>
        </fieldset>

        <fieldset>
            <legend>Área 2 - Edición</legend>
            <label>Mención de edición:</label>
            <small>Especifique si es la primera edición, segunda, revisada, ampliada, etc.</small>
            <input type="text" name="mencion_edicion" required>
        </fieldset>

        <!--fieldset>
        <legend>Área 3 - Material o tipo de recurso específico</legend>
        <label>Tipo de recurso específico:</label>
        <small>Ejemplo: mapa, manuscrito, partitura, base de datos, etc.</small>
        <input type="text" name="tipo_recurso">
        </fieldset-->


        <fieldset>
            <legend>Área 3 - Publicación, producción, distribución</legend>


            <label>Nombre del editor:</label>
            <small>Nombre de la editorial o entidad responsable de la publicación.</small>
            <select name="editorial" class="form-select" id="editorialRegistro" required>
                <!-- Editoriales dinámicas -->
            </select>

            <label>Lugar de publicación:</label>
            <small>Ciudad y país donde se publicó el libro.</small>
            <input type="text" name="lugar_publicacion" id="lugarPublicacion">
            <input type="hidden" id="checkLugarPublicacion" name="checkLugarPublicacion">


            <label>Año de publicación:</label>
            <small>Indique el año de publicación del libro.</small>
            <input type="text" name="fecha_publicacion" id="fecha_publicacion" maxlength="4" pattern="[0-9]+" title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, ''); validarAnio()" required>
            <p class="error-message" style="display: none;">Por favor, ingrese solo números.</p>
        </fieldset>

        <fieldset>
            <legend>Área 4 - Descripción física</legend>
            <label>Extensión:</label>
            <small>Número total de páginas o duración en caso de material audiovisual.</small>
            <input type="text" name="extension" pattern="[A-Za-z0-9\u00C0-\u024F\s]+" title="Solo se permiten letras, números y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F\s]/g, '')" required>

            <label>Otros detalles físicos:</label>
            <small>Ejemplo: ilustraciones, gráficos, tablas, fotografías, etc.</small>
            <input type="text" name="detalles_fisicos" pattern="[A-Za-z0-9\u00C0-\u024F\s]+" title="Solo se permiten letras, números y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F\s]/g, '')">

            <label>Dimensiones:</label>
            <small>Especifique el tamaño físico del material (ej. 21 cm x 15 cm).</small>
            <input type="text" name="dimensiones" pattern="[A-Za-z0-9\u00C0-\u024F.,\s]+" title="Solo se permiten letras, números, puntos, comas y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F.,\s]/g, '')">
        </fieldset>

        <fieldset>
        <legend>Área 5 - Serie</legend>
    <small>De no ser una serie, deje estos campos vacíos.</small>
    <label for="titulo_serie">Título de la serie:</label>
    <small>Si el libro forma parte de una serie, ingrese el título de la serie aquí, de lo contrario, dejar el campo vacío.</small>
    <input type="text" name="titulo_serie" id="titulo_serie" pattern="[A-Za-z0-9\u00C0-\u024F\s]+" title="Solo se permiten letras, números y tildes" oninput="this.value = this.value.replace(/[^A-Za-z0-9\u00C0-\u024F\s]/g, '')">

    <div class="hidden-content" id="extraFields">
        <label>Numeración dentro de la serie:</label>
        <small>Especifique el número del libro dentro de la serie (ej. Volumen/Tomo 3).</small>
        <input type="text" name="numeracion_serie" pattern="[0-9]+" title="Solo se permiten números" oninput="this.value = this.value.replace(/\D/g, '')">

        <label>ISBN del volumen y/o tomo dentro de la serie:</label>
        <small>Especifique el ISBN del volumen y/o tomo específico.</small>
        <input type="text" class="isbnRegistro" name="isbn_serie">

        <label for="portada">Portada</label>
        <small>Portada del volumen/tomo.</small>
        <input type="file" id="portada_volumen" name="portada_volumen" accept="image/*" onchange="previewImage(event, 'previewSerie')">
        <img id="previewSerie" src="public/img/libros/default.jpg" alt="Portada" class="preview-img">
    </div>

            <!--label>Numeración dentro de la serie:</label>
                <small>Especifique el número del libro dentro de la serie (ej. Volumen/Tomo 3).</small>
                <input type="text" name="numeracion_serie">

            <label>ISBN del volumen y/o tomo dentro de la serie:</label>
            <small>Especifique el ISBN del volumen y/o tomo específico.</small>
            <input type="text" name="isbn_serie">

            <label for="portada">Portada</label>
            <input type="file" id="portada" name="portada" accept="image/*" onchange="previewImage(event, 'preview')">
            <img id="preview" src="public/img/libros/default.jpg" alt="Portada" class="preview-img"-->
        </fieldset>

        <fieldset>
            <legend>Área 6 - Notas</legend>
            <label>Notas:</label>
            <small>Ingrese cualquier información adicional relevante sobre el libro.</small>
            <textarea name="notas" rows="4"></textarea>
        </fieldset>

        <fieldset>
            <legend>Área 7 - Identificación del recurso</legend>
            <label>ISBN/ISSN:</label>
            <small>Ingrese el número ISBN (para libros) o ISSN (para publicaciones seriadas).</small>
            <input type="text" class="isbnRegistro" name="isbn">

            <label>Clasificación Decimal Dewey (DDC):</label>
            <small>Ingrese el número de clasificación Decimal Dewey (DDC o Cota).</small>
            <small id="sugerirCota" style="display: none;">Cota sugerida:</small>
            <input type="text" name="cota" pattern="[0-9.]*" title="Solo se permiten números y puntos" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">

            <!--label>Condiciones de disponibilidad:</label>
            <small>Especifique si es de acceso libre, restringido, venta, etc.</small>
            <input type="text" name="condiciones_disponibilidad"-->


            <label for="portada">Portada</label>
            <input type="file" id="portada" name="portada" accept="image/*" onchange="previewImage(event, 'preview')">
            <img id="preview" src="public/img/libros/default.jpg" alt="Portada" class="preview-img">

        </fieldset>


        <fieldset class="gestion-ejemplares-container" legend="Gestionar ejemplares:">
    <legend>Gestionar ejemplares:</legend>
    
    <div class="gestion-ejemplares-row">
        <div class="gestion-ejemplares-col">
            <label for="estanteria">Estantería:</label>
            <small>Indique la estantería en la que se almacenará el ejemplar.</small>
            <div class="gestion-ejemplares-select2">
                <select id="estanteria" name="estanteria" required>
                    <option value="" selected>Seleccione la estantería.</option>
                </select>
            </div>
        </div>
        
        <div class="gestion-ejemplares-col">
            <label for="filaEstanteria">Fila:</label>
            <small>Indique la fila en la que se almacenará el ejemplar.</small>
            <div class="gestion-ejemplares-select2">
                <select id="filaEstanteria" name="fila" disabled required>
                    <option value="" selected>Primero seleccione una estantería.</option>
                </select>
            </div>
            <div class="gestion-ejemplares-info" id="filaInfoContainer" style="display: none;">
                <span id="espaciosDisponibles"></span>
            </div>
        </div>
    </div>
    
    <div class="gestion-ejemplares-row">
        <div class="gestion-ejemplares-col">
            <label for="cantidadEjemplares">Cantidad de ejemplares:</label>
            <input type="text" class="form-control gestion-ejemplares-cantidad" 
                   id="cantidadEjemplares" name="cantidad" disabled required>
            <div class="gestion-ejemplares-error" id="errorCantidad"></div>
        </div>
    </div>
    </fieldset>

        <button type="submit" id="submitBtnRegConIsbn" class="registrarLibroBtn" onclick="return validarFormulario()">Registrar Libro</button>
    </form>
</div>

<script>
    function previewImage(event, targetId) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById(targetId);
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let espaciosDisponibles = 0;
    const $cantidadInput = $('#cantidadEjemplares');
    const $errorMsg = $('#errorCantidad');
    const submitBtn = document.getElementById('submitBtnRegConIsbn');
    const formulario = document.getElementById('formulario');
    
    // Deshabilitar botón inicialmente
    submitBtn.disabled = true;
    
    // Función para validar todo el formulario
    function validarFormularioCompleto() {
        const camposRequeridos = formulario.querySelectorAll('[required]');
        let formularioValido = true;
        
        // Validar campos requeridos
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                formularioValido = false;
                return;
            }
            
            // Validaciones específicas
            if (campo.name === 'fecha_publicacion' && !validarAnio()) {
                formularioValido = false;
                return;
            }
            
            if (campo.name === 'cantidad' && !validarCantidad()) {
                formularioValido = false;
                return;
            }
            
            if (campo.name === 'cota' && !validarCota(campo.value)) {
                formularioValido = false;
                return;
            }
        });
        
        // Validar selects múltiples
        if ($('#autorRegistro').select2('data').length === 0 || 
            $('#categoriaRegistro').select2('data').length === 0) {
            formularioValido = false;
        }
        
        // Validar archivos (si se requieren)
        const portadaInput = document.querySelector('input[name="portada"]');
        if (portadaInput && !portadaInput.files[0]) {
            formularioValido = false;
        }
        
        // Validar campos de serie si se ingresó título de serie
        const tituloSerie = document.getElementById('titulo_serie').value.trim();
        if (tituloSerie) {
            const camposSerie = document.querySelectorAll('#extraFields input[required]');
            camposSerie.forEach(campo => {
                if (!campo.value.trim()) {
                    formularioValido = false;
                    return;
                }
            });
        }
        
        // Habilitar/deshabilitar botón
        submitBtn.disabled = !formularioValido;
        
        return formularioValido;
    }
    
    // Función para validar la cantidad de ejemplares
    function validarCantidad() {
        const value = $cantidadInput.val().replace(/[^0-9]/g, '');
        const cantidad = parseInt(value) || 0;
        
        if (cantidad <= 0) {
            $errorMsg.text('Ingrese un número válido').show();
            $cantidadInput.addClass('is-invalid');
            return false;
        }
        
        if (cantidad > espaciosDisponibles) {
            $errorMsg.text(`Solo hay ${espaciosDisponibles} espacios disponibles`).show();
            $cantidadInput.addClass('is-invalid');
            return false;
        }
        
        $errorMsg.hide();
        $cantidadInput.removeClass('is-invalid');
        return true;
    }
    
    // Función para validar el año
    function validarAnio() {
        const inputAnio = document.getElementById("fecha_publicacion");
        const anioIngresadoStr = inputAnio.value;
        const anioIngresado = parseInt(anioIngresadoStr);
        const anioActual = new Date().getFullYear();
        let spanMensaje = inputAnio.nextElementSibling;
        
        if (anioIngresadoStr.startsWith("0") && anioIngresadoStr.length > 1) {
            crearSpan(
                inputAnio,
                spanMensaje,
                "El año no puede empezar por cero.",
                "red"
            );
            return false;
        }
        
        if (isNaN(anioIngresado)) {
            crearSpan(inputAnio, spanMensaje, "Por favor, ingresa un año válido.", "red");
            return false;
        }
        
        if (anioIngresado > anioActual) {
            crearSpan(
                inputAnio,
                spanMensaje,
                `El año ingresado no puede ser mayor al año actual (${anioActual}).`,
                "red"
            );
            return false;
        }
        
        crearSpan(inputAnio, spanMensaje, "", "", true);
        return true;
    }
    
    // Función para validar la cota Dewey
    function validarCota(cota) {
        if (!cota) return false;
        return /^[0-9.]+$/.test(cota);
    }
    
    // Función auxiliar para mostrar mensajes
    function crearSpan(inputElement, spanElement, mensaje, color, limpiar = false) {
        if (limpiar) {
            if (spanElement && spanElement.tagName === 'SPAN') {
                spanElement.remove();
            }
            return;
        }
        
        if (!spanElement || spanElement.tagName !== 'SPAN') {
            spanElement = document.createElement('span');
            spanElement.style.color = color || 'red';
            spanElement.style.fontSize = '0.8em';
            spanElement.style.display = 'block';
            inputElement.parentNode.insertBefore(spanElement, inputElement.nextSibling);
        }
        
        spanElement.textContent = mensaje;
        spanElement.style.color = color || 'red';
    }
    
    // Configuración de Select2 para Estantería
    $('#estanteria').select2({
        ajax: {
            url: 'public/js/ajax/obtenerEstanteriaYFila.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { term: params.term };
            },
            processResults: function(data) {
                return { results: data };
            }
        },
        minimumInputLength: 0,
        width: '100%',
        placeholder: 'Seleccione la estantería'
    }).on('change', function() {
        const estanteriaId = $(this).val();
        const $filaSelect = $('#filaEstanteria');
        
        // Resetear estado
        $filaSelect.val('').prop('disabled', !estanteriaId).empty()
            .append('<option value="">Seleccione una fila...</option>');
        $cantidadInput.val('').prop('disabled', true);
        $('#filaInfoContainer').hide();
        espaciosDisponibles = 0;
        $errorMsg.hide();
        $cantidadInput.removeClass('is-invalid');
        
        if (estanteriaId) {
            cargarFilas(estanteriaId);
        }
        
        validarFormularioCompleto();
    });
    
    // Función para cargar filas
    function cargarFilas(estanteriaId) {
        $.ajax({
            url: 'public/js/ajax/obtenerEstanteriaYFila.php',
            dataType: 'json',
            data: { estanteria: estanteriaId },
            success: function(data) {
                const $filaSelect = $('#filaEstanteria');
                
                data.forEach(function(item) {
                    const disponible = item.disponible || 0;
                    const disabled = disponible <= 0 ? 'disabled' : '';
                    
                    $filaSelect.append(
                        `<option value="${item.id}" ${disabled} 
                          data-disponible="${disponible}">
                            ${item.text}
                        </option>`
                    );
                });
                
                $filaSelect.select2({
                    width: '100%',
                    minimumInputLength: 0
                }).prop('disabled', false);
            }
        });
    }
    
    // Evento al cambiar fila
    $('#filaEstanteria').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        espaciosDisponibles = parseInt(selectedOption.data('disponible')) || 0;
        const $infoContainer = $('#filaInfoContainer');
        
        if ($(this).val() && !selectedOption.prop('disabled')) {
            const match = selectedOption.text().match(/\d+\/(\d+)/);
            const capacidad = match ? match[1] : '?';
            
            $('#espaciosDisponibles').text(`${espaciosDisponibles}/${capacidad} espacios disponibles`);
            $infoContainer.show();
            $cantidadInput.prop('disabled', false);
            
            if ($cantidadInput.val()) {
                validarCantidad();
            }
        } else {
            $infoContainer.hide();
            $cantidadInput.prop('disabled', true).val('');
            $errorMsg.hide();
            $cantidadInput.removeClass('is-invalid');
        }
        
        validarFormularioCompleto();
    });
    
    // Configuración de Select2 para otros campos
    $("#autorRegistro").select2({
        tags: true,
        multiple: true,
        ajax: {
            url: 'public/js/ajax/cargarDatosSelect2.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { autor: params.term };
            },
            processResults: function(data) {
                return { results: data };
            }
        }
    }).on('change', validarFormularioCompleto);
    
    $("#editorialRegistro").select2({
        tags: true,
        ajax: {
            url: 'public/js/ajax/cargarDatosSelect2.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { editorial: params.term };
            },
            processResults: function(data) {
                return { results: data };
            }
        }
    }).on('select2:select', function(e) {
        if (typeof e.params.data.origen != 'undefined') {
            let lugarPublicacion = document.getElementById("lugarPublicacion");
            lugarPublicacion.value = e.params.data.origen;
            lugarPublicacion.setAttribute('readonly', '');
            lugarPublicacion.removeAttribute('required');
            document.getElementById("checkLugarPublicacion").value = 1;
        } else {
            let lugarPublicacion = document.getElementById("lugarPublicacion");
            lugarPublicacion.value = "";
            lugarPublicacion.removeAttribute("readonly");
            lugarPublicacion.setAttribute('required', '');
            document.getElementById("checkLugarPublicacion").value = 0;
        }
        validarFormularioCompleto();
    }).on('change', validarFormularioCompleto);
    
    $("#categoriaRegistro").select2({
        tags: true,
        multiple: true,
        ajax: {
            url: 'public/js/ajax/cargarDatosSelect2.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { categoria: params.term };
            },
            processResults: function(data) {
                var results = $.map(data, function(item) {
                    return {
                        id: item.id,
                        text: item.text,
                        codigo: item.codigo
                    };
                });
                return { results: results };
            }
        }
    }).on('change', function() {
        var selectedData = $(this).select2('data');
        if (selectedData && selectedData.length > 0 && selectedData[0].codigo) {
            var codigoDewey = selectedData[0].codigo;
            document.getElementById("sugerirCota").style.display = "block";
            $('input[name="cota"]').val(codigoDewey)
                                   .attr('placeholder', 'Sugerencia: ' + codigoDewey);
        }
        validarFormularioCompleto();
    });
    
    // Validación en tiempo real para campos de texto
    formulario.querySelectorAll('input[type="text"], textarea').forEach(input => {
        input.addEventListener('input', validarFormularioCompleto);
    });
    
    // Validación en tiempo real para cantidad
    $cantidadInput.on('input', function() {
        validarCantidad();
        validarFormularioCompleto();
    });
    
    // Validación para campos de archivo
    formulario.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', validarFormularioCompleto);
    });
    
    // Validación para campos de serie
    document.getElementById('titulo_serie').addEventListener('input', function() {
        let extraFields = document.getElementById('extraFields');
        if (this.value.trim() !== '') {
            extraFields.classList.add('show');
            // Agregar required a los campos de serie
            extraFields.querySelectorAll('input').forEach(input => {
                input.setAttribute('required', '');
            });
        } else {
            extraFields.classList.remove('show');
            // Quitar required de los campos de serie
            extraFields.querySelectorAll('input').forEach(input => {
                input.removeAttribute('required');
            });
        }
        validarFormularioCompleto();
    });
    
    // Validar el formulario al cargar la página
    validarFormularioCompleto();
});
</script>