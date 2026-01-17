<style>
    /* Personalización del modal para hacerlo más único */
    .libro-registro-modal-content {
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .libro-registro-submit-btn {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 20px;
        font-weight: bold;
        border-radius: 5px;
        width: 100%;
    }

    .libro-registro-submit-btn:hover {
        background-color: #218838;
    }

    .modal-title {
        color: #333;
    }

    .modal-header {
        border-bottom: none;
    }



    /* Estilo general para el span-message */
    span.span-message {
        font-size: 0.875rem;
        /* Tamaño de fuente más pequeño */
        margin-top: 5px;
        /* Espacio superior para separar el mensaje del input */
        display: block;
        /* Hace que el span se comporte como un bloque para ocupar toda la línea */
        padding: 5px 10px;
        /* Espaciado interno para que el mensaje no esté pegado al borde */
        border-radius: 3px;
        /* Bordes redondeados */
        font-family: Arial, sans-serif;
        /* Fuente del mensaje */
    }

    /* Estilos para los mensajes rojos */
    span.span-message.red {
        background-color: #f8d7da;
        /* Fondo rojo suave */
        color: #721c24;
        /* Texto rojo oscuro */
        border: 1px solid #f5c6cb;
        /* Borde rojo claro */
    }

    /* Estilos para los mensajes verdes */
    span.span-message.green {
        background-color: #d4edda;
        /* Fondo verde suave */
        color: #155724;
        /* Texto verde oscuro */
        border: 1px solid #c3e6cb;
        /* Borde verde claro */
    }

    /* Opcional: puedes agregar un efecto al pasar el ratón sobre el mensaje */
    span.span-message:hover {
        opacity: 0.8;
        /* Efecto de opacidad al pasar el ratón */
    }






    /* Ajusta el contenedor de las opciones */
    .select2-checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        /* Espacio entre el checkbox y el texto */
        margin-bottom: 8px;
        /* Espaciado entre opciones */
    }

    /* Alinea los checkboxes verticalmente */
    .select2-results__options {
        display: flex;
        flex-direction: column;
        /* Muestra los elementos en una columna */
    }

    /* Opcional: Ajustar el tamaño del checkbox */
    .select2-checkbox {
        width: 16px;
        height: 16px;
        margin: 0;
        cursor: pointer;
    }

    /* Agregar espacio para el texto del autor */
    .select2-checkbox-wrapper span {
        font-size: 14px;
        color: #333;
        /* Ajusta el color si es necesario */
    }




    /* Estilos específicos para el select con ID #editorial */
    #editorial+.select2-container .select2-option,
    #categoriaRegistro+.select2-container .select2-option,
    #autor+.select2-container .select2-option {
        padding: 8px 12px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eaeaea;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #editorial+.select2-container .select2-option:hover {
        background-color: #f5f5f5;
    }

    #editorial+.select2-container .select2-results__options {
        max-height: 250px;
        overflow-y: auto;
        padding: 0;
    }

    #editorial+.select2-container .select2-selection--single {
        height: 40px;
        padding: 0 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        display: flex;
        align-items: center;
        background-color: #fff;
    }

    #editorial+.select2-container .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 10px;
    }
</style>


<!-- Botón para abrir el modal -->


<!-- Modal de Registro de Libro -->
<div class="modal fade" id="libroModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Registrar libro</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="libro-registro-form libro-registro-modal-content" action="app/controller/libros/c_registrar_libro.php" method="POST" id="formulario">

                    <input type="hidden" name="responsable" value="<?php echo $cedula; ?>">

                    <!-- Campo ISBN -->
                    <div class="libro-registro-input-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" id="isbn" name="isbn" class="isbnRegistro form-control" placeholder="Ingrese el ISBN"
                            required>
                    </div>

                    <!-- Campo Título -->
                    <div class="libro-registro-input-group">
                        <label for="titulo">Título</label>
                        <input type="text" id="titulo" name="titulo" class="form-control"
                            placeholder="Ingrese el título del libro" required>
                    </div>

                    <!-- Campo Año de publicación -->
                    <div class="libro-registro-input-group">
                        <label for="anio">Año de publicación</label>
                        <input type="text" id="anio" name="anio" maxlength="4" pattern="[0-9]{4}" title="Este campo solo admite numeros." class="form-control"
                            placeholder="Ingrese el año de publicación" required>
                    </div>

                    <!-- Campo Edición -->
                    <div class="libro-registro-input-group">
                        <label for="edicion">Edición</label>
                        <input type="text" id="edicion" name="edicion" class="form-control"
                            placeholder="Ingrese la edición" required>
                    </div>


                    <!-- Campo Autor -->
                    <div class="libro-registro-input-group">
                        <label for="autor">Autor(es)</label>
                        <select style="width: 100%;" name="autor[]" id="autor" class="form-select" required>
                            <!--Autores-->
                        </select>
                    </div>

                    <!-- Campo Categorías -->
                    <div class="libro-registro-input-group">
                        <label for="categoriaRegistro">Categoría</label>
                        <select name="categoria[]" id="categoriaRegistro" class="form-select" style="width: 100%;">
                            <!-- Categorías dinámicas -->
                        </select>
                    </div>

                    <!-- Campo Editorial -->
                    <div class="libro-registro-input-group">
                        <label for="editorial">Editorial</label>
                        <select style="width: 100%;" name="editorial" id="editorial" class="form-select" required>
                            <!-- Editoriales dinámicas -->

                        </select>
                    </div>


                    <div class="modal-footer">
                        <button type="submit" class="libro-registro-submit-btn">Registrar</button>
                    </div>
            </div>

        </div>

        </form>
    </div>
</div>



<!--script src="node_modules/select2/js/select2.min.js"></script-->

<script>
    /*$(document).ready(function() {
        $('#categoriaRegistro').select2({
            dropdownParent: $('#libroModal'),
            placeholder: 'Categorias',
            minimumInputLength: 2,
            multiple: true, // Enable multiple selections
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
                        nombreCategoria: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.id, // Usar `item` en lugar de `id`
                                text: item.text // Usar `item.text` para el texto
                            };
                        })
                    };
                },

                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            },

            // Customize how options are displayed in the dropdown
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                // Add checkbox next to the option text
                var $result = $('<span><input type="checkbox" class="select2-checkbox" data-id="' + data.id + '" /> ' + data.text + '</span>');
                // Check or uncheck the checkbox based on whether the item is selected
                var selected = $('#categoriaRegistro').val();
                if (selected && selected.indexOf(data.id) !== -1) {
                    $result.find('input').prop('checked', true); // Check the box if the item is selected
                }
                return $result;
            },

            // Customize how the selected items are displayed in the input field
            templateSelection: function(data) {
                return data.text; // Display the selected text in the input box
            }
        });

        // Handle selection and unselection manually to check/uncheck the checkboxes
        $('#categoriaRegistro').on('select2:select', function(e) {
            var selectedData = e.params.data;
            // Find the checkbox corresponding to the selected item and check it
            $('input[data-id="' + selectedData.id + '"]').prop('checked', true);
        });

        $('#categoriaRegistro').on('select2:unselect', function(e) {
            var unselectedData = e.params.data;
            // Find the checkbox corresponding to the unselected item and uncheck it
            $('input[data-id="' + unselectedData.id + '"]').prop('checked', false);
        });
    });


    // ------ SELECT2 PARA AUTORES -------

    $(document).ready(function() {
        $('#autor').select2({
            dropdownParent: $('#libroModal'), // Asegura que el dropdown se muestre correctamente dentro del modal
            placeholder: 'Seleccionar Autor',
            minimumInputLength: 2,
            multiple: true, // Permite múltiples selecciones
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
                        nombreAutor: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.id,
                                text: `${item.nombre} ${item.apellido}`
                            };
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            },

            // Personaliza cómo se muestran las opciones en el dropdown
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                // Añade un checkbox al lado de cada opción, con estilos personalizados
                const $result = $(`
                <div class="select2-checkbox-wrapper">
                    <input type="checkbox" class="select2-checkbox" data-id="${data.id}" /> 
                    <span>${data.text}</span>
                </div>
            `);

                // Marca o desmarca el checkbox según el estado de selección
                const selected = $('#autor').val();
                if (selected && selected.indexOf(data.id) !== -1) {
                    $result.find('input').prop('checked', true);
                }

                return $result;
            },

            // Personaliza cómo se muestran las selecciones en el campo de entrada
            templateSelection: function(data) {
                return data.text;
            }
        });

        // Manejo manual de la selección y deselección para los checkboxes
        $('#autor').on('select2:select', function(e) {
            const selectedData = e.params.data;

            // Encuentra el checkbox correspondiente y márcalo
            $(`input[data-id="${selectedData.id}"]`).prop('checked', true);
        });

        $('#autor').on('select2:unselect', function(e) {
            const unselectedData = e.params.data;

            // Encuentra el checkbox correspondiente y desmárcalo
            $(`input[data-id="${unselectedData.id}"]`).prop('checked', false);
        });
    });

    // ------- SELECT2 PARA EDITORIALES --------

    $(document).ready(function() {
        $('#editorial').select2({
            dropdownParent: $('#libroModal'), // Asegura que el dropdown funcione correctamente dentro del modal
            placeholder: 'Seleccionar Editorial', // Placeholder visible
            minimumInputLength: 2, // Requiere al menos 2 caracteres para buscar
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
                url: 'public/js/ajax/cargarDatosSelect2.php', // URL para obtener los datos
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        nombreEditorial: params.term // Envía el término de búsqueda
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.id,
                                text: item.nombre // Muestra el nombre de la editorial
                            };
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // Evita escapes innecesarios

            // Personaliza cómo se muestran las opciones
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }

                // Diseño moderno para cada opción
                return `
                <div class="select2-option">
                    <span>${data.text}</span>
                </div>
            `;
            },

            // Personaliza cómo se muestra la selección
            templateSelection: function(data) {
                return data.text || 'Seleccionar Editorial';
            }
        });
    });*/
</script>