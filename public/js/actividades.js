const fechaInicioActividad = document.querySelector(".nuevaFechaInicioActividad");
    const fechaFinActividad = document.querySelector(".nuevaFechaFinActividad");
    const btnReprogramar = document.querySelector(".btnReprogramar");
    console.log(fechaInicioActividad.value)


    //No recuerdo pa que es esta vaina
    document.addEventListener('DOMContentLoaded', () => {
        const modalAccion = document.getElementById('modalAccion');
        modalAccion.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const activityId = button.getAttribute('data-id');
            // Puedes usar este ID para rellenar datos dinámicos en el modal.
        });
    });

    //pasarle el id de actividad al modal para su posterior uso en un controlador
    $(document).on('click', '.modalbtnAccion', function() {
        $tr = $(this).closest('tr');

        var datos = $tr.children("td").map(function() {
            return $(this).text();
        });

        // Asignar el valor al input con id 'inputISBN'
        $('.idActividad').val(datos[0]);


        const estado = datos[9];
        const isFinalizado = estado === "Finalizado";
        const isCancelado = estado === "Cancelado";
        
        // Deshabilitar botones
        $("#suspender-tab").prop("disabled", isFinalizado || isCancelado);
        $("#reprogramar-tab").prop("disabled", isFinalizado);
        
        // Tabs activos
        $("#suspender-tab").toggleClass("active", !isFinalizado && !isCancelado);
        $("#observacion-tab").toggleClass("active", isFinalizado);
        $("#reprogramar-tab").toggleClass("active", isCancelado);
        
        // Contenidos modales visibles
        $("#suspender").toggleClass("show active", !isFinalizado && !isCancelado);
        $("#observacion").toggleClass("show active", isFinalizado);
        $("#reprogramar").toggleClass("show active", isCancelado);


        //esto ta mal
        //$('.idActividad').val(datos[9]);
    });










//Validar que los textarea no estén vacíos

const observacionText = document.getElementById("observacionText");
const btnObservacion = document.getElementById("btn-observacion");

observacionText.addEventListener("input", () => {
    if (observacionText.value !== ""){
        btnObservacion.removeAttribute("disabled");
    } else {
        btnObservacion.setAttribute("disabled", "");
    }
});

const reprogramacionText = document.getElementById("motivoSuspensionR");
const btnReprogramacion = document.getElementById("motivoSuspensionRBTN");

reprogramacionText.addEventListener("input", () => {
    if (reprogramacionText.value !== ""){
        btnReprogramacion.removeAttribute("disabled");
    } else {
        btnReprogramacion.setAttribute("disabled", "");
    }
});

const suspenderText = document.getElementById("motivoSuspension");
const btnSuspender = document.getElementById("motivoSuspensionBTN");

suspenderText.addEventListener("input", () => {
    if (suspenderText.value !== ""){
        btnSuspender.removeAttribute("disabled");
    } else {
        btnSuspender.setAttribute("disabled", "");
    }
});