<!-- Button trigger modal -->


<!-- Modal -->
<div class="modal fade" id="devolver" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">DEVOLUCION DE PRESTAMO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="app/controller/prestamos/c_devolver_prestamo.php" method="POST">
                    <h2>Confirmar devolución de préstamo.</h2>
                    <div class="col-12">
                        <input type="hidden" name="idPrestamo" id="idPrestamo" class="form-control">
                        <input type="hidden" name="estadoPrestamo" id="estadoPrestamo" class="form-control">
                    </div>
                    <input type="hidden" name="accion" value="devolver">


                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Devolver</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>

            </div>


            </form>
        </div>
    </div>
</div>

<!-- Modal para registrar multa -->
<div class="modal fade" id="modalRegistrarMulta" tabindex="-1" aria-labelledby="modalRegistrarMultaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarMultaLabel">Registrar Multa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formRegistrarMulta" method="POST">
    <div class="modal-body">
        <!-- Campo oculto para el ID del préstamo -->
        <input type="hidden" name="prestamo_id" id="prestamo_id" value=""> <!-- Cambia el valor según sea necesario -->
        <input type="hidden" name="diferencia_dias" id="diferencia_dias" value=""> <!-- Cambia el valor según sea necesario -->

        <!-- Input para el usuario -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="hidden" name="lector" id="lectorCedula" readonly>
            <input type="text" id="lectorNombre" style="width: 90%;" readonly>
        </div>

        <!-- Select para el motivo -->
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
            <select class="form-control" id="motivo" name="motivo" required onchange="calcularMonto()">
                <option value="" disabled selected>Seleccione un motivo</option>
                <option value="1">Retraso</option>
                <option value="2">Pérdida de material</option>
                <option value="3">Daño al material</option>
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
        <button type="submit" id="regMultaBtn" class="btn btn-primary">Registrar</button>
    </div>
</form>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    // Variables globales
    var dolarToday = <?= $_SESSION["dolarBCV"]; ?>;
    var montoPerdida = <?= $_SESSION["monto_por_perdida_material"]; ?>;
    var montoRetraso = <?= $_SESSION["monto_por_dia_retraso"]; ?>;
    var montoDanio = <?= $_SESSION["monto_por_danio"]; ?>;
    var diferenciaDias = document.getElementById("diferencia_dias");

    // Verificar el valor de diferenciaDias
    console.log("Valor inicial de diferenciaDias:", diferenciaDias.value);

    // Función para calcular el monto
    function calcularMonto() {
        const motivo = document.getElementById('motivo').value;
        const montoInput = document.getElementById('monto');

        // Verificar el valor de diferenciaDias en el momento del cálculo
        console.log("Valor de diferenciaDias al calcular:", diferenciaDias.value);

        // Convertir diferenciaDias.value a número
        const dias = parseFloat(diferenciaDias.value) || 0;
        const multiplicarPorRetraso = dias > 0 ? dolarToday * (montoRetraso * dias) : 0;
        console.log("monto retraso normal " + montoRetraso + " multiplicado " + montoRetraso * dias);
        // Definir los montos según el motivo
        const montos = {
            '2': ((dolarToday * montoPerdida) + multiplicarPorRetraso).toFixed(2), // Monto por pérdida de material
            '3': ((dolarToday * montoDanio) + multiplicarPorRetraso).toFixed(2),     // Monto por daño al material
            '1': (dolarToday * (montoRetraso * dias)).toFixed(2), // Monto por retraso
        };

        // Asignar el monto correspondiente
        montoInput.value = montos[motivo] || 0;
        console.log(montoInput.value);
        console.log(dolarToday * montoPerdida);
        console.log(dolarToday * montoDanio);
        console.log(dolarToday + "*(" + montoDanio + "*" + multiplicarPorRetraso + ") :" + dolarToday * montoDanio * multiplicarPorRetraso);
    }

    // Asignar la función calcularMonto a un evento (por ejemplo, al cambiar el motivo)
    document.getElementById('motivo').addEventListener('change', calcularMonto);
});



document.getElementById('formRegistrarMulta').addEventListener('submit', function (e) {
    e.preventDefault(); // Evitar el envío tradicional del formulario

    const formData = new FormData(this);

    fetch('app/controller/prestamos/c_multas.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            //alert(data.message); // Mostrar mensaje de éxito
            if (data.redirect) {
                // Redirigir a la URL especificada
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message); // Mostrar mensaje de error
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});



//resetear datos del modal al cerrarlo

// Escuchar el evento 'hidden.bs.modal' del modal
document.getElementById('modalRegistrarMulta').addEventListener('hidden.bs.modal', function () {
        // Resetear el formulario
        document.getElementById('formRegistrarMulta').reset();

        // Resetear campos específicos si es necesario
        document.getElementById('lectorNombre').value = ''; // Limpiar el nombre del lector
        document.getElementById('lectorCedula').value = ''; // Limpiar la cédula del lector
        document.getElementById('prestamo_id').value = ''; // Limpiar el ID del préstamo
        document.getElementById('diferencia_dias').value = ''; // Limpiar la diferencia de días
        document.getElementById('monto').value = ''; // Limpiar el monto
    });


</script>







<style>
    /* Estilos generales para los grupos de input */
.input-group {
    display: flex;
    align-items: center; /* Alinea verticalmente los elementos */
    margin-bottom: 15px;
}

/* Estilos para el span (input-group-text) */
.input-group-text {
    background-color: #e9ecef;
    border: 1px solid #ced4da;
    color: #495057;
    padding: 0.375rem 0.75rem;
    height: 38px; /* Fijamos la altura */
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box; /* Asegura que el padding no afecte la altura */
}

/* Estilos para los inputs y selects */
.form-control {
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    color: #495057;
    background-color: #fff;
    height: 38px; /* Fijamos la altura */
    box-sizing: border-box; /* Asegura que el padding no afecte la altura */
    flex-grow: 1; /* Ocupa el espacio restante */
    margin-left: -1px; /* Solapa el borde con el span */
}

/* Estilos específicos para el input de nombre del lector */
#lectorNombre {
    flex-grow: 1; /* Ocupa el espacio restante */
}



/* Ajustes para el input de fecha */
input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    color: #495057;
    height: 38px; /* Fijamos la altura */
    box-sizing: border-box; /* Asegura que el padding no afecte la altura */
}

/* Ajustes para el input de monto */
#monto {
    text-align: right;
}

/* Estilos específicos para el grupo del nombre del lector */
.input-group #lectorNombre {
    flex-grow: 1; /* Ocupa el espacio restante */
    height: 38px; /* Misma altura que el span */
    border: 1px solid #ced4da; /* Borde suave */
    border-left: none; /* Elimina el borde izquierdo para que no se solape con el span */
    border-radius: 0 5px 5px 0; /* Bordes redondeados solo a la derecha */
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    color: #495057;
    background-color: #fff;
    box-sizing: border-box; /* Asegura que el padding no afecte la altura */
    outline: none; /* Elimina el resaltado al hacer focus */
}

/* Estilos para el span del nombre del lector */
.input-group #lectorNombre + .input-group-text {
    border-right: none; /* Elimina el borde derecho para que no se solape con el input */
}

/* Estilos para el input cuando está en foco */
.input-group #lectorNombre:focus {
    border-color: #ced4da; /* Mantén el mismo color de borde al hacer focus */
    box-shadow: none; /* Elimina cualquier sombra al hacer focus */
}

.input-group #lectorNombre {
    border: 1px solid #ced4da !important;
    outline: none !important;
    box-shadow: none !important;
}

</style>