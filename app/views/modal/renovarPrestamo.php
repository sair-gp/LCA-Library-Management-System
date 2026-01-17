<!-- Modal Renovar -->
<div class="modal fade" id="renovar" tabindex="-1" aria-labelledby="renovarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renovarLabel">Renovar Préstamo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRenovar" method="POST" action="app/controller/prestamos/c_renovar.php">
                    <div class="mb-3">

                        <label for="idPrestamoRenovar" class="form-label">ID del Préstamo</label>
                        <input type="text" class="form-control" id="idPrestamoRenovar" name="idPrestamo" readonly>


                    </div>
                    <div class="mb-3">
                        <label for="nuevaFecha" class="form-label">Nueva Fecha de Devolución</label>
                        <input type="date" class="form-control" id="nuevaFecha" name="nuevaFecha" required>

                        <input type="hidden" id="spanAdvertenciaFecha" value="valor">

                    </div>
                    <button type="submit" class="btn btn-primary" id="btnRenovarPrestamo">Renovar</button>
                </form>
            </div>
        </div>
    </div>
</div>



<?php



?>


<script>
   
   const fechaActual = new Date();
console.log("Fecha actual:", fechaActual);

const spanAdvertenciaFecha = document.getElementById("spanAdvertenciaFecha");
const nuevaFechaInput = document.getElementById("nuevaFecha");
const btnRenovarPrestamo = document.getElementById("btnRenovarPrestamo");

let inicio;
let fechaFormateada;
let fin;

function parseFecha(fechaStr) {
    // Intenta detectar el formato correcto y convertirlo a yyyy-MM-dd
    const partes = fechaStr.split("-");
    if (partes.length === 3) {
        return new Date(`${partes[0]}-${partes[1]}-${partes[2]}`); // yyyy-MM-dd
    }
    return new Date(fechaStr); // Formato alternativo
}

function calcularNuevaFecha(fecha, unidades, cantidad) {
    let nuevaFecha = new Date(fecha);
    switch (unidades) {
        case "días":
        case "dias":
            nuevaFecha.setDate(nuevaFecha.getDate() + cantidad);
            break;
        case "semanas":
            nuevaFecha.setDate(nuevaFecha.getDate() + cantidad * 7);
            break;
        case "meses":
            nuevaFecha.setMonth(nuevaFecha.getMonth() + cantidad);
            break;
        default:
            console.warn("Unidad de tiempo no válida:", unidades);
    }
    return nuevaFecha;
}

function setRenovarId(event, id = "", cedula = "") {
    if (id) {
        document.getElementById("idPrestamoRenovar").value = id;
    }

    const button = event.target;
    const row = button.closest("tr");
    const fechaInicioStr = row.querySelector("td:nth-child(5)").innerText.trim();
    const fechaFinStr = row.querySelector("td:nth-child(6)").innerText.trim();

    inicio = parseFecha(fechaInicioStr);
    fin = parseFecha(fechaFinStr);

    console.log("Fecha inicio:", inicio);
    console.log("Fecha fin antes del ajuste:", fin);

    if (isNaN(fin.getTime())) {
        console.error("Error: La fecha de fin no se pudo interpretar correctamente.");
        return;
    }

    if (fin.getTime() < fechaActual.getTime()) {
        console.log("Préstamo vencido");
    }

    // Obtener datos de la base de datos cada vez que se presiona el botón
    fetch("public/js/ajax/obtener_regla_circulacion.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `cedula=${encodeURIComponent(cedula)}`
    })
        .then(response => response.json())
        .then(data => {
            let unidades = data.unidades.toLowerCase();
            let periodoRenovaciones = parseInt(data.periodo_renovaciones, 10);

            const nuevaFecha = calcularNuevaFecha(fin, unidades, periodoRenovaciones);

            console.log("Nueva fecha calculada:", nuevaFecha);

            // Formatea la nueva fecha a 'yyyy-MM-dd'
            const anio = nuevaFecha.getFullYear();
            const mes = String(nuevaFecha.getMonth() + 1).padStart(2, "0");
            const dia = String(nuevaFecha.getDate()).padStart(2, "0");
            fechaFormateada = `${anio}-${mes}-${dia}`;

            crearSpan(
                spanAdvertenciaFecha,
                spanAdvertenciaFecha.nextElementSibling,
                `El usuario puede solicitar una renovación de un tiempo máximo de ${periodoRenovaciones} ${unidades}.`,
                "green",
                false,
                true
            );

            nuevaFechaInput.value = fechaFormateada;
        })
        .catch(error => console.error("Error obteniendo datos: ", error));
}

nuevaFechaInput.addEventListener("input", function () {
    const fechaIngresada = nuevaFechaInput.value ? new Date(nuevaFechaInput.value) : null;
    console.log("Fecha ingresada:", fechaIngresada);

    if (!fechaIngresada || fechaIngresada.getTime() < fin.getTime() || fechaIngresada.getTime() > new Date(fechaFormateada).getTime()) {
        crearSpan(
            nuevaFechaInput,
            nuevaFechaInput.nextElementSibling,
            "La fecha no puede ser menor a la fecha de inicio ni mayor a la fecha máxima permitida.",
            "red",
            ""
        );
        btnRenovarPrestamo.disabled = true;
        nuevaFechaInput.value = fechaFormateada;
    } else {
        crearSpan(
            nuevaFechaInput,
            nuevaFechaInput.nextElementSibling,
            "",
            "",
            true
        );
        btnRenovarPrestamo.disabled = false;
    }
});




</script>