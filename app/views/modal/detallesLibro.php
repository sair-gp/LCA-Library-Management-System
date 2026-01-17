<style>
  .nav-tabs {
    --bs-nav-link-padding-x: 0.5rem;
    --bs-nav-link-padding-y: 0.5rem;
  }
</style>

<!-- Modal con pestañas -->
<div class="modal fade" id="detallesModal" tabindex="-1" aria-labelledby="detallesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detallesModalLabel">Detalles del Libro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <input type="hidden" id="isbnDetalles">

        <!-- Pestañas -->
        <ul class="nav nav-tabs" id="detailsTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">Detalles</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="add-copy-tab" data-bs-toggle="tab" data-bs-target="#add-copy" type="button" role="tab" aria-controls="add-copy" aria-selected="false">Agregar Ejemplar</button>
          </li>
          <!-- Tercera Pestaña: Desincorporar Ejemplar -->
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="desincorporate-copy-tab" data-bs-toggle="tab" data-bs-target="#desincorporate-copy" type="button" role="tab" aria-controls="desincorporate-copy" aria-selected="false">Desincorporar Ejemplar</button>
          </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content mt-3" id="detailsTabContent">
          <!-- Pestaña Detalles -->
          <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
            <div class="container-fluid">
              <div class="row mb-3">
                <div class="col-12">
                  <h5 class="text-primary">Detalles del Libro</h5>
                </div>
                <div class="col-6">
                  <p><strong>ISBN:</strong> <span id="book-isbn">978-1234567890</span></p>
                  <p><strong>Título:</strong> <span id="book-title">Ejemplo de Título</span></p>
                  <p><strong>Autor(es):</strong> <span id="book-authors">John Doe, Jane Smith</span></p>
                  <p><strong>Año de Publicación:</strong> <span id="book-year">2020</span></p>
                </div>
                <div class="col-6">
                  <p><strong>Editorial:</strong> <span id="book-editorial">Editorial Ejemplo</span></p>
                  <p><strong>Categorías:</strong> <span id="book-categories">Ficción, Misterio</span></p>
                  <p><strong>Fecha de Ingreso:</strong> <span id="book-entry-date">01/01/2022</span></p>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12">
                  <h5 class="text-primary">Estado de los Ejemplares</h5>
                </div>
                <div class="col-6">
                  <p><strong>Total de Copias:</strong> <span id="book-copies">15</span></p>
                  <p><strong>En Circulación:</strong> <span id="book-circulating">8</span></p>
                </div>
                <div class="col-6">
                  <p><strong>Copias en Mal Estado:</strong> <span id="book-damaged">2</span></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Pestaña Agregar Ejemplar -->
          <div class="tab-pane fade" id="add-copy" role="tabpanel" aria-labelledby="add-copy-tab">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12">
                  <h5 class="text-primary">Agregar Nuevo Ejemplar</h5>

                  <form id="add-copy-form">
                    <!-- Input para la Cota -->
                    <div class="mb-3">
                      <label for="copy-cota" class="form-label">Cota</label>
                      <input type="text" class="form-control" id="copy-cota" placeholder="Ingrese la cota">
                    </div>
                    <!-- Input para la Ubicación -->
                    <!--div class="mb-3">
                      <label for="copy-location" class="form-label">Ubicación</label>
                      <input type="text" class="form-control" id="copy-location" placeholder="Estante, Sala, etc.">
                    </div-->

                    <div class="mb-3">
                      <label for="copy-location" class="form-label">Ubicacion:</label>
                      <select class="form-select" id="copy-location">
                        <option value="0">Seleccione la ubicación</option>
                        
                      </select>
                    </div>
                    <!-- Select para el Estado -->
                    <div class="mb-3">
                      <label for="copy-source" class="form-label">Suministrado mediante:</label>
                      <select class="form-select" id="copy-source">
                        <option value="2">Donación</option>
                        <option value="1">Red de bibliotecas</option>
                        <!--option value="3">Dañado</option-->
                      </select>
                    </div>
                    <!-- Botón para guardar -->
                    <div class="text-end">
                      <button type="submit" id="botonAgregarEjemplar" class="btn btn-success">
                        <span id="botonTexto">Agregar Ejemplar</span>
                        <span id="botonCargando" style="display: none;">Cargando...</span> <!-- Indicador de carga -->
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Pestaña Desincorporar Ejemplar -->
          <div class="tab-pane fade" id="desincorporate-copy" role="tabpanel" aria-labelledby="desincorporate-copy-tab">
            <div class="container-fluid">
              <div class="row">
                <div class="col-12">
                  <h5 class="text-primary">Desincorporar Ejemplar</h5>

                  <form id="desincorporate-copy-form">
                    <!-- Input para la Cota -->
                    <div class="mb-3">
                      <label for="copy-cota-des" class="select-label">Cota</label>
                      <select class="form-select" id="copy-cota-des" name="copy-cota-des">

                      </select>
                    </div>

                    <!-- Select para el Estado -->
                    <div class="mb-3">
                      <label for="copy-detail" class="form-label">Descripcion</label>
                      <input type="text" class="form-control" id="copy-detail" name="copy-detail" placeholder="Descripción de la condición física del libro (opcional).">
                    </div>

                    <!-- Botón para guardar -->
                    <div class="text-end">
                      <button type="submit" id="botonDesincorporarEjemplar" class="btn btn-success">
                        <span id="botonTexto">Desincorporar Ejemplar</span>
                        <span id="botonCargando" style="display: none;">Cargando...</span> <!-- Indicador de carga -->
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>




<!--script defer src="node_modules/select2/js/select2.min.js"></script-->
<script defer>
  // Función para actualizar los detalles del libro
  function actualizarDetallesLibro(isbn) {

    $.ajax({
      url: 'public/js/ajax/libros.php',
      method: 'GET',
      data: {
        isbn: isbn
      },
      success: function(response) {
        try {
          const data = JSON.parse(response);
          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.error,
            });
          } else {
            // Actualiza los detalles del libro
            document.getElementById('book-isbn').textContent = data.isbn;
            document.getElementById('book-title').textContent = data.titulo;
            document.getElementById('book-authors').textContent = data.autores;
            document.getElementById('book-year').textContent = data.anio;
            document.getElementById('book-editorial').textContent = data.editorialN;
            document.getElementById('book-categories').textContent = data.categorias;
            document.getElementById('book-entry-date').textContent = data.fecha_registro;

            // Estado de los ejemplares
            document.getElementById('book-copies').textContent = data.cantidad_ejemplares;
            document.getElementById('book-circulating').textContent = data.en_circulacion;
            document.getElementById('book-damaged').textContent = data.copias_danadas;

            // TD en la tabla
            //document.getElementById("copiasTd").textContent = "";

            // Actualiza el contenido del td correctamente
            const copiasTdElements = document.querySelectorAll('.copiasTd');
            copiasTdElements.forEach(td => {
              if (td.closest('tr').querySelector('.notEditable').textContent === data.isbn) {
                td.textContent = data.cantidad_ejemplares;
              }
            });

            console.log('Copias actualizadas:', data.cantidad_ejemplares);



          }
        } catch (e) {
          console.error("Error al parsear JSON:", e);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema con la respuesta del servidor.',
          });
        }
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Hubo un error al procesar la solicitud.',
        });
      }
    });
  }

  // Función para agregar ejemplar
  function agregarEjemplar(event) {
    event.preventDefault(); // Evita la recarga de la página

    const cotaF = document.getElementById("copy-cota").value;
    const fuente = document.getElementById("copy-source").value;
    const isbnDetalles = document.getElementById("isbnDetalles").value;
    const titulo = document.getElementById("book-title").textContent;
    console.log(titulo)
    const botonAgregarEjemplar = document.getElementById("botonAgregarEjemplar");

    // Verifica que los valores sean válidos
    if (!cotaF || !fuente || !isbnDetalles) {
      Swal.fire({
        icon: 'warning',
        title: 'Formulario incompleto',
        text: 'Por favor, completa todos los campos antes de agregar el ejemplar.',
      });
      return;
    }

    // Deshabilita el botón para evitar múltiples clics
    botonAgregarEjemplar.setAttribute("disabled", "disabled");

    // Realiza la solicitud AJAX
    $.ajax({
      url: "public/js/ajax/libros.php",
      method: "GET",
      data: {
        cota: cotaF,
        fuente: fuente,
        isbnDetalles: isbnDetalles,
        titulo: titulo
      },
      success: function(response) {
        try {
          const data = JSON.parse(response);

          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.error,
            });
          } else {
            Swal.fire({
              icon: 'success',
              title: '¡Añadido!',
              text: 'El ejemplar se registró correctamente.',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              document.getElementById("copy-cota").value = "";
              // Actualiza los detalles después de registrar el ejemplar
              actualizarDetallesLibro(isbnDetalles);
            });
          }
        } catch (e) {
          console.error("Error al parsear JSON:", e);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema con la respuesta del servidor.',
          });
        }
      },
      error: function(xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Hubo un error al procesar la solicitud.',
        });
      },
      complete: function() {
        // Habilita el botón nuevamente
        botonAgregarEjemplar.removeAttribute("disabled");
      }
    });
  }


  function desincorporarEjemplar(evento) {
    evento.preventDefault(); // Previne la recarga de la página al enviar el formulario

    // Se obtienen los valores de los campos del formulario
    const numeroCota = document.getElementById("copy-cota-des").value;
    //const estadoEjemplar = document.getElementById("copy-condition").value;
    const isbnDelLibro = document.getElementById("isbnDetalles").value;
    const tituloDelLibro = document.getElementById("book-title").textContent;
    console.log(tituloDelLibro);

    // Referencia al botón de agregar ejemplar para deshabilitarlo temporalmente
    const botonDesincorporar = document.getElementById("botonDesincorporarEjemplar");

    //console.log(tituloDelLibro)
    //console.log("isbn" + isbnDelLibro)
    //console.log("num cota" + numeroCota)

    // Verificación de que todos los campos del formulario estén completos
    if (!numeroCota || !isbnDelLibro || !tituloDelLibro) {
      Swal.fire({
        icon: 'warning',
        title: 'Formulario incompleto',
        text: 'Por favor, completa todos los campos antes de desincorporar el ejemplar.',
      });
      return; // Detiene la ejecución si algún campo está vacío
    }

    // Deshabilita el botón para evitar múltiples clics mientras se procesa la solicitud
    botonDesincorporar.setAttribute("disabled", "disabled");

    // Se realiza la solicitud AJAX para desincorporar el ejemplar
    $.ajax({
      url: "public/js/ajax/libros.php",
      method: "GET",
      data: {
        cotaDeshabilitar: numeroCota, // Se pasa el número de cota del ejemplar
        //condicionDeshabilitar: estadoEjemplar, // Se pasa el estado del ejemplar
        isbnDeshabilitar: isbnDelLibro, // Se pasa el ISBN del libro
        tituloDeshabilitar: tituloDelLibro // Se pasa el título del libro
      },
      success: function(respuesta) {
        try {
          // Se parsea la respuesta JSON
          const datos = JSON.parse(respuesta);

          // Se maneja el caso en que la respuesta contiene un error
          if (datos.error) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: datos.error,
            });
          } else {
            // Si la respuesta es exitosa, se muestra un mensaje y se actualizan los detalles
            Swal.fire({
              icon: 'success',
              title: '¡Desincorporado!',
              text: 'El ejemplar se desincorporó correctamente.',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              // Se limpia el campo del número de cota para nuevos registros
              document.getElementById("copy-cota-des").value = "";
              // Se actualizan los detalles del libro después de desincorporarlo
              actualizarDetallesLibro(isbnDelLibro);
            });
          }
        } catch (e) {
          // Manejo de errores si ocurre un problema al parsear la respuesta
          console.error("Error al parsear JSON:", e);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema con la respuesta del servidor.',
          });
        }
      },
      error: function(xhr, estado, error) {
        // Manejo de errores si la solicitud AJAX falla
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Hubo un error al procesar la solicitud.',
        });
      },
      complete: function() {
        // Se habilita nuevamente el botón después de completar la solicitud
        botonDesincorporar.removeAttribute("disabled");
      }
    });
  }


  // Configuración inicial
  document.addEventListener("DOMContentLoaded", () => {
    const copyInputs = document.querySelectorAll("#add-copy-form input");
    const copyInputsDesincorporate = document.querySelectorAll("#desincorporate-copy-form select");
    const botonDesincorporar = document.getElementById("botonDesincorporarEjemplar");
    const botonAgregarEjemplar = document.getElementById("botonAgregarEjemplar");

    botonAgregarEjemplar.setAttribute("disabled", "");

    copyInputs.forEach(input => {
      input.addEventListener("input", () => {
        const cota = document.getElementById("copy-cota").value;
        const condicion = document.getElementById("copy-source").value;
        // Verifica que cota y condicion tengan algun valor
        if (cota && condicion) {
          botonAgregarEjemplar.removeAttribute("disabled");
        } else {
          botonAgregarEjemplar.setAttribute("disabled", "");
        }
      });
    });

    copyInputsDesincorporate.forEach(input => {
      input.addEventListener("input", () => {
        const cotaD = document.getElementById("copy-cota-des").value;
        //const condicionD = document.getElementById("copy-condition").value;
        // Verifica que cota y condicion tengan algun valor
        if (cotaD) {
          botonDesincorporar.removeAttribute("disabled");
        } else {
          botonDesincorporar.setAttribute("disabled", "");
        }
      });
    });



    // Asocia el evento submit al formulario
    document.getElementById('add-copy-form').addEventListener('submit', agregarEjemplar);
    document.getElementById('desincorporate-copy-form').addEventListener('submit', desincorporarEjemplar);
  });



  // select 2 para desincorporar cota
  $(document).ready(function() {


    $('#copy-cota-des').select2({
      dropdownParent: $('#detallesModal'), // Para asegurarte de que funcione dentro del modal
      placeholder: 'Seleccionar Cota',
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
            nombreCotaDes: params.term, // Nombre de la cota que el usuario escribe
            isbn: isbnOg,

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
        return data.text || 'Seleccionar Cota';
      }
    });

    // Evento cuando se selecciona una opción
    /* $('#cota').on('select2:select', function() {
       checkerCota = true; // Cambiar checkerCota a true
       checkChecker();

       console.log('Cota seleccionada:', checkerCota);
     });

     // Evento cuando se deselecciona una opción
     $('#cota').on('select2:unselect', function() {
       checkerCota = false; // Cambiar checkerCota a false
       checkChecker();

       console.log('Cota deseleccionada:', checkerCota);
     });

     // Para comprobar el estado inicial al cargar
     $('#cota').on('change', function() {
       checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
       checkChecker();

       console.log('Cambio detectado:', checkerCota);
     });*/

    // Establecer el ancho del contenedor de Select2 al 100%
    $('.select2-container').css('width', '100%');
  });






// select 2 para ubicacion
  $(document).ready(function() {


    $('#copy-location').select2({
      dropdownParent: $('#detallesModal'), // Para asegurarte de que funcione dentro del modal
      placeholder: 'Seleccionar Ubicación',
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
            ubicacion: params.term // Nombre de la ubicacion que el usuario escribe
          

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
        return data.text || 'Seleccionar Cota';
      }
    });

    // Evento cuando se selecciona una opción
    /* $('#cota').on('select2:select', function() {
       checkerCota = true; // Cambiar checkerCota a true
       checkChecker();

       console.log('Cota seleccionada:', checkerCota);
     });

     // Evento cuando se deselecciona una opción
     $('#cota').on('select2:unselect', function() {
       checkerCota = false; // Cambiar checkerCota a false
       checkChecker();

       console.log('Cota deseleccionada:', checkerCota);
     });

     // Para comprobar el estado inicial al cargar
     $('#cota').on('change', function() {
       checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
       checkChecker();

       console.log('Cambio detectado:', checkerCota);
     });*/

    // Establecer el ancho del contenedor de Select2 al 100%
    $('.select2-container').css('width', '100%');
  });

</script>