<script>





function validarAnio() {
  const inputAnio = document.getElementById("fecha_publicacion"); // Reemplaza "anioInput" con el ID de tu input
  const anioIngresadoStr = inputAnio.value;
  const anioIngresado = parseInt(anioIngresadoStr);
  const anioActual = new Date().getFullYear();
  let spanMensaje = inputAnio.nextElementSibling; // Intenta obtener un span existente después del input

  if (anioIngresadoStr.startsWith("0") && anioIngresadoStr.length > 1) {
    crearSpan(
      inputAnio,
      spanMensaje,
      "El año no puede empezar por cero.",
      "red"
    );
    inputAnio.value = ""; // Limpiar el campo
    return false;
  }

  if (isNaN(anioIngresado)) {
    crearSpan(inputAnio, spanMensaje, "Por favor, ingresa un año válido.", "red");
    inputAnio.value = ""; // Limpiar el campo si no es un número
    return false;
  }

  if (anioIngresado > anioActual) {
    crearSpan(
      inputAnio,
      spanMensaje,
      "El año ingresado no puede ser mayor al año actual (" + anioActual + ").",
      "red"
    );
    inputAnio.value = ""; // Limpiar el campo si es mayor al actual
    return false;
  }

  // Si la validación es exitosa, elimina cualquier mensaje anterior
  crearSpan(inputAnio, spanMensaje, "", "", true);
  return true; // El año es válido
}

function soloNumeros(event) {
  const charCode = event.which ? event.which : event.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }
  return true;
}

    document.getElementById('titulo_serie').addEventListener('input', function() {
        let extraFields = document.getElementById('extraFields');
        if (this.value.trim() !== '') {
            extraFields.classList.add('show');
        } else {
            extraFields.classList.remove('show');
        }

        var efInputs = extraFields.getElementsByTagName('input');

        for (var i = 0; i < inputs.length; i++) {
        if (this.value.trim() !== '') {
            efInputs[i].setAttribute('required', '');
        } else {
            efInputs[i].removeAttribute('required');
        }
        }



    });

    $(document).ready(function () {
        
        $("#autorRegistro").select2({
            tags: true,
            multiple: true, // Esto habilita la selección múltiple
            ajax: {
                url: 'public/js/ajax/cargarDatosSelect2.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        autor: params.term
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $("#editorialRegistro").select2({
            tags: true,
            ajax: {
                url: 'public/js/ajax/cargarDatosSelect2.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        editorial: params.term
                    }
                },
                processResults: function (data) {

                    return {
                        results: data
                    };
                }
            }
        }).on('select2:select', function (e) {
            if (typeof e.params.data.origen != 'undefined') {
                let lugarPublicacion = document.getElementById("lugarPublicacion");
                lugarPublicacion.value = e.params.data.origen;
                //lugarPublicacion.disabled = true;
                lugarPublicacion.setAttribute('readonly', '');
                lugarPublicacion.removeAttribute('required')
                document.getElementById("checkLugarPublicacion").value = 1;


            } else {
                let lugarPublicacion = document.getElementById("lugarPublicacion");
                lugarPublicacion.value = "";
                // document.getElementById("lugarPublicacion").disabled = false; // Comentado por ahora
                lugarPublicacion.removeAttribute("readonly");
                lugarPublicacion.setAttribute('required', '');
                document.getElementById("checkLugarPublicacion").value = 0;

            }

        });

        $("#categoriaRegistro").select2({
    tags: true,
    multiple: true,
    ajax: {
        url: 'public/js/ajax/cargarDatosSelect2.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                categoria: params.term
            }
        },
        processResults: function (data) {
            // Mapeamos los datos para incluir el código Dewey
            var results = $.map(data, function (item) {
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
    // Cuando cambia la selección, tomamos la primera categoría
    var selectedData = $(this).select2('data');
    
    if (selectedData && selectedData.length > 0 && selectedData[0].codigo) {
        // Obtenemos el código Dewey de la primera categoría seleccionada
        var codigoDewey = selectedData[0].codigo;
        document.getElementById("sugerirCota").style.display = "block";
        // Actualizamos el campo cota con el código Dewey como sugerencia
        $('input[name="cota"]').val(codigoDewey)
                               .attr('placeholder', 'Sugerencia: ' + codigoDewey);
    }
});


        // Inicializar select2 para estantería
        // Select2 para estantería



    // Select2 para fila (configuración básica)
    //$("#filaEstanteria").select2({
    //    minimumInputLength: 0 // Permite mostrar todas las opciones sin escribir
    //});




        // Agregar lógica para permitir solo texto en el campo de búsqueda
    $('#autorRegistro').on('select2:open', function() {
    $('.select2-search__field').on('keypress', function(event) {
        var inputValue = String.fromCharCode(event.keyCode);
        if (!/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ\s]*$/.test(inputValue)) {
            event.preventDefault();
        }
    });
    });

    // Agregar lógica para permitir solo texto en el campo de búsqueda
    $('#editorialRegistro').on('select2:open', function() {
    $('.select2-search__field').on('keypress', function(event) {
        var inputValue = String.fromCharCode(event.keyCode);
        if (!/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ\s]*$/.test(inputValue)) {
            event.preventDefault();
        }
    });
    });

    // Agregar lógica para permitir solo texto en el campo de búsqueda
    $('#categoriaRegistro').on('select2:open', function() {
    $('.select2-search__field').on('keypress', function(event) {
        var inputValue = String.fromCharCode(event.keyCode);
        if (!/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ\s]*$/.test(inputValue)) {
            event.preventDefault();
        }
    });
    });

   


    });





    const input = document.getElementById('fecha_publicacion');
    const errorMessage = document.querySelector('.error-message');

    input.addEventListener('input', function () {
        if (isNaN(input.value)) {
            errorMessage.style.display = 'block';
        } else {
            errorMessage.style.display = 'none';
        }
    });



    function validarFormulario() {
        var select = document.getElementById('tipoDeMedio');
        var errorSelect = document.getElementById('errorSelect');

        if (select.value === "") {
            errorSelect.innerText = "Por favor, seleccione una opción.";
            return false;
        } else {
            errorSelect.innerText = "";
            return true;
        }
    }




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



$(document).ready(function() {
    // Variables globales
    let espaciosDisponibles = 0;
    const $cantidadInput = $('#cantidadEjemplares');
    const $errorMsg = $('#errorCantidad');
    
    // Función para validar la cantidad
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
        const estanteriaIdv = $('#estanteria').val();
console.log("Valor de Estantería:", estanteriaIdv);
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
    });
    
    // Función para cargar filas (USANDO EL FORMATO ORIGINAL DEL BACKEND)
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
                    
                    // Usamos directamente el texto que viene del backend
                    $filaSelect.append(
                        `<option value="${item.id}" ${disabled} 
                          data-disponible="${disponible}">
                            ${item.text}  <!-- Usamos el texto original del backend -->
                        </option>`
                    );
                });
                
                // Inicializar Select2 para fila
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
            // Extraemos la capacidad del texto de la opción
            const match = selectedOption.text().match(/\d+\/(\d+)/);
            const capacidad = match ? match[1] : '?';
            
            $('#espaciosDisponibles').text(`${espaciosDisponibles}/${capacidad} espacios disponibles`);
            $infoContainer.show();
            $cantidadInput.prop('disabled', false);
            
            // Validar inmediatamente la cantidad actual
            if ($cantidadInput.val()) {
                validarCantidad();
            }
        } else {
            $infoContainer.hide();
            $cantidadInput.prop('disabled', true).val('');
            $errorMsg.hide();
            $cantidadInput.removeClass('is-invalid');
        }
    });
    
    // Validación en tiempo real
    $cantidadInput.on('input', validarCantidad);
    
    // Validación al enviar el formulario
    $('#formulario').on('submit', function(e) {
        if (!validarCantidad()) {
            e.preventDefault();
            $cantidadInput.focus();
        }
    });
});



</script>