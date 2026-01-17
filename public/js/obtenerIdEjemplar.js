const botonEF = document.getElementById("botonEF");
const inputFecha = document.querySelector("#fecha_fin");
const inputLector = document.querySelector("#lector");
const inputsPrestamos = document.querySelectorAll("#formulario .prestaInput");
let checkerCota = false;
let checkerFecha = false;
let checkerLector = false;
botonEF.setAttribute("disabled", "");

// Verificar si todos los check son true

let veces = 0;

const checkChecker = () => {
  let cdLector = document.getElementById("lector");
  const divFecha = document.getElementById("divFechaInput");
  if (!cdLector.value){
    console.log("no hay valor en cd lector")
    divFecha.style.display = "none";
    return;
  } else {
    divFecha.style.display = "block";
  }
  console.log("checker cota " + checkerCota);
  console.log("checker lector " + checkerLector);
  console.log("checker fecha " + checkerFecha);
  console.log("Veces entradas en la funcion: " + veces);
  veces++;
  if (checkerCota && checkerFecha && checkerLector) {
    if (botonEF.hasAttribute("disabled")) {
      botonEF.removeAttribute("disabled", "");
    }
  } else {
    if (!botonEF.hasAttribute("disabled")) {
      botonEF.setAttribute("disabled", "");
    }
  }

  

};
//Obtener fecha de venezuela
function obtenerFechaCaracas() {
  const fechaActual = new Date();
  const diferenciaUTC = fechaActual.getTimezoneOffset(); // Diferencia local con UTC en minutos
  const diferenciaCaracas = -4 * 60; // Caracas es UTC-4, en minutos

  const diferenciaTotal = diferenciaCaracas - diferenciaUTC;
  const fechaCaracas = new Date(fechaActual.getTime() + diferenciaTotal * 60 * 1000);

  return fechaCaracas.toISOString().split("T")[0];
}

console.log(obtenerFechaCaracas());

//CALCULAR NUEVA FECHA BASADO EN LA UNIDAD (DIA, SEMANA, AÑO) ESCOGIDA
function calcularNewFecha(fecha, unidades, cantidad) {
  let nuevaFecha = new Date(fecha);
  switch (unidades) {
      case "días":
      case "dias":
          nuevaFecha.setDate(nuevaFecha.getDate() + cantidad);
          break;
      case "semanas":
          nuevaFecha.setDate(nuevaFecha.getDate() + (cantidad * 7));
          break;
      case "meses":
          nuevaFecha.setMonth(nuevaFecha.getMonth() + cantidad);
          break;
      default:
          console.warn("Unidad de tiempo no válida");
          return null;
  }
  return nuevaFecha;
}

// ------ VERIFICAR FECHA -------
function validarFecha() {
  let cedulaLector = document.getElementById("lector").value.trim();
  console.log(cedulaLector)

  // Deshabilitar botón si no hay cédula
  if (!cedulaLector) {
    console.log("Cedula no definida")
      botonEF.disabled = true;
      return;
  }
  
  botonEF.disabled = false;

  const fechaSeleccionada = new Date(inputFecha.value);
  fechaSeleccionada.setDate(fechaSeleccionada.getDate() + 1);
  const fechaActual = new Date();

  //inputFecha.value = obtenerFechaCaracas(fechaActual);

  fetch("public/js/ajax/obtener_regla_circulacion.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: `cedulaPrestamo=${encodeURIComponent(cedulaLector)}`
  })
  .then(response => {
      if (!response.ok) {
          throw new Error(`Error HTTP: ${response.status}`);
      }
      return response.json();
  })
  .then(data => {
      if (data.error) {
        crearSpan(inputFecha, inputFecha.nextElementSibling, 
          `${data.error}`, "red");
          inputFecha.value = obtenerFechaCaracas(fechaActual);
          document.getElementById("botonEF").disabled = true;
          return;
      }else{
        document.getElementById("botonEF").disabled = false;
      }

      if (!data.unidades || typeof data.periodo_prestamo === "undefined") {
          throw new Error("Respuesta inválida del servidor.");
      }

      let unidades = data.unidades.toLowerCase();
      let periodoPrestamo = parseInt(data.periodo_prestamo, 10);

      if (isNaN(periodoPrestamo) || periodoPrestamo <= 0) {
          throw new Error("El periodo de préstamo recibido no es válido.");
      }

      const nuevaFecha = calcularNewFecha(fechaActual, unidades, periodoPrestamo);
      if (!nuevaFecha) return;

      // Formatea la nueva fecha a 'yyyy-MM-dd'
      const fechaFormateada = nuevaFecha.toISOString().split("T")[0];

      crearSpan(document.getElementById("divFechaInput"), document.getElementById("divFechaInput").nextElementSibling, `El usuario puede solicitar un préstamo por un tiempo máximo de ${periodoPrestamo} ${unidades}.`);
      console.log(`El usuario puede solicitar un préstamo por un tiempo máximo de ${periodoPrestamo} ${unidades}.`)

      //inputFecha.value = fechaFormateada;

      if (fechaSeleccionada >= fechaActual && fechaSeleccionada <= nuevaFecha) {
          crearSpan(inputFecha, inputFecha.nextElementSibling, "Fecha válida.", "green");
          inputFecha = fechaSeleccionada;
      } else {
          crearSpan(inputFecha, inputFecha.nextElementSibling, 
              `Fecha inválida. Debe ser mayor o igual a la fecha actual y no mayor que ${periodoPrestamo} ${unidades}.`, "red");
              inputFecha.value = obtenerFechaCaracas(fechaActual);
      }
  })
  .catch(error => {
      console.error("Error al obtener la regla de circulación:", error.message);
      crearSpan(
          spanAdvertenciaFecha,
          spanAdvertenciaFecha.nextElementSibling,
          "Ocurrió un error al obtener la información del préstamo.",
          "red",
          true,
          true
      );
  });
}


inputFecha.addEventListener("change", validarFecha);

// -------- VERIFICAR COTA 2.0 --------

$(document).ready(function () {
  // Inicializa la variable checkerCota
  // let checkerCota = false;

  $("#cota").select2({
    dropdownParent: $("#registrarPrestamo"), // Para asegurarte de que funcione dentro del modal
    placeholder: "Escriba la cota del libro a prestar.",
    minimumInputLength: 2,
    language: {
      inputTooShort: function () {
        return "Por favor, ingrese al menos 2 caracteres";
      },
      noResults: function () {
        return "No se encontraron resultados";
      },
      searching: function () {
        return "Buscando...";
      },
    },
    ajax: {
      url: "public/js/ajax/cargarDatosSelect2.php",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          nombreCota: params.term, // Nombre de la cota que el usuario escribe
        };
      },
      processResults: function (data) {
        return {
          results: data.results.map(function (item) {
            return {
              id: item.id, // El ID único de la cota
              text: item.text, // El texto que se mostrará
            };
          }),
        };
      },
      cache: true,
      timeout: 5000, // 5 segundos de tiempo máximo para la solicitud
      error: function (jqXHR, textStatus) {
        if (textStatus === "timeout") {
          alert("La búsqueda ha tardado demasiado. Intenta nuevamente.");
        }
      },
    },

    escapeMarkup: function (markup) {
      return markup;
    },
    templateResult: function (data) {
      if (data.loading) {
        return data.text;
      }
      return `<span>${data.text}</span>`;
    },
    templateSelection: function (data) {
      return data.text || "Seleccionar Cota";
    },
  });

  // Evento cuando se selecciona una opción
  $("#cota").on("select2:select", function () {
    checkerCota = true; // Cambiar checkerCota a true
    checkChecker();

    console.log("Cota seleccionada:", checkerCota);
  });

  // Evento cuando se deselecciona una opción
  $("#cota").on("select2:unselect", function () {
    checkerCota = false; // Cambiar checkerCota a false
    checkChecker();

    console.log("Cota deseleccionada:", checkerCota);
  });

  // Para comprobar el estado inicial al cargar
  $("#cota").on("change", function () {
    checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
    checkChecker();

    console.log("Cambio detectado:", checkerCota);
  });
});

// ------ VERIFICAR CEDULA LECTOR -------

$(document).ready(function () {
  $("#lector").select2({
    dropdownParent: $("#registrarPrestamo"),
    placeholder: "Cedula del lector",
    minimumInputLength: 2, // Start searching after the user types 1 character
    language: {
      inputTooShort: function () {
        return "Por favor, ingrese al menos 2 caracteres"; // Custom message for too few characters
      },
      noResults: function () {
        checkerLector = false;
        return "No se encontraron resultados"; // Custom "No results found" message
      },
      searching: function () {
        return "Buscando..."; // Message while searching
      },
    },
    ajax: {
      url: "public/js/ajax/validarInputsRegistroPrestamo.php", // The URL of your PHP script
      dataType: "json",
      delay: 250, // Delay before making the request to prevent too many calls
      data: function (params) {
        return {
          cedulaLector: params.term, // Send the search term as 'cedulaLector'
        };
      },
      processResults: function (data) {
        // Format the returned data for Select2
        return {
          results: data.results.map(function (user) {
            return {
              id: user.id, // User's ID (cedula)
              text: user.text, // User's name
            };
          }),
        };
      },
      cache: true,
    },
    escapeMarkup: function (markup) {
      return markup;
    }, // Prevent Select2 from escaping markup
  });

  // Trigger checkerLector = true when a user is selected
  $("#lector").on("select2:select", function (e) {
    checkerLector = true; // User selected
    checkChecker();
    validarFecha();
    console.log("checkerLector:", checkerLector); // Debugging
  });

  // Trigger checkerLector = false when dropdown closes without a selection
  $("#lector").on("select2:close", function () {
    if ($("#lector").val() === null || $("#lector").val() === "") {
      checkerLector = false; // No selection
      checkChecker();

      console.log("checkerLector:", checkerLector); // Debugging
    }
  });

  

});

//inputLector.addEventListener("input", validarLector);

inputsPrestamos.forEach((input) => {
  input.addEventListener("change", checkChecker);
  /* input.addEventListener("input", checkChecker);
    input.addEventListener("keydown", checkChecker);
    input.addEventListener("keyup", checkChecker);
    input.addEventListener("blur", checkChecker);
    input.addEventListener("focus", checkChecker);
    input.addEventListener("click", checkChecker);
    */
});


/*
function crearSpan(elemento, existingSpan, texto, redOrGreen = "") {
    if (elemento.value === "") {
        // Si el campo está vacío, limpia el mensaje de error
        if (existingSpan && existingSpan.classList.contains('span-message')) {
            existingSpan.innerHTML = "";
            elemento.style.border = "";
            
        }
        return; // Salir porque no es necesario continuar
    }

    if (existingSpan && existingSpan.classList.contains('span-message')) {
        // Si ya existe un <span> de error, actualiza su contenido
        existingSpan.innerHTML = texto;


         // Eliminar cualquier clase anterior (red o green)
         const clasesExistentes = ['red', 'green'];
         clasesExistentes.forEach(clase => {
                if (existingSpan.classList.contains(clase)) {
                    existingSpan.classList.remove(clase);
                }
            });
        

        if (redOrGreen){
            // Agregar nueva clase y borde al input
            existingSpan.classList.add(redOrGreen);
            elemento.style.border = redOrGreen ? "1px solid " + redOrGreen : "";
        } else {
            elemento.style.border = "";

        }

       
            
            

        

    } else {

        //Agregar borde al elemento
        elemento.style.border = redOrGreen ? "1px solid " + redOrGreen : "";




        // Crear un nuevo <span> si no existe
        const span = document.createElement("span");
        span.classList.add('span-message');
        if (redOrGreen){
        span.classList.add(redOrGreen);

        }
        span.innerHTML = texto;

        // Insertar el nuevo <span> después del elemento de entrada
        elemento.parentNode.insertBefore(span, elemento.nextSibling);
    }
}

*/

// ------ VERIFICAR COTA --------
/*$(document).ready(function() {
    $("#cota").on("input", function() {
        var cota = $(this).val();

        // Si el nombre está vacío, limpiar el input oculto
        if (cota === "") {
            $("#idEjemplar").val(""); // Aquí se limpia el input oculto
        } else {
            $.ajax({
                url: "public/js/ajax/validarInputsRegistroPrestamo.php",
                data: { cota: cota },
                type: "POST",
                success: function(response) {
                    var resultado = JSON.parse(response);
                    if (resultado.id === "" && resultado.titulo === ""){
                        $("#idEjemplar").val("");
                        $("#tituloLibroSpan").text("¡Esa cota no pertenece a ningún libro!");
                        document.getElementById("tituloLibroSpan").style.display = "block";
                        console.log("primer block");

                        checkerCota = false;
                      
                    }
                    else {
                        $("#idEjemplar").val(resultado.id);
                        $("#tituloLibroSpan").text(resultado.titulo);
                        document.getElementById("tituloLibroSpan").style.display = "block";
                        console.log("segundo block");

                       checkerCota = true;
                    }
                    
                    


                   
                }
            });
        }
    });
});

*/

// ------ SELECT2 PARA VERIFICAR CATEGORIA CON MULTIPLES CHECK --------
/*$(document).ready(function() {
    let checkerLector = false; // Default state

    $('#lector').select2({
        dropdownParent: $('#registrarPrestamo'),
        placeholder: 'Cédula del lector',
        minimumInputLength: 2,
        multiple: true,  // Enable multiple selections
        language: {
            inputTooShort: function () {
                return "Por favor, ingrese al menos 2 caracteres";
            },
            noResults: function () {
                checkerLector = false; // Update state when no results are found
                console.log('checkerLector:', checkerLector); // Debugging
                return "No se encontraron resultados";
            },
            searching: function () {
                return "Buscando...";
            }
        },
        ajax: {
            url: 'public/js/ajax/validarInputsRegistroPrestamo.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    cedulaLector: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.results.map(function(user) {
                        return {
                            id: user.id,
                            text: user.text
                        };
                    })
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) { return markup; },

        // Customize how options are displayed in the dropdown
        templateResult: function (data) {
            if (data.loading) {
                return data.text;
            }
            // Add checkbox next to the option text
            var $result = $('<span><input type="checkbox" class="select2-checkbox" data-id="' + data.id + '" /> ' + data.text + '</span>');
            // Check or uncheck the checkbox based on whether the item is selected
            var selected = $('#lector').val();
            if (selected && selected.indexOf(data.id) !== -1) {
                $result.find('input').prop('checked', true); // Check the box if the item is selected
            }
            return $result;
        },

        // Customize how the selected items are displayed in the input field
        templateSelection: function (data) {
            return data.text; // Display the selected text in the input box
        }
    });

    // Handle selection and unselection manually to check/uncheck the checkboxes
    $('#lector').on('select2:select', function(e) {
        var selectedData = e.params.data;
        // Find the checkbox corresponding to the selected item and check it
        $('input[data-id="' + selectedData.id + '"]').prop('checked', true);
        checkerLector = true; // User selected
        console.log('checkerLector:', checkerLector); // Debugging
    });

    $('#lector').on('select2:unselect', function(e) {
        var unselectedData = e.params.data;
        // Find the checkbox corresponding to the unselected item and uncheck it
        $('input[data-id="' + unselectedData.id + '"]').prop('checked', false);
        // Check if there are no selections, and update checkerLector
        if ($('#lector').val().length === 0) {
            checkerLector = false;
            console.log('checkerLector:', checkerLector); // Debugging
        }
    });

    // Trigger checkerLector = false when dropdown closes without a selection
    $('#lector').on('select2:close', function() {
        if ($('#lector').val() === null || $('#lector').val().length === 0) {
            checkerLector = false; // No selection
            console.log('checkerLector:', checkerLector); // Debugging
        }
    });
});




*/
