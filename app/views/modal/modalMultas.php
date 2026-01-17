<!-- Modal para registrar multa -->
<div class="modal fade" id="modalRegistrarMulta" tabindex="-1" aria-labelledby="modalRegistrarMultaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarMultaLabel">Registrar Multa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formRegistrarMulta" action="app/controller/prestamos/c_multas.php" method="POST">
    <div class="modal-body">
        <!-- Campo oculto para el ID del préstamo -->
        <!--input type="hidden" name="prestamo_id" id="prestamo_id" value="1"--> <!-- Cambia el valor según sea necesario -->

        <!-- Input para el usuario -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <select name="usuario" class="form-control select2-usuario" style="width: 90%;" id="lector" required>
            </select>
        </div>

        <!-- Input para el prestamo de la multa -->
        <div class="input-group mb-3" id="idPrestamoDiv" style="display: none;">
            <span class="input-group-text"><i class="bi bi-receipt"></i></span>
            <select name="idPrestamo" class="form-control select2-prestamo" style="width: 90%;" id="prestamo" required>
            </select>
        </div>

        <!-- Select para el motivo -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
            <select class="form-control" id="motivo" name="motivo" required onchange="calcularMonto()">
                <option value="" disabled selected>Seleccione un motivo</option>
                <option value="perdida">Pérdida de material</option>
                <option value="danio">Daño al material</option>
            </select>
        </div>

        <!-- Input para el monto (solo lectura) -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-cash-coin"></i></span>
            <input type="number" class="form-control" id="monto" name="monto" placeholder="Monto" readonly required>
            <span class="input-group-text">Bs.</span> <!-- Símbolo monetario -->
        </div>

        <!-- Input para la fecha -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d'); ?>" readonly required>
        </div>

        <!-- Select para el estado -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-list-check"></i></span>
            <input type="text" class="form-control" name="estado" id="estado" value="pendiente" readonly required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" id="regMultaBtn" class="btn btn-primary" disabled>Registrar</button>
    </div>
</form>
        </div>
    </div>
</div>

<!-- Modal para pagar multa -->
<div class="modal fade" id="modalPagar" tabindex="-1" aria-labelledby="modalPagarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagarLabel">Pagar Multa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formPagarMulta" action="app/controller/prestamos/c_pagar_multas.php" method="POST">
                <div class="modal-body">
                    <p><strong>Usuario:</strong> <span id="modalUsuario"></span></p>
                    <p><strong>Monto:</strong> <span id="modalMonto"></span></p>
                    <p><strong>Seleccione el tipo de pago:</strong></p>
                    <input type="hidden" name="monto" id="montoInput">
                    <input type="hidden" name="idMulta" id="idMulta">
                    <select id="tipoPago" name="tipoPago" class="form-control" required>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="modal-footer">
          
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
  


$(document).ready(function () {
  // Inicializar el select2 para el lector
  $("#lector").select2({
    dropdownParent: $("#modalRegistrarMulta"),
    placeholder: "Cédula del lector",
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
      url: "public/js/ajax/validarInputsRegistroPrestamo.php",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          cedulaLector: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: data.results.map(function (user) {
            return {
              id: user.id,
              text: user.text,
            };
          }),
        };
      },
      cache: true,
    },
    escapeMarkup: function (markup) {
      return markup;
    },
  });

  // Escuchar el evento change del select de lector
  $('#lector').on('change', function () {
    if ($(this).val()) {
      // Si hay un valor seleccionado, mostrar el select de préstamos
      $('#idPrestamoDiv').show();
      // Habilitar el botón de registro
      $('#regMultaBtn').prop('disabled', false);
    } else {
      // Si no hay valor seleccionado, ocultar el select de préstamos
      $('#idPrestamoDiv').hide();
      // Deshabilitar el botón de registro
      $('#regMultaBtn').prop('disabled', true);
    }
  });

  // Inicializar el select2 para el préstamo
  $("#prestamo").select2({
    dropdownParent: $("#modalRegistrarMulta"),
    placeholder: "ID del préstamo",
    minimumInputLength: 1,
    language: {
      inputTooShort: function () {
        return "Por favor, ingrese al menos 1 carácter";
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
          terminoPrestamo: params.term,
          cedulaLector: $('#lector').val(), // Obtener el valor del select de lector
        };
      },
      processResults: function (data) {
        if (data.error) {
          console.error(data.error);
          return { results: [] };
        }
        return {
          results: data.map(function (user) {
            return {
              id: user.id,
              text: user.text,
            };
          }),
        };
      },
      cache: true,
    },
    escapeMarkup: function (markup) {
      return markup;
    },
  });
});



// Escuchar el evento 'hidden.bs.modal' del modal
document.getElementById('modalPagar').addEventListener('hidden.bs.modal', function () {
        // Resetear el formulario
        document.getElementById('formRegistrarMulta').reset();

        // Resetear campos específicos si es necesario
        document.getElementById('idMulta').value = ''; 
        document.getElementById('monto').value = ''; 
        //document.getElementById('tipoPago').value = ''; 
        //document.getElementById('diferencia_dias').value = ''; // Limpiar la diferencia de días
        //document.getElementById('monto').value = ''; // Limpiar el monto
    });



//javascript para el backend

document.getElementById('formPagarMulta').addEventListener('submit', function (e) {
    e.preventDefault(); // Evitar el envío tradicional del formulario

    // Obtener los datos del formulario
    const formData = new FormData(this);

    // Depuración: Verificar los datos del formulario
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    // Enviar los datos al backend usando Fetch API
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data); // Depuración
        if (data.success) {
            // Si el pago fue exitoso, actualizar el estado de la multa
            var idMulta = "estadoMulta" + document.getElementById("idMulta").value;
            const divEstado = document.getElementById(idMulta);

            divEstado.classList = "";
            divEstado.classList = "estado pagada";
            divEstado.innerHTML = "Pagada"

            var pagarId = "pagar" + document.getElementById("idMulta").value;
            document.getElementById(pagarId).style.display = "none";

            // Cerrar el modal usando Bootstrap
            const modalElement = document.getElementById('modalPagar');
            const modal = bootstrap.Modal.getInstance(modalElement);

            if (modal) {
                modal.hide(); // Cerrar el modal

                // Eliminar el backdrop manualmente si es necesario
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove(); // Eliminar el backdrop
                }

                // Habilitar el desplazamiento del cuerpo (body)
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0'; // Restaurar el padding si se modificó
            } else {
                console.error('No se pudo obtener la instancia del modal');
            }
        } else {
            // Mostrar un mensaje de error si algo salió mal
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al procesar la solicitud.');
    });
});
</script>