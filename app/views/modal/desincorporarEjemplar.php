
<!-- Modal -->
<div class="modal fade" id="desincorporarModal" tabindex="-1" aria-labelledby="desincorporarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Encabezado del modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="desincorporarModalLabel">Desincorporar Ejemplar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <form id="desincorporarForm" action="" method="POST">
                    <!-- Select para la cota -->
                    <div class="mb-3">
                        <input type="hidden" id="isbnDes" name="isbnDes">
                        <input type="hidden" id="tituloDes" name="tituloDes" value="<?= $tituloDes ?>">
                        <input type="hidden" id="cotaVolumen">
                        <label for="cota" class="form-label">Cota del libro</label>
                        <select class="form-select" id="cotaDes" name="cota" required>
                            <option value="" selected disabled>Seleccione una cota</option>
                            
                        </select>
                    </div>

                    <!-- Textarea para el motivo -->
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de desincorporación</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required></textarea>
                    </div>
                </form>
            </div>

            <!-- Pie del modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="desincorporarForm" class="btn btn-danger">Desincorporar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
  // Inicializa la variable checkerCota
  // let checkerCota = false;


  $("#cotaDes").select2({
    dropdownParent: $("#desincorporarModal"), // Para asegurarte de que funcione dentro del modal
    placeholder: "Escriba la cota del libro a prestar.",
    minimumInputLength: 2,
    language: {
      inputTooShort: function () {
         const cotaBase = $('#cotaVolumen').val() || '[prefijo numérico]'; 
         return `Escriba la cota completa del ejemplar a desincorporar. Debe empezar por ${cotaBase}`;
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
          nombreCotaDes: params.term,  // Nombre de la cota que el usuario escribe
          isbnDes: document.getElementById("isbnDes").value 
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

  
});


document.getElementById("desincorporarForm").addEventListener("submit", function (event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("public/js/ajax/desincorporarEjemplar.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la red');
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            // Actualizar los contadores según tu estructura HTML
            updateCounters();
            
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonText: 'Aceptar'
            }).then(() => {
                closeModal();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error desconocido',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud',
            confirmButtonText: 'Aceptar'
        });
    });

    function updateCounters() {
        // Para obras completas (los contadores están en spans con estos IDs)
        const copiasSpan = document.getElementById("vol-copias");
        const disponiblesSpan = document.getElementById("vol-disponibes"); // Nota: hay un typo en "disponibes"
        
        if (copiasSpan && disponiblesSpan) {
            // Actualizar los valores directamente
            copiasSpan.textContent = parseInt(copiasSpan.textContent) - 1;
            disponiblesSpan.textContent = parseInt(disponiblesSpan.textContent) - 1;
        } 
        // Para volúmenes individuales (en tablas)
        else {
            const cota = formData.get('cota');
            const rows = document.querySelectorAll('.volumes-table tbody tr');
            
            rows.forEach(row => {
                const rowCota = row.querySelector('.cotaVolReg')?.textContent;
                if (rowCota === cota) {
                    const copiasTd = row.querySelector('.copiasVolTD');
                    const disponiblesTd = row.querySelector('.disponiblesVolTD');
                    
                    if (copiasTd && disponiblesTd) {
                        copiasTd.textContent = parseInt(copiasTd.textContent) - 1;
                        disponiblesTd.textContent = parseInt(disponiblesTd.textContent) - 1;
                    }
                }
            });
        }
    }

    function closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('desincorporarModal'));
        modal?.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
    }
});




// Escuchar el evento 'hidden.bs.modal' del modal
document.getElementById('desincorporarModal').addEventListener('hidden.bs.modal', function () {
        // Resetear el formulario
        document.getElementById('desincorporarForm').reset();

        // Resetear campos específicos si es necesario
        document.getElementById('cotaDes').value = ''; 
        document.getElementById('motivo').value = ''; 
        //document.getElementById('tipoPago').value = ''; 
        //document.getElementById('diferencia_dias').value = ''; // Limpiar la diferencia de días
        //document.getElementById('monto').value = ''; // Limpiar el monto
    });

    
</script>

<style>
  .select2-container {
  max-width: 350px; /* Ajusta este valor según tus necesidades */
}
</style>