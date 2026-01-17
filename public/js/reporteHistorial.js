// JavaScript para cambiar entre tabs
document.querySelectorAll(".tab").forEach((tab) => {
  tab.addEventListener("click", () => {
    document
      .querySelectorAll(".tab")
      .forEach((t) => t.classList.remove("active"));
    document
      .querySelectorAll(".tab-panel")
      .forEach((panel) => panel.classList.remove("active"));

    tab.classList.add("active");
    document.getElementById(tab.dataset.tab).classList.add("active");
  });
});

//Javascript select2 responsables

$(document).ready(function () {
  // Inicializa la variable checkerCota
  // let checkerCota = false;

  $("#responsable").select2({
    dropdownParent: $("#reporteModal"), // Para asegurarte de que funcione dentro del modal
    placeholder: "Cédula o nombre del responsable",
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
          responsable: params.term, // Nombre de la cota que el usuario escribe
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
      return data.text || "Seleccionar responsable";
    },
  });

  // Evento cuando se selecciona una opción
  /*
    $('#responsable').on('select2:select', function() {
        checkerCota = true; // Cambiar checkerCota a true
        checkChecker();

        console.log('Cota seleccionada:', checkerCota);
    });

    // Evento cuando se deselecciona una opción
    $('#responsable').on('select2:unselect', function() {
        checkerCota = false; // Cambiar checkerCota a false
        checkChecker();

        console.log('Cota deseleccionada:', checkerCota);
    });

    // Para comprobar el estado inicial al cargar
    $('#cota').on('change', function() {
        checkerCota = $(this).val() !== null; // Si hay un valor seleccionado, es true; si no, es false
        checkChecker();

        console.log('Cambio detectado:', checkerCota);
    });

    */
});
